<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->whereKey($id)->first();

        if (! $notification) {
            return null;
        }

        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        if ($url) {
            return $this->redirect($url, navigate: false);
        }

        return null;
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.notification-bell', [
            'notifications' => $user->notifications()->latest()->limit(10)->get(),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }
}
