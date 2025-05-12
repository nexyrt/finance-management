# 🏷️ Flux UI Badge Component

The **Badge component** in Flux UI is used to show small counts, statuses, or labels. Perfect for tags, statuses, notifications, or categories.

---

## 🚀 Installation

If you haven't installed Flux UI yet, follow the guide here:
📦 [Flux UI Installation Guide](https://fluxui.dev/docs/installation)

---

## ✅ Basic Usage

Here’s how to use the default badge:

```blade
<flux:badge>Default</flux:badge>
```

👉 This will render a basic badge with default styling.

---

## 🎨 Variants

Flux UI badges come with predefined **variants** to reflect different meanings or types. Use the `variant` prop to change the style.

```blade
<flux:badge variant="default">Default</flux:badge>
<flux:badge variant="secondary">Secondary</flux:badge>
<flux:badge variant="destructive">Destructive</flux:badge>
<flux:badge variant="outline">Outline</flux:badge>
```

| Variant       | Description                        |
| ------------- | ---------------------------------- |
| `default`     | Standard badge (usually primary)   |
| `secondary`   | Subtle and less attention-grabbing |
| `destructive` | Warning or error related           |
| `outline`     | Minimal border-style badge         |

---

## 🧩 Customizing Classes

Need more control? Use `class` to override or add your own styles (e.g., Tailwind classes):

```blade
<flux:badge class="bg-blue-500 text-white">Custom Blue</flux:badge>
```

---

## 🧠 Use Case Examples

### 📌 Status Indicator

```blade
<flux:badge variant="default">Active</flux:badge>
<flux:badge variant="destructive">Inactive</flux:badge>
```

### 🗂️ Category Tags

```blade
<flux:badge class="bg-purple-600 text-white">Laravel</flux:badge>
<flux:badge class="bg-green-600 text-white">Livewire</flux:badge>
```

### 🔔 Notifications

You can even combine it with icons or buttons for advanced UI:

```blade
<flux:button>
    Inbox
    <flux:badge class="ml-2 bg-red-500 text-white">3</flux:badge>
</flux:button>
```

---

## 📚 API Summary

| Prop      | Type     | Description                                                            |
| --------- | -------- | ---------------------------------------------------------------------- |
| `variant` | `string` | Controls badge style. One of: default, secondary, destructive, outline |
| `class`   | `string` | Custom CSS classes for full control                                    |

---

## 🔗 Docs Reference

* 🧾 [Badge Component on FluxUI](https://fluxui.dev/components/badge)
* 🎨 [TailwindCSS Utility Reference](https://tailwindcss.com/docs)

---

Let me know if you want example usage inside a **Livewire component** or combined with other **Flux components** like cards, buttons, or modals!
