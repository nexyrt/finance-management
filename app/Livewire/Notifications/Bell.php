<?php

namespace App\Livewire\Notifications;

use App\Models\AppNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Bell extends Component
{
    public bool $dropdown = false;

    public function render(): View
    {
        return view('livewire.notifications.bell');
    }

    #[Computed]
    public function unreadCount(): int
    {
        return AppNotification::forUser(auth()->id())
            ->unread()
            ->count();
    }

    #[Computed]
    public function notifications(): Collection
    {
        return AppNotification::forUser(auth()->id())
            ->recent()
            ->limit(10)
            ->get();
    }

    #[On('notification-created')]
    #[On('feedback-created')]
    #[On('feedback-responded')]
    public function refresh(): void
    {
        unset($this->unreadCount);
        unset($this->notifications);
    }

    public function markAsRead(int $id): void
    {
        $notification = AppNotification::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            unset($this->unreadCount);
            unset($this->notifications);
        }
    }

    public function markAllAsRead(): void
    {
        AppNotification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        unset($this->unreadCount);
        unset($this->notifications);
    }

    public function openNotification(int $id): void
    {
        $notification = AppNotification::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();

            // If notification has a URL in data, redirect to it
            $url = $notification->data['url'] ?? null;
            if ($url) {
                $this->redirect($url);
            }

            unset($this->unreadCount);
            unset($this->notifications);
        }
    }

    public function toggleDropdown(): void
    {
        $this->dropdown = !$this->dropdown;
    }
}
