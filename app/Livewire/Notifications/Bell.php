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
    public bool $allNotificationsSlide = false;
    public int $allNotificationsPage = 1;
    public int $perPage = 20;

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

    #[Computed]
    public function allNotifications(): Collection
    {
        return AppNotification::forUser(auth()->id())
            ->latest()
            ->limit($this->allNotificationsPage * $this->perPage)
            ->get();
    }

    #[Computed]
    public function allNotificationsTotal(): int
    {
        return AppNotification::forUser(auth()->id())->count();
    }

    #[On('notification-created')]
    #[On('feedback-created')]
    #[On('feedback-responded')]
    #[On('invoice-created')]
    #[On('payment-created')]
    #[On('invoice-deleted')]
    #[On('payment-deleted')]
    #[On('notification-read')]
    public function refresh(): void
    {
        unset($this->unreadCount);
        unset($this->notifications);
        unset($this->allNotifications);
        unset($this->allNotificationsTotal);
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
            unset($this->allNotifications);
        }
    }

    public function markAllAsRead(): void
    {
        AppNotification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        unset($this->unreadCount);
        unset($this->notifications);
        unset($this->allNotifications);
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
            unset($this->allNotifications);
        }
    }

    public function openAllNotifications(): void
    {
        $this->allNotificationsPage = 1;
        $this->allNotificationsSlide = true;
        unset($this->allNotifications);
    }

    public function loadMore(): void
    {
        $this->allNotificationsPage++;
        unset($this->allNotifications);
    }
}
