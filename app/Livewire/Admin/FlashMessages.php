<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;

class FlashMessages extends Component
{
    /**
     * @var array<int, array{id: string, type: string, message: string}>
     */
    public array $messages = [];

    /**
     * Mount component and check for session flash messages.
     */
    public function mount(): void
    {
        // Charger les messages flash existants en session (pour compatibilité)
        $this->loadSessionMessages();
    }

    /**
     * Listen for flash-message event from other Livewire components.
     */
    #[On('flash-message')]
    public function addMessage(string $type, string $message): void
    {
        $id = uniqid('msg_', true);

        $this->messages[] = [
            'id' => $id,
            'type' => $type,
            'message' => $message,
        ];

        // Auto-dismiss après 5 secondes
        $this->dispatch('message-added', id: $id);
    }

    /**
     * Remove a message from the list.
     */
    public function removeMessage(string $id): void
    {
        $this->messages = array_filter($this->messages, fn ($msg) => $msg['id'] !== $id);
    }

    /**
     * Load session flash messages (pour compatibilité avec les redirections).
     */
    private function loadSessionMessages(): void
    {
        $types = ['success', 'error', 'warning', 'info'];

        foreach ($types as $type) {
            if (session()->has($type)) {
                $this->messages[] = [
                    'id' => uniqid('msg_', true),
                    'type' => $type,
                    'message' => session($type),
                ];
            }
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.flash-messages');
    }
}
