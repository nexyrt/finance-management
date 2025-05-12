# Flux UI Button Component

The Flux UI Button component is a versatile and composable element designed for seamless integration with Livewire and styled using Tailwind CSS.

---

## 📦 Installation

Installation guide: [https://fluxui.dev/docs/installation](https://fluxui.dev/docs/installation)

---

## ✅ Basic Usage


```blade
<flux:button>Click me</flux:button>
```



---

## 🎨 Variants

Customize the button's appearance using the `variant` prop:

* `primary`
* `filled`
* `danger`
* `ghost`
* `subtle`

```blade
<flux:button variant="primary">Primary</flux:button>
<flux:button variant="danger">Danger</flux:button>
```



---

## 📏 Sizes

Adjust the button's size using the `size` prop:

* `sm` (small)
* `xs` (extra small)

```blade
<flux:button size="sm">Small Button</flux:button>
<flux:button size="xs">Extra Small Button</flux:button>
```



---

## 🔄 Loading State

Buttons with `wire:click` or `type="submit"` will automatically show a loading indicator and disable pointer events during network requests.

```blade
<flux:button wire:click="save">Save changes</flux:button>
```



To disable this behavior:

```blade
<flux:button wire:click="save" :loading="false">Save changes</flux:button>
```



---

## 📐 Full Width

Make the button span the full width of its container:

```blade
<flux:button class="w-full">Full Width Button</flux:button>
```



---

## 🔘 Button Groups

Group related buttons together:

```blade
<flux:button.group>
    <flux:button>Option 1</flux:button>
    <flux:button>Option 2</flux:button>
</flux:button.group>
```



---

## 🧭 Reference: Available Properties

* **`variant`**: Defines the button's visual style. Options include `primary`, `filled`, `danger`, `ghost`, and `subtle`.
* **`size`**: Adjusts the button's size. Options include `sm` (small) and `xs` (extra small).
* **`icon`**: Adds a leading icon to the button.
* **`icon:trailing`**: Adds a trailing icon to the button.
* **`loading`**: Controls the loading state. Set to `false` to disable the automatic loading indicator.
* **`class`**: Applies additional Tailwind CSS classes to the button.
* **`href`**: Renders the button as an anchor (`<a>`) tag, turning it into a link.
* **`square`**: Makes the button's width and height equal, typically used for icon-only buttons.
* **`inset`**: Removes invisible padding, useful for aligning buttons with other elements.

---

## 🔗 Additional Resources

* Component Documentation: [https://fluxui.dev/components/button](https://fluxui.dev/components/button)
* Flux UI Documentation: [https://fluxui.dev/docs](https://fluxui.dev/docs)
* Livewire Documentation: [https://livewire.laravel.com/docs](https://livewire.laravel.com/docs)
* Tailwind CSS Documentation: [https://tailwindcss.com/docs](https://tailwindcss.com/docs)

---

This `README.md` provides a clear and concise guide to implementing the Flux UI Button component in your Livewire applications, focusing solely on the features available in the free version.