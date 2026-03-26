@extends('layouts.admin')

@section('title', 'Workflow #' . $workflowId)

@push('head')
    <style>
        body { background-color: #ffffff !important; }
    </style>
@endpush

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @livewire('admin.autofill.autofill-workflow-detail', ['workflowId' => $workflowId])
    </div>
@endsection
