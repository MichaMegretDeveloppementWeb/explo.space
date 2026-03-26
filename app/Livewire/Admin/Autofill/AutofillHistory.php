<?php

namespace App\Livewire\Admin\Autofill;

use App\Models\AutofillWorkflow;
use App\Services\Admin\Autofill\AutofillCleanupService;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class AutofillHistory extends Component
{
    use WithPagination;

    public function deleteWorkflow(int $workflowId): void
    {
        $workflow = AutofillWorkflow::find($workflowId);

        if (! $workflow) {
            return;
        }

        $deleted = app(AutofillCleanupService::class)->deleteWorkflow($workflow);

        if (! $deleted) {
            session()->flash('error', 'Impossible de supprimer un workflow actif.');

            return;
        }

        session()->flash('success', 'Workflow supprimé.');
    }

    /** @return LengthAwarePaginator<int, AutofillWorkflow> */
    public function getWorkflowsProperty(): LengthAwarePaginator
    {
        return AutofillWorkflow::query()
            ->with('admin:id,name')
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.admin.autofill.autofill-history', [
            'workflows' => $this->getWorkflowsProperty(),
        ]);
    }
}
