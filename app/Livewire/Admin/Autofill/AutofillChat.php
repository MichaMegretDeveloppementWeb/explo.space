<?php

namespace App\Livewire\Admin\Autofill;

use App\Enums\AutofillMessageRole;
use App\Enums\AutofillMessageType;
use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class AutofillChat extends Component
{
    use AuthorizesRequests;

    /** @var Collection<int, \App\Models\AutofillMessage> */
    public Collection $messages;

    public string $query = '';

    public string $provider = '';

    public int $quantity = 10;

    /** @var array<int> */
    public array $selectedItems = [];

    /** @var array<int, array<int>> */
    public array $selectedImages = [];

    public ?int $activeWorkflowId = null;

    public bool $isPolling = false;

    public bool $blockedByOtherAdmin = false;

    public string $blockedByAdminName = '';

    public bool $showInterruptConfirm = false;

    public bool $showEmptySelectionConfirm = false;

    public function mount(): void
    {
        $this->provider = config('autofill.default_provider', 'openai');
        $this->quantity = config('autofill.default_quantity', 10);
        $this->messages = collect();

        $this->loadCurrentWorkflow();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:3', 'max:500'],
            'provider' => ['required', 'string', 'in:openai,anthropic,gemini'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.config('autofill.max_quantity', 50)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Veuillez saisir une requête.',
            'query.min' => 'La requête doit contenir au moins 3 caractères.',
            'query.max' => 'La requête ne doit pas dépasser 500 caractères.',
            'provider.required' => 'Veuillez sélectionner un fournisseur.',
            'provider.in' => 'Fournisseur invalide.',
            'quantity.required' => 'Veuillez indiquer un nombre.',
            'quantity.min' => 'Le nombre minimum est 1.',
            'quantity.max' => 'Le nombre maximum est '.config('autofill.max_quantity', 50).'.',
        ];
    }

    public function startWorkflow(): void
    {
        $this->validate();

        $service = app(AutofillPipelineService::class);

        // Check if any workflow currently occupies a slot (active or paused)
        $check = $service->checkActiveWorkflow();

        if ($check['active']) {
            /** @var AutofillWorkflow $existingWorkflow */
            $existingWorkflow = $check['workflow'];

            if ($existingWorkflow->admin_id === auth()->id()) {
                // Same admin — ask to abandon and start new
                $this->showInterruptConfirm = true;

                return;
            }

            // Different admin — block
            $this->blockedByOtherAdmin = true;
            $this->blockedByAdminName = $existingWorkflow->admin->name ?? 'un autre administrateur';

            return;
        }

        $this->dispatchNewWorkflow($service);
    }

    public function confirmInterrupt(): void
    {
        $this->showInterruptConfirm = false;

        $service = app(AutofillPipelineService::class);

        if ($this->activeWorkflowId) {
            $workflow = AutofillWorkflow::find($this->activeWorkflowId);

            if ($workflow && $workflow->isCurrent()) {
                $service->abandon($workflow);
            }
        }

        $this->dispatchNewWorkflow($service);
    }

    public function cancelInterrupt(): void
    {
        $this->showInterruptConfirm = false;
    }

    public function submitSelection(): void
    {
        if (empty($this->selectedItems)) {
            $this->showEmptySelectionConfirm = true;

            return;
        }

        $this->processSelectionConfirmed();
    }

    public function confirmEmptySelection(): void
    {
        $this->showEmptySelectionConfirm = false;
        $this->processSelectionConfirmed();
    }

    public function cancelEmptySelection(): void
    {
        $this->showEmptySelectionConfirm = false;
    }

    public function submitImageSelection(): void
    {
        if (! $this->activeWorkflowId) {
            return;
        }

        $workflow = AutofillWorkflow::find($this->activeWorkflowId);

        if (! $workflow || $workflow->status !== AutofillWorkflowStatus::AwaitingImages) {
            return;
        }

        $this->authorize('manage', $workflow);

        $service = app(AutofillPipelineService::class);

        // Add user message
        $service->createMessage($workflow, AutofillMessageType::Text, AutofillMessageRole::User, [
            'text' => 'Images sélectionnées.',
        ]);

        $service->processImageSelection($workflow, $this->selectedImages);

        $this->selectedImages = [];
        $this->refreshMessages();
    }

    public function abandonWorkflow(): void
    {
        if (! $this->activeWorkflowId) {
            return;
        }

        $workflow = AutofillWorkflow::find($this->activeWorkflowId);

        if (! $workflow) {
            $this->clearWorkflowState();

            return;
        }

        $this->authorize('manage', $workflow);

        app(AutofillPipelineService::class)->abandon($workflow);

        $this->clearWorkflowState();
    }

    public function resumeWorkflow(): void
    {
        if (! $this->activeWorkflowId) {
            return;
        }

        $workflow = AutofillWorkflow::find($this->activeWorkflowId);

        if (! $workflow || ! $workflow->isPaused()) {
            return;
        }

        $this->authorize('manage', $workflow);

        app(AutofillPipelineService::class)->resume($workflow);

        $this->isPolling = true;
        $this->refreshMessages();
    }

    public function refreshMessages(): void
    {
        if (! $this->activeWorkflowId) {
            $this->isPolling = false;

            return;
        }

        $workflow = AutofillWorkflow::find($this->activeWorkflowId);

        if (! $workflow) {
            $this->clearWorkflowState();

            return;
        }

        $this->messages = $workflow->messages()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        // Determine polling based on state
        if ($workflow->isDismissed()) {
            // Completed or abandoned — stop polling and free the input
            $this->isPolling = false;
            $this->activeWorkflowId = null;

            return;
        }

        if ($workflow->isPaused()) {
            // Paused (error) — stop polling
            $this->isPolling = false;

            return;
        }

        // Active — poll unless awaiting user input
        $this->isPolling = ! $workflow->status->isAwaiting();
    }

    /**
     * @return array<string, string>
     */
    public function getAvailableProvidersProperty(): array
    {
        return [
            'openai' => 'OpenAI',
            'anthropic' => 'Anthropic',
            'gemini' => 'Google Gemini',
        ];
    }

    public function getActiveWorkflowProperty(): ?AutofillWorkflow
    {
        if (! $this->activeWorkflowId) {
            return null;
        }

        return AutofillWorkflow::find($this->activeWorkflowId);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.autofill.autofill-chat');
    }

    /**
     * Load the admin's current workflow (active or paused) on mount.
     */
    private function loadCurrentWorkflow(): void
    {
        // First check for any current workflow globally (active or paused)
        $currentWorkflow = AutofillWorkflow::current()->first();

        if (! $currentWorkflow) {
            return;
        }

        $this->activeWorkflowId = $currentWorkflow->id;

        if ($currentWorkflow->admin_id !== auth()->id()) {
            $this->blockedByOtherAdmin = true;
            $this->blockedByAdminName = $currentWorkflow->admin->name ?? 'un autre administrateur';
        }

        $this->messages = $currentWorkflow->messages()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        // Polling only for active (not paused) workflows that aren't awaiting input
        $this->isPolling = $currentWorkflow->isActive() && ! $currentWorkflow->status->isAwaiting();
    }

    private function dispatchNewWorkflow(AutofillPipelineService $service): void
    {
        $workflow = $service->start([
            'query' => $this->query,
            'provider' => $this->provider,
            'requested_quantity' => $this->quantity,
            'admin_id' => auth()->id(),
        ]);

        $this->activeWorkflowId = $workflow->id;
        $this->isPolling = true;
        $this->query = '';
        $this->blockedByOtherAdmin = false;

        $this->messages = $workflow->messages()
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    private function processSelectionConfirmed(): void
    {
        if (! $this->activeWorkflowId) {
            return;
        }

        $workflow = AutofillWorkflow::find($this->activeWorkflowId);

        if (! $workflow || $workflow->status !== AutofillWorkflowStatus::AwaitingSelection) {
            return;
        }

        $this->authorize('manage', $workflow);

        $service = app(AutofillPipelineService::class);

        // Add user message
        $count = count($this->selectedItems);
        $service->createMessage($workflow, AutofillMessageType::Text, AutofillMessageRole::User, [
            'text' => $count > 0 ? "{$count} lieu(x) sélectionné(s)." : 'Aucun lieu sélectionné.',
        ]);

        $service->processSelection($workflow, $this->selectedItems);

        $this->selectedItems = [];
        $this->isPolling = $count > 0;
        $this->refreshMessages();
    }

    private function clearWorkflowState(): void
    {
        $this->activeWorkflowId = null;
        $this->isPolling = false;
        $this->messages = collect();
    }
}
