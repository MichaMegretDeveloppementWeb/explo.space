<?php

namespace App\Livewire\Admin\Autofill;

use App\Enums\AutofillWorkflowStatus;
use App\Models\AutofillItem;
use App\Models\AutofillStepLog;
use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillPipelineService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

class AutofillWorkflowDetail extends Component
{
    use AuthorizesRequests;

    public int $workflowId;

    public bool $isPolling = false;

    public bool $showIoModal = false;

    public string $ioFilterStep = '';

    public string $ioFilterItem = '';

    /** @var array<int> */
    public array $expandedItems = [];

    public function mount(int $workflowId): void
    {
        $this->workflowId = $workflowId;

        $workflow = AutofillWorkflow::findOrFail($workflowId);
        $this->authorize('view', $workflow);

        $this->isPolling = $workflow->isActive() && ! $workflow->status->isAwaiting();
    }

    public function refreshWorkflow(): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);

        if (! $workflow || $workflow->isDismissed()) {
            $this->isPolling = false;

            return;
        }

        $this->isPolling = $workflow->isActive() && ! $workflow->status->isAwaiting();
    }

    public function toggleItem(int $itemId): void
    {
        if (in_array($itemId, $this->expandedItems, true)) {
            $this->expandedItems = array_values(array_diff($this->expandedItems, [$itemId]));
        } else {
            $this->expandedItems[] = $itemId;
        }
    }

    public function openIoModal(): void
    {
        $this->showIoModal = true;
        $this->ioFilterStep = '';
        $this->ioFilterItem = '';
    }

    public function closeIoModal(): void
    {
        $this->showIoModal = false;
    }

    public function abandonWorkflow(): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);

        if (! $workflow || $workflow->isDismissed()) {
            return;
        }

        app(AutofillPipelineService::class)->abandon($workflow);
        $this->isPolling = false;
    }

    public function resumeWorkflow(): void
    {
        $workflow = AutofillWorkflow::find($this->workflowId);

        if (! $workflow || ! $workflow->isPaused()) {
            return;
        }

        app(AutofillPipelineService::class)->resume($workflow);
        $this->isPolling = true;
    }

    public function getWorkflowProperty(): AutofillWorkflow
    {
        return AutofillWorkflow::with('admin:id,name')->findOrFail($this->workflowId);
    }

    /**
     * @return Collection<int, AutofillItem>
     */
    public function getItemsProperty(): Collection
    {
        return AutofillItem::query()
            ->where('workflow_id', $this->workflowId)
            ->with('place.photos')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, AutofillStepLog>
     */
    public function getStepLogsProperty(): Collection
    {
        $query = AutofillStepLog::query()
            ->where('workflow_id', $this->workflowId)
            ->with('item:id,name')
            ->orderBy('created_at');

        if ($this->ioFilterStep !== '') {
            $query->where('step', $this->ioFilterStep);
        }

        if ($this->ioFilterItem !== '') {
            $query->where('item_id', (int) $this->ioFilterItem);
        }

        return $query->get();
    }

    /**
     * @return array<string>
     */
    public function getAvailableStepsProperty(): array
    {
        return AutofillStepLog::query()
            ->where('workflow_id', $this->workflowId)
            ->distinct()
            ->pluck('step')
            ->toArray();
    }

    /**
     * @return Collection<int, AutofillItem>
     */
    public function getAvailableItemsProperty(): Collection
    {
        return AutofillItem::query()
            ->where('workflow_id', $this->workflowId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /** @return array{steps: list<string>, currentStep: int, isDismissed: bool, isPaused: bool, isAwaiting: bool} */
    public function getProgressProperty(): array
    {
        $workflow = $this->getWorkflowProperty();
        $steps = ['Découverte', 'Enrichissement', 'Images', 'Traduction', 'Enregistrement'];

        // currentStep = index of the NEXT step to run.
        // Steps before currentStep show a checkmark (completed).
        // The step AT currentStep shows: pulse (processing), red (paused), or gray (awaiting/future).
        $statusMap = [
            AutofillWorkflowStatus::Pending->value => 0,
            AutofillWorkflowStatus::Discovering->value => 0,
            AutofillWorkflowStatus::AwaitingSelection->value => 1,  // Discovery ✓, waiting before enrichment
            AutofillWorkflowStatus::Enriching->value => 1,
            AutofillWorkflowStatus::AwaitingImages->value => 3,     // Discovery ✓, Enrichissement ✓, Images ✓, waiting before translation
            AutofillWorkflowStatus::Translating->value => 3,
            AutofillWorkflowStatus::Saving->value => 4,
            AutofillWorkflowStatus::Completed->value => 5,
        ];

        $currentStep = $statusMap[$workflow->status->value] ?? 0;

        // For completed workflows, determine actual progress based on items
        if ($workflow->status === AutofillWorkflowStatus::Completed && $workflow->isDismissed()) {
            $items = $this->getItemsProperty();

            if ($items->isEmpty()) {
                $currentStep = 1; // Only discovery ran
            } elseif ($items->where('status', \App\Enums\AutofillItemStatus::Saved)->isEmpty()) {
                $currentStep = 1; // Items exist but none saved
            }
        }

        return [
            'steps' => $steps,
            'currentStep' => $currentStep,
            'isDismissed' => $workflow->isDismissed(),
            'isPaused' => $workflow->isPaused(),
            'isAwaiting' => $workflow->status->isAwaiting(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.autofill.autofill-workflow-detail');
    }
}
