# 📥 Using `<flux:input>` in Flux UI

Flux UI offers a flexible and elegant input component that integrates seamlessly with Livewire and Tailwind CSS.([GitHub][2])

## ✅ Basic Usage

```blade
<flux:input label="Username" description="This will be publicly displayed." />
```



This shorthand automatically wraps the input with a label, description, and error handling.([Flux UI][3])

## 🔤 Input Types

```blade
<flux:input type="email" label="Email" />
<flux:input type="password" label="Password" />
<flux:input type="date" label="Date" />
```



## 📁 File Uploads

```blade
<flux:input type="file" wire:model="logo" label="Logo" />
<flux:input type="file" wire:model="attachments" label="Attachments" multiple />
```



## 🧩 Enhancements

* **Clearable**: Adds a clear button.([Flux UI][4])

```blade
  <flux:input placeholder="Search orders" clearable />
```



* **Copyable**: Adds a copy-to-clipboard button.([Flux UI][4])

```blade
  <flux:input value="FLUX-1234-5678-ABCD-EFGH" readonly copyable />
```



* **Viewable**: Toggles password visibility.([Flux UI][4])

```blade
  <flux:input type="password" value="password" viewable />
```



## 🎨 Icons

```blade
<flux:input icon="magnifying-glass" placeholder="Search..." />
<flux:input icon:trailing="credit-card" placeholder="Card Number" />
```



## 🔘 Icon Buttons

```blade
<flux:input placeholder="Search orders">
    <x-slot name="iconTrailing">
        <flux:button size="sm" variant="subtle" icon="x-mark" class="-mr-1" />
    </x-slot>
</flux:input>
```



## 🧩 Grouped Inputs

```blade
<flux:input.group>
    <flux:input placeholder="Post title" />
    <flux:button icon="plus">New post</flux:button>
</flux:input.group>
```



## 🔤 Prefixes & Suffixes

```blade
<flux:input.group>
    <flux:input.group.prefix>https://</flux:input.group.prefix>
    <flux:input placeholder="example.com" />
</flux:input.group>
```



## 🎛️ Additional Props

* `size="sm"`: Smaller input.
* `disabled`: Disables the input.
* `readonly`: Makes the input read-only.
* `invalid`: Marks the input as invalid.
* `mask="(999) 999-9999"`: Applies input masking.([Flux UI][4], [Reddit][5])

## 🎨 Styling

* `class`: Applies classes to the wrapper.
* `class:input`: Applies classes directly to the input element.([Flux UI][6])

---

For more details and examples, visit the official documentation: ([Flux UI][4])

Let me know if you need further assistance or examples!

[1]: https://fluxui.dev/docs/principles?utm_source=chatgpt.com "Principles - Flux UI"
[2]: https://github.com/livewire/flux?utm_source=chatgpt.com "Flux - The official Livewire UI component library - GitHub"
[3]: https://fluxui.dev/components/field?utm_source=chatgpt.com "Field - Flux UI"
[4]: https://fluxui.dev/components/input?utm_source=chatgpt.com "Input - Flux UI"
[5]: https://www.reddit.com/r/laravel/comments/1f39hxi/flux_livewire_ui_kit/?utm_source=chatgpt.com "Flux · Livewire UI kit : r/laravel - Reddit"
[6]: https://fluxui.dev/docs/patterns?utm_source=chatgpt.com "Patterns - Flux UI"
