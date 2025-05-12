# 🚀 Flux UI Dropdown Component

A versatile and customizable dropdown menu for your Livewire app—perfect for navigation links, action menus, filters, and more.

---

## 📦 Installation

📖 Follow the installation guide here:
[https://fluxui.dev/docs/installation](https://fluxui.dev/docs/installation)

---

## ✅ Basic Usage

```blade
<flux:dropdown>
    <flux:button icon:trailing="chevron-down">Options</flux:button>
    <flux:menu>
        <flux:menu.item icon="plus">New post</flux:menu.item>
        <flux:menu.separator />
        <flux:menu.submenu heading="Sort by">
            <flux:menu.radio.group>
                <flux:menu.radio checked>Name</flux:menu.radio>
                <flux:menu.radio>Date</flux:menu.radio>
                <flux:menu.radio>Popularity</flux:menu.radio>
            </flux:menu.radio.group>
        </flux:menu.submenu>
        <flux:menu.submenu heading="Filter">
            <flux:menu.checkbox checked>Draft</flux:menu.checkbox>
            <flux:menu.checkbox checked>Published</flux:menu.checkbox>
            <flux:menu.checkbox>Archived</flux:menu.checkbox>
        </flux:menu.submenu>
        <flux:menu.separator />
        <flux:menu.item variant="danger" icon="trash">Delete</flux:menu.item>
    </flux:menu>
</flux:dropdown>
```

---

## 🧭 Navigation Menu

Create a simple navigation dropdown:

```blade
<flux:dropdown position="bottom" align="end">
    <flux:profile avatar="/img/demo/user.png" name="Olivia Martin" />
    <flux:navmenu>
        <flux:navmenu.item href="#" icon="user">Account</flux:navmenu.item>
        <flux:navmenu.item href="#" icon="building-storefront">Profile</flux:navmenu.item>
        <flux:navmenu.item href="#" icon="credit-card">Billing</flux:navmenu.item>
        <flux:navmenu.item href="#" icon="arrow-right-start-on-rectangle">Logout</flux:navmenu.item>
        <flux:navmenu.item href="#" icon="trash" variant="danger">Delete</flux:navmenu.item>
    </flux:navmenu>
</flux:dropdown>
```

---

## ⚙️ Positioning

Customize the dropdown's position and alignment:

```blade
<flux:dropdown position="top" align="start">...</flux:dropdown>
<flux:dropdown position="right" align="center">...</flux:dropdown>
<flux:dropdown position="bottom" align="center">...</flux:dropdown>
<flux:dropdown position="left" align="end">...</flux:dropdown>
```

---

## 🔧 Offset & Gap

Adjust spacing between trigger and menu:

```blade
<flux:dropdown offset="-15" gap="2">...</flux:dropdown>
```

---

## ⌨️ Keyboard Shortcuts

Add hints for keyboard shortcuts:

```blade
<flux:dropdown>
    <flux:button icon:trailing="chevron-down">Options</flux:button>
    <flux:menu>
        <flux:menu.item icon="pencil-square" kbd="⌘S">Save</flux:menu.item>
        <flux:menu.item icon="document-duplicate" kbd="⌘D">Duplicate</flux:menu.item>
        <flux:menu.item icon="trash" variant="danger" kbd="⌘⌫">Delete</flux:menu.item>
    </flux:menu>
</flux:dropdown>
```

---

## ✅ Checkbox Items

Use checkboxes inside the menu:

```blade
<flux:dropdown>
    <flux:button icon:trailing="chevron-down">Permissions</flux:button>
    <flux:menu>
        <flux:menu.checkbox wire:model="read" checked>Read</flux:menu.checkbox>
        <flux:menu.checkbox wire:model="write" checked>Write</flux:menu.checkbox>
        <flux:menu.checkbox wire:model="delete">Delete</flux:menu.checkbox>
    </flux:menu>
</flux:dropdown>
```

---

## 🛠 Customization

Publish the component to customize styles or layout:

```bash
php artisan flux:publish
```

You’ll find the files in:
`resources/views/flux/dropdown.blade.php`

Guide: [https://fluxui.dev/docs/customization](https://fluxui.dev/docs/customization)

---

## 📚 Available Properties

| Property     | Type    | Description                                         | Example             |
| ------------ | ------- | --------------------------------------------------- | ------------------- |
| `position`   | string  | Dropdown position: `top`, `bottom`, `left`, `right` | `position="bottom"` |
| `align`      | string  | Alignment: `start`, `center`, `end`                 | `align="end"`       |
| `offset`     | integer | Distance from trigger to menu                       | `offset="-15"`      |
| `gap`        | integer | Space between trigger and menu                      | `gap="2"`           |
| `kbd`        | string  | Keyboard shortcut hint                              | `kbd="⌘S"`          |
| `variant`    | string  | Style variant like `danger`                         | `variant="danger"`  |
| `icon`       | string  | Icon name from icon set                             | `icon="trash"`      |
| `checked`    | boolean | Checkbox or radio checked state                     | `checked`           |
| `wire:model` | string  | Bind value to Livewire property                     | `wire:model="read"` |

---

## 🔗 Additional Resources

* 🔧 Component Docs: [fluxui.dev/components/dropdown](https://fluxui.dev/components/dropdown)
* 📘 Flux UI Docs: [fluxui.dev/docs](https://fluxui.dev/docs)
* ⚡ Livewire Docs: [livewire.laravel.com/docs](https://livewire.laravel.com/docs)
* 🎨 Tailwind CSS Docs: [tailwindcss.com/docs](https://tailwindcss.com/docs)

---

Let me know if you want to turn this into a live preview playground, or if you need a version for your documentation site.
