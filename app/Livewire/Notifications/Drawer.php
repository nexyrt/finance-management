<?php

namespace App\Livewire\Notifications;

use App\Models\AppNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Drawer extends Component
{
    public bool $slide = false;
    public int $page = 1;
    public int $perPage = 20;

    public function render(): View
    {
        return view('livewire.notifications.drawer');
    }

    #[On('open-notification-drawer')]
    public function open(): void
    {
        $this->page = 1;
        $this->slide = true;
        unset($this->notifications);
        unset($this->total);
        unset($this->unreadCount);
    }

    #[On('notification-created')]
    #[On('feedback-created')]
    #[On('feedback-responded')]
    #[On('invoice-created')]
    #[On('payment-created')]
    #[On('invoice-deleted')]
    #[On('payment-deleted')]
    public function refresh(): void
    {
        unset($this->notifications);
        unset($this->total);
        unset($this->unreadCount);
    }

    #[Computed]
    public function notifications(): Collection
    {
        return AppNotification::forUser(auth()->id())
            ->latest()
            ->limit($this->page * $this->perPage)
            ->get();
    }

    #[Computed]
    public function total(): int
    {
        return AppNotification::forUser(auth()->id())->count();
    }

    #[Computed]
    public function unreadCount(): int
    {
        return AppNotification::forUser(auth()->id())->unread()->count();
    }

    public function loadMore(): void
    {
        $this->page++;
        unset($this->notifications);
    }

    public function markAllAsRead(): void
    {
        AppNotification::forUser(auth()->id())
            ->unread()
            ->update(['read_at' => now()]);

        unset($this->notifications);
        unset($this->unreadCount);

        $this->dispatch('notification-read');
    }

    public function openNotification(int $id): void
    {
        $notification = AppNotification::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->slide = false;

            $url = $notification->data['url'] ?? null;
            if ($url) {
                $this->redirect($url);
                return;
            }

            unset($this->notifications);
            unset($this->unreadCount);

            $this->dispatch('notification-read');
        }
    }
}
