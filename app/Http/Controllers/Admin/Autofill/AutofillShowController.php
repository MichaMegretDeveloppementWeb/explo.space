<?php

namespace App\Http\Controllers\Admin\Autofill;

use App\Http\Controllers\Controller;
use App\Models\AutofillWorkflow;
use Illuminate\View\View;

class AutofillShowController extends Controller
{
    public function __invoke(int $workflow): View
    {
        $workflowModel = AutofillWorkflow::findOrFail($workflow);

        return view('admin.autofill.show', [
            'workflowId' => $workflowModel->id,
        ]);
    }
}
