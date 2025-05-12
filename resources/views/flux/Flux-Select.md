# Flux UI Select Component

The Flux UI Select component offers a customizable alternative to the native HTML `<select>` element, designed for seamless integration with Livewire and styled using Tailwind CSS.:contentReference[oaicite:2]{index=2}

---

## 📦 Installation

:contentReference[oaicite:4]{index=4}:contentReference[oaicite:6]{index=6}

Installation guide: [https://fluxui.dev/docs/installation](https://fluxui.dev/docs/installation)

---

## ✅ Basic Usage

:contentReference[oaicite:8]{index=8}:contentReference[oaicite:10]{index=10}


```blade
<flux:select wire:model="industry" placeholder="Choose industry...">
    <flux:select.option>Photography</flux:select.option>
    <flux:select.option>Design services</flux:select.option>
    <flux:select.option>Web development</flux:select.option>
    <flux:select.option>Accounting</flux:select.option>
    <flux:select.option>Legal services</flux:select.option>
    <flux:select.option>Consulting</flux:select.option>
    <flux:select.option>Other</flux:select.option>
</flux:select>
```


---

## 📏 Small Size Variant

For a more compact select element, use the `size="sm"` attribute:([Flux UI][1])

```blade
<flux:select wire:model="industry" size="sm" placeholder="Choose industry...">
    <!-- options -->
</flux:select>
```



---

## 🔄 Clearable Selection

Allow users to clear their selection by adding the `clearable` attribute:

```blade
<flux:select wire:model="industry" clearable placeholder="Choose industry...">
    <!-- options -->
</flux:select>
```



This adds a clear (×) button to the select component, enabling users to reset their selection.

---

## ⚙️ Customization

To customize the Select component, you can publish its Blade files:([Flux UI][2])

```bash
php artisan flux:publish
```



After publishing, you'll find the component files in `resources/views/flux/select.blade.php`, where you can modify classes, slots, and variants as needed.

Customization guide: [https://fluxui.dev/docs/customization](https://fluxui.dev/docs/customization)

---

## 📚 Reference: Available Properties
| Property      | Type    | Description                                                    | Example Usage                      |                                                                                      |
| ------------- | ------- | -------------------------------------------------------------- | ---------------------------------- | ------------------------------------------------------------------------------------ |
| `wire:model`  | string  | Binds the selected value to a Livewire property                | `wire:model="industry"`            |                                                                                      |
| `placeholder` | string  | Sets the placeholder text displayed when no option is selected | `placeholder="Choose industry..."` |                                                                                      |
| `size`        | string  | Adjusts the size of the select component (`"sm"` for small)    | `size="sm"`                        |                                                                                      |
| `clearable`   | boolean | Adds a clear (×) button to reset the selection                 | `clearable`                        | 
---

This `README.md` provides a clear and concise guide to implementing the Flux UI Select component in your Livewire applications, focusing solely on the features available in the free version.