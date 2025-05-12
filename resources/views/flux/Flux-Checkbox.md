# Flux UI Checkbox Component

This README provides a comprehensive guide to using the Flux UI Checkbox component in your Livewire applications. Flux UI is a robust, hand-crafted UI component library built specifically for Livewire interfaces, utilizing Tailwind CSS for styling.

---

## 📦 Installation

Ensure you have Flux UI installed in your Laravel project. If not, follow the installation steps provided in the [Flux UI Installation Guide](https://fluxui.dev/docs/installation).

---

## ✅ Basic Usage

To create a simple checkbox:

```blade
<flux:checkbox wire:model="terms" />
<flux:label>I agree to the terms and conditions</flux:label>
<flux:error name="terms" />
```

---

## 📋 Checkbox Group

Organize related checkboxes into a group:

```blade
<flux:checkbox.group wire:model="notifications" label="Notifications">
    <flux:checkbox label="Push notifications" value="push" checked />
    <flux:checkbox label="Email" value="email" checked />
    <flux:checkbox label="In-app alerts" value="app" />
    <flux:checkbox label="SMS" value="sms" />
</flux:checkbox.group>
```

This approach binds multiple checkboxes to a Livewire property, allowing for grouped selections.

---

## 📝 Checkbox with Descriptions

Add descriptions to each checkbox for better clarity:

```blade
<flux:checkbox.group wire:model="subscription" label="Subscription preferences">
    <flux:checkbox checked value="newsletter" label="Newsletter" description="Receive our monthly newsletter with the latest updates and offers." />
    <flux:checkbox value="updates" label="Product updates" description="Stay informed about new features and product updates." />
    <flux:checkbox value="invitations" label="Event invitations" description="Get invitations to our exclusive events and webinars." />
</flux:checkbox.group>
```

Descriptions provide additional context to each option.

---

## 🔁 Horizontal Fieldset

Display checkboxes horizontally within a fieldset:

```blade
<flux:fieldset>
    <flux:legend>Languages</flux:legend>
    <flux:description>Choose the languages you want to support.</flux:description>
    <div class="flex gap-4">
        <flux:checkbox checked value="english" label="English" />
        <flux:checkbox checked value="spanish" label="Spanish" />
        <flux:checkbox value="french" label="French" />
        <flux:checkbox value="german" label="German" />
    </div>
</flux:fieldset>
```

This layout is useful for categorizing options within a form.

---

## ✅ Select All Checkbox

Implement a "Select All" checkbox to control a group of checkboxes:

```blade
<flux:checkbox.group>
    <flux:checkbox.all />
    <flux:checkbox checked />
    <flux:checkbox />
    <flux:checkbox />
</flux:checkbox.group>
```

The `.all` component acts as a master checkbox to select or deselect all options.

---

## ✅ Pre-Checked Checkbox

Set a checkbox to be checked by default:

```blade
<flux:checkbox checked />
```

This is useful for default selections in forms.

---

## 🚫 Disabled Checkbox

Make a checkbox non-interactive:

```blade
<flux:checkbox disabled />
```

Disabled checkboxes are visually distinct and cannot be interacted with.

---

## 🃏 Checkbox Cards (Pro Version)

Use bordered checkbox cards for a more stylized appearance:

```blade
<flux:checkbox.group wire:model="subscription" label="Subscription preferences" variant="cards" class="max-sm:flex-col">
    <flux:checkbox checked value="newsletter" label="Newsletter" description="Get the latest updates and offers." />
    <flux:checkbox value="updates" label="Product updates" description="Learn about new features and products." />
    <flux:checkbox value="invitations" label="Event invitations" description="Invitations to exclusive events." />
</flux:checkbox.group>
```

This variant is available in the Pro version of Flux UI. [Upgrade to unlock](https://fluxui.dev/docs/pricing).

---

## ⚙️ Customization

You can publish the checkbox component to customize its Blade files:

```bash
php artisan flux:publish
```

After publishing, you'll find the component files in `resources/views/flux/checkbox.blade.php`, where you can modify classes, slots, and variants as needed.

For more on customization, refer to the [Flux UI Customization Guide](https://fluxui.dev/docs/customization).

---

## 🔗 Documentation

For detailed documentation and more examples, visit the [Flux UI Checkbox Documentation](https://fluxui.dev/components/checkbox).

---

## 🧠 Notes

* Use `wire:model` to bind checkboxes to Livewire properties.
* The `.all` component is used to create a "Select All" checkbox in a group.
* Descriptions can be added to checkboxes to provide additional context.
* The Pro version offers advanced features like checkbox cards with icons.

---

Feel free to integrate this component into your Livewire applications to enhance user interactions with checkboxes.

```

---

Let me know if you need further customization or additional information!
::contentReference[oaicite:0]{index=0}
 
```
