<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsShowController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the settings page.
     *
     * Authorization via Policy (tous les admins peuvent accéder).
     */
    public function show(): View|RedirectResponse
    {
        // Vérifier l'autorisation via la Policy
        $this->authorize('viewSettings', auth()->user());

        return view('admin.settings.index');
    }
}
