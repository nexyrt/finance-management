<?php

namespace App\Livewire\Services;

use TallStackUi\Traits\Interactions;
use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Interactions;

    public ?int $serviceId = null;
    public ?string $serviceName = null;

    #[On('delete::service')]
    public function load(int $serviceId): void
    {
        $service = Service::select(['id', 'name'])->find($serviceId);
        if (!$service) return;

        $this->serviceId = $service->id;
        $this->serviceName = $service->name;
        $this->confirm();
    }

    #[Renderless]
    public function confirm(): void
    {
        $this->dialog()->question(
            __('pages.delete_service'),
            __('pages.confirm_delete_service', ['name' => $this->serviceName])
        )
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        Service::destroy($this->serviceId);

        $this->serviceId = null;
        $this->serviceName = null;

        $this->dispatch('service-deleted');
        $this->dialog()->success(__('common.success'), __('common.deleted_successfully'))->send();
    }

    public function render(): string
    {
        return '<div></div>';
    }
}
