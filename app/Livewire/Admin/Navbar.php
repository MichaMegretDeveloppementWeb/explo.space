<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;

class Navbar extends Component
{
    public string $userName = '';

    public string $userRole = '';

    public string $userInitial = '';

    /**
     * Mount component with current user data.
     */
    public function mount(): void
    {
        $this->loadUserData();
    }

    /**
     * Reload user data when profile is updated.
     */
    #[On('profile:updated')]
    public function refreshUserData(): void
    {
        $this->loadUserData();
    }

    /**
     * Load current user data.
     */
    private function loadUserData(): void
    {
        $user = auth()->user();
        $this->userName = $user->name;
        $this->userRole = ucfirst($user->role);
        $this->userInitial = substr($user->name, 0, 1);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.navbar');
    }
}
