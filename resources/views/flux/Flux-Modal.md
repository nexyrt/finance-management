Here's a comprehensive and AI-friendly guide to the **Flux UI Modal Component**, tailored for developers seeking clarity and practical examples.

---

# 📦 Flux UI Modal Component

The Flux UI Modal component offers a flexible and accessible way to display content in a layer above the main page. It's ideal for confirmations, alerts, forms, and more.

---

## 🚀 Installation

To get started with the Flux UI Modal component, follow the installation guide:

🔗 [Flux UI Installation Guide](https://fluxui.dev/docs/installation)

---

## ✅ Basic Usage

Here's how to implement a simple modal with a trigger button:

```blade
<flux:modal.trigger name="edit-profile">
    <flux:button>Edit Profile</flux:button>
</flux:modal.trigger>

<flux:modal name="edit-profile" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update Profile</flux:heading>
            <flux:text class="mt-2">Make changes to your personal details.</flux:text>
        </div>
        <flux:input label="Name" placeholder="Your name" />
        <flux:input label="Date of Birth" type="date" />
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" variant="primary">Save Changes</flux:button>
        </div>
    </div>
</flux:modal>
```



Ensure each modal has a unique `name` attribute, especially when used within loops, to prevent unexpected behavior. ([bladewindui.com][1])

---

## ⚙️ Control Methods

### Livewire Integration

You can control modals directly from your Livewire components:([Flux UI][2])

```php
class ShowPost extends \Livewire\Component
{
    public function delete()
    {
        // Open the modal
        Flux::modal('confirm')->show();

        // Close the modal
        Flux::modal('confirm')->close();

        // Close all modals
        Flux::modals()->close();
    }
}
```



Alternatively, within the Livewire component context:([Flux UI][2])

```php
$this->modal('confirm')->show();
$this->modal('confirm')->close();
```



### JavaScript Control

Using Alpine.js:

```html
<button x-on:click="$flux.modal('confirm').show()">Open Modal</button>
<button x-on:click="$flux.modal('confirm').close()">Close Modal</button>
<button x-on:click="$flux.modals().close()">Close All Modals</button>
```



Or with the global `window.Flux` object:([Flux UI][2])

```javascript
// Open the modal
Flux.modal('confirm').show();

// Close the modal
Flux.modal('confirm').close();

// Close all modals
Flux.modals().close();
```



---

## 🔄 Data Binding with Livewire

Bind a Livewire property to control the modal's visibility:([Flux UI][2])

```blade
<flux:modal wire:model.self="showConfirmModal">
    <!-- Modal content -->
</flux:modal>
```



In your Livewire component:([Flux UI][2])

```php
public $showConfirmModal = false;

public function delete()
{
    $this->showConfirmModal = true;
}
```



To open the modal from the frontend without a server roundtrip:([Flux UI][2])

```blade
<flux:button x-on:click="$wire.showConfirmModal = true">Delete Post</flux:button>
```



*Note:* The `.self` modifier is crucial to prevent nested elements from interfering with the modal's state. ([Flux UI][2])

---

## 🔔 Event Handling

### Close Events

Execute logic after the modal closes:([Flux UI][2])

```blade
<flux:modal @close="handleClose">
    <!-- Modal content -->
</flux:modal>
```



Alternatively, using Livewire or Alpine.js:

```blade
<flux:modal wire:close="handleClose">
    <!-- Modal content -->
</flux:modal>
```



### Cancel Events

Execute logic after the modal is canceled:([Flux UI][2])

```blade
<flux:modal @cancel="handleCancel">
    <!-- Modal content -->
</flux:modal>
```



Or:

```blade
<flux:modal wire:cancel="handleCancel">
    <!-- Modal content -->
</flux:modal>
```



---

## 🛡️ Dismiss Behavior

By default, clicking outside the modal will close it. To disable this behavior:([Flux UI][2])

```blade
<flux:modal :dismissible="false">
    <!-- Modal content -->
</flux:modal>
```



---

## 📚 Available Properties

* **`name`**: Unique identifier for the modal.
* **`:dismissible`**: Boolean to enable or disable closing the modal by clicking outside. Default is `true`.
* **`wire:model.self`**: Binds the modal's visibility to a Livewire property. The `.self` modifier ensures proper event handling.
* **`@close` / `wire:close`**: Event emitted when the modal closes.
* **`@cancel` / `wire:cancel`**: Event emitted when the modal is canceled.([Flux UI][2])

---

## 🔗 Additional Resources

* 📄 [Flux UI Modal Documentation](https://fluxui.dev/components/modal)
* 📘 [Flux UI Documentation](https://fluxui.dev/docs)
* ⚡ [Livewire Documentation](https://livewire.laravel.com/docs)
* 🎨 [Tailwind CSS Documentation](https://tailwindcss.com/docs)

---

This guide provides a clear and concise overview of the Flux UI Modal component, focusing on its features available in the free version. Let me know if you need further assistance or examples!

[1]: https://bladewindui.com/component/modal?utm_source=chatgpt.com "Modal Component - BladewindUI"
[2]: https://fluxui.dev/components/modal?utm_source=chatgpt.com "Modal - Flux UI"
