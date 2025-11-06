# Accessible Data Tables and Complex Components

## Overview

This document provides comprehensive guidance on using accessible data tables, modal dialogs, dropdown menus, and pagination components that meet WCAG 2.2 Level AA accessibility standards.

## Table of Contents

1. [Accessible Data Tables](#accessible-data-tables)
2. [Accessible Modal Dialogs](#accessible-modal-dialogs)
3. [Accessible Dropdown Menus](#accessible-dropdown-menus)
4. [Accessible Pagination](#accessible-pagination)
5. [Accessibility Features](#accessibility-features)
6. [Testing Guidelines](#testing-guidelines)

---

## Accessible Data Tables

### Component: `<x-accessible.data-table>`

The accessible data table component provides a fully WCAG 2.2 Level AA compliant table with sorting, filtering, and search capabilities.

### Features

- ✅ Proper table headers with `scope` attributes
- ✅ Accessible sorting controls with ARIA sort attributes
- ✅ Accessible filtering with proper form labels
- ✅ Screen reader announcements for sort/filter changes
- ✅ Keyboard navigation support
- ✅ Responsive design with horizontal scrolling
- ✅ Empty state handling
- ✅ Search functionality with debouncing
- ✅ Sticky header option

### Basic Usage

```blade
<x-accessible.data-table
    caption="User List"
    summary="A list of all registered users with their details"
    :headers="[
        ['key' => 'name', 'label' => 'Name', 'sortable' => true, 'scope' => 'col'],
        ['key' => 'email', 'label' => 'Email', 'sortable' => true, 'scope' => 'col'],
        ['key' => 'role', 'label' => 'Role', 'sortable' => false, 'scope' => 'col'],
        ['key' => 'status', 'label' => 'Status', 'sortable' => true, 'scope' => 'col'],
    ]"
    sortable
    searchable
    filterable
    responsive
    striped
    hover
>
    @foreach($users as $user)
        <tr>
            <th scope="row" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ $user->name }}
            </th>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ $user->email }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ $user->role }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                <x-accessibility.status-badge :status="$user->status" />
            </td>
        </tr>
    @endforeach
</x-accessible.data-table>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `caption` | string | null | Table caption (visible) |
| `captionSide` | string | 'top' | Caption position ('top' or 'bottom') |
| `summary` | string | null | Table summary (screen reader only) |
| `headers` | array | [] | Array of header configurations |
| `sortable` | boolean | true | Enable sorting functionality |
| `filterable` | boolean | false | Enable filtering functionality |
| `searchable` | boolean | false | Enable search functionality |
| `responsive` | boolean | true | Enable responsive design |
| `striped` | boolean | true | Alternate row colors |
| `hover` | boolean | true | Highlight rows on hover |
| `compact` | boolean | false | Reduce padding for compact view |
| `stickyHeader` | boolean | false | Make header sticky on scroll |

### Header Configuration

Each header can be configured with the following properties:

```php
[
    'key' => 'column_name',        // Required: Column identifier
    'label' => 'Display Name',     // Required: Visible column name
    'sortable' => true,            // Optional: Enable sorting for this column
    'scope' => 'col',              // Optional: Scope attribute (col/row)
    'abbr' => 'Abbreviation',      // Optional: Abbreviation for long headers
    'class' => 'custom-class',     // Optional: Additional CSS classes
]
```

### Keyboard Navigation

- **Tab**: Navigate between sortable headers and controls
- **Enter/Space**: Activate sorting on focused header
- **Escape**: Clear filters (when filter panel is open)

### Screen Reader Support

- Table caption and summary are announced
- Sort direction is announced when changed
- Filter changes are announced
- Empty state is announced
- All interactive elements have proper ARIA labels

---

## Accessible Modal Dialogs

### Component: `<x-accessible.modal>`

The accessible modal dialog component provides a fully WCAG 2.2 Level AA compliant modal with focus management and keyboard navigation.

### Features

- ✅ Focus trap implementation
- ✅ Keyboard management (Esc, Tab, Shift+Tab)
- ✅ ARIA attributes (role="dialog", aria-modal, aria-labelledby, aria-describedby)
- ✅ Return focus to trigger on close
- ✅ Backdrop click to close
- ✅ Smooth transitions
- ✅ Multiple size options
- ✅ Optional close button

### Basic Usage

```blade
<x-accessible.modal
    name="user-details"
    title="User Details"
    maxWidth="2xl"
    closeable
>
    <div class="space-y-4">
        <p>Modal content goes here...</p>
        
        <div class="flex justify-end space-x-2">
            <x-accessibility.button
                type="button"
                variant="secondary"
                @click="$dispatch('close-modal', 'user-details')"
            >
                {{ __('Cancel') }}
            </x-accessibility.button>
            
            <x-accessibility.button
                type="button"
                variant="primary"
            >
                {{ __('Save') }}
            </x-accessibility.button>
        </div>
    </div>
</x-accessible.modal>

{{-- Trigger Button --}}
<x-accessibility.button
    type="button"
    @click="$dispatch('open-modal', 'user-details')"
>
    {{ __('Open Modal') }}
</x-accessibility.button>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | 'modal' | Unique modal identifier |
| `title` | string | null | Modal title |
| `show` | boolean | false | Initial visibility state |
| `maxWidth` | string | '2xl' | Maximum width (sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl) |
| `closeable` | boolean | true | Show close button |

### Keyboard Navigation

- **Escape**: Close modal
- **Tab**: Navigate forward through focusable elements (trapped within modal)
- **Shift+Tab**: Navigate backward through focusable elements (trapped within modal)

### Events

- `open-modal`: Open modal by name
- `close-modal`: Close modal by name

```javascript
// Open modal
$dispatch('open-modal', 'modal-name')

// Close modal
$dispatch('close-modal', 'modal-name')
```

---

## Accessible Dropdown Menus

### Component: `<x-accessible.dropdown-menu>`

The accessible dropdown menu component provides a fully WCAG 2.2 Level AA compliant dropdown with keyboard navigation.

### Features

- ✅ Keyboard navigation (Arrow keys, Home, End, Enter, Space, Esc)
- ✅ ARIA attributes (role="menu", aria-haspopup, aria-expanded)
- ✅ Focus management
- ✅ Support for menu items, checkboxes, and radio buttons
- ✅ Disabled state support
- ✅ Multiple alignment options
- ✅ Customizable width

### Basic Usage

```blade
<x-accessible.dropdown-menu
    align="right"
    width="48"
    label="Actions"
>
    <x-accessible.menu-item href="{{ route('users.edit', $user) }}">
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </x-slot:icon>
        {{ __('Edit') }}
    </x-accessible.menu-item>
    
    <x-accessible.menu-item 
        type="button"
        @click="deleteUser({{ $user->id }})"
    >
        <x-slot:icon>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </x-slot:icon>
        {{ __('Delete') }}
    </x-accessible.menu-item>
</x-accessible.dropdown-menu>
```

### Menu Item Types

```blade
{{-- Link Menu Item --}}
<x-accessible.menu-item href="/profile">
    Profile
</x-accessible.menu-item>

{{-- Button Menu Item --}}
<x-accessible.menu-item type="button" @click="handleClick()">
    Action
</x-accessible.menu-item>

{{-- Checkbox Menu Item --}}
<x-accessible.menu-item type="checkbox" :checked="$isChecked">
    Enable Notifications
</x-accessible.menu-item>

{{-- Radio Menu Item --}}
<x-accessible.menu-item type="radio" :checked="$isSelected">
    Option 1
</x-accessible.menu-item>

{{-- Disabled Menu Item --}}
<x-accessible.menu-item disabled>
    Disabled Action
</x-accessible.menu-item>
```

### Props (Dropdown Menu)

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `align` | string | 'right' | Alignment ('left', 'right', 'center') |
| `width` | string | '48' | Width (48, 56, 64, 72, 80, 96, full) |
| `contentClasses` | string | 'py-1 bg-white dark:bg-gray-700' | Custom content classes |
| `trigger` | slot | null | Custom trigger element |
| `disabled` | boolean | false | Disable dropdown |
| `label` | string | 'Menu' | Default trigger label |

### Props (Menu Item)

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `href` | string | null | Link URL |
| `icon` | slot | null | Icon element |
| `disabled` | boolean | false | Disable menu item |
| `active` | boolean | false | Active state |
| `type` | string | 'link' | Type ('link', 'button', 'checkbox', 'radio') |
| `checked` | boolean | null | Checked state (for checkbox/radio) |

### Keyboard Navigation

- **Arrow Down**: Move focus to next menu item
- **Arrow Up**: Move focus to previous menu item
- **Home**: Move focus to first menu item
- **End**: Move focus to last menu item
- **Enter/Space**: Activate focused menu item
- **Escape**: Close menu and return focus to trigger
- **Tab**: Close menu and move focus to next element

---

## Accessible Pagination

### Component: `<x-accessible.pagination>`

The accessible pagination component provides a fully WCAG 2.2 Level AA compliant pagination with proper ARIA labels.

### Features

- ✅ ARIA labels for all navigation elements
- ✅ Current page indication with aria-current
- ✅ Keyboard navigation support
- ✅ Page jumper for large datasets
- ✅ Responsive design
- ✅ Screen reader announcements
- ✅ Ellipsis for large page ranges

### Basic Usage

```blade
<x-accessible.pagination
    :paginator="$users"
    showInfo
    showJumper
    :maxLinks="7"
/>
```

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `paginator` | object | required | Laravel paginator instance |
| `showInfo` | boolean | true | Show result count information |
| `showJumper` | boolean | false | Show page number input |
| `compact` | boolean | false | Compact mobile view |
| `maxLinks` | integer | 7 | Maximum page links to display |

### Keyboard Navigation

- **Tab**: Navigate between page links
- **Enter**: Activate focused page link
- **Arrow keys**: Navigate in page jumper input

---

## Accessibility Features

### WCAG 2.2 Level AA Compliance

All components meet the following WCAG 2.2 Level AA success criteria:

#### 1.3.1 Info and Relationships (Level A)

- Proper semantic HTML structure
- Correct use of table headers with scope attributes
- Proper heading hierarchy in modals
- ARIA landmarks and roles

#### 2.1.1 Keyboard (Level A)

- All functionality available via keyboard
- No keyboard traps (except intentional focus traps in modals)
- Logical tab order

#### 2.1.2 No Keyboard Trap (Level A)

- Users can navigate away from all components using standard keyboard navigation
- Modal focus traps can be exited with Escape key

#### 2.4.3 Focus Order (Level A)

- Logical and intuitive focus order
- Focus moves to appropriate elements when opening/closing components

#### 2.4.6 Headings and Labels (Level AA)

- Descriptive labels for all form controls
- Clear headings in modal dialogs
- Proper table captions and summaries

#### 2.4.7 Focus Visible (Level AA)

- Clear focus indicators on all interactive elements
- 3-4px outline with 2px offset
- Minimum 3:1 contrast ratio for focus indicators

#### 3.2.4 Consistent Identification (Level AA)

- Consistent component behavior across the application
- Consistent keyboard shortcuts
- Consistent visual design

#### 4.1.2 Name, Role, Value (Level A)

- Proper ARIA roles for all components
- Descriptive accessible names
- Current state communicated to assistive technologies

#### 4.1.3 Status Messages (Level AA)

- ARIA live regions for dynamic content updates
- Polite announcements for sort/filter changes
- Status messages for empty states

### Touch Target Size

All interactive elements meet the minimum touch target size of 44×44 pixels as specified in WCAG 2.2 Level AA (Success Criterion 2.5.8).

### Screen Reader Support

- Tested with NVDA (Windows)
- Tested with JAWS (Windows)
- Tested with VoiceOver (macOS/iOS)
- Tested with TalkBack (Android)

---

## Testing Guidelines

### Manual Testing Checklist

#### Data Tables

- [ ] Table caption is announced by screen readers
- [ ] Table headers are properly associated with data cells
- [ ] Sorting controls are keyboard accessible
- [ ] Sort direction is announced when changed
- [ ] Search input is properly labeled
- [ ] Filter controls are keyboard accessible
- [ ] Empty state is announced
- [ ] All interactive elements have 44×44px touch targets

#### Modal Dialogs

- [ ] Modal opens when triggered
- [ ] Focus moves to first focusable element in modal
- [ ] Tab key cycles through focusable elements within modal
- [ ] Shift+Tab cycles backward through focusable elements
- [ ] Escape key closes modal
- [ ] Focus returns to trigger element when modal closes
- [ ] Backdrop click closes modal
- [ ] Modal title is announced by screen readers
- [ ] Close button has proper accessible label

#### Dropdown Menus

- [ ] Dropdown opens when triggered
- [ ] Arrow keys navigate through menu items
- [ ] Home/End keys jump to first/last items
- [ ] Enter/Space activates focused menu item
- [ ] Escape closes menu and returns focus to trigger
- [ ] Menu items are properly announced by screen readers
- [ ] Disabled items are not focusable
- [ ] Checkbox/radio states are announced

#### Pagination

- [ ] Current page is announced with aria-current
- [ ] Previous/Next buttons have proper labels
- [ ] Page links have descriptive labels
- [ ] Disabled buttons are not focusable
- [ ] Page jumper input is properly labeled
- [ ] Result count is announced

### Automated Testing

```php
// tests/Feature/AccessibleComponentsTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Dusk\Browser;

class AccessibleComponentsTest extends TestCase
{
    public function test_data_table_has_proper_structure(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                    ->assertPresent('table')
                    ->assertPresent('caption')
                    ->assertAttribute('table', 'aria-describedby', 'table-.*-caption')
                    ->assertPresent('thead th[scope="col"]');
        });
    }
    
    public function test_modal_focus_management(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                    ->click('[data-modal-trigger="user-details"]')
                    ->waitFor('[role="dialog"]')
                    ->assertFocused('[role="dialog"] button:first-of-type')
                    ->keys('', '{escape}')
                    ->waitUntilMissing('[role="dialog"]')
                    ->assertFocused('[data-modal-trigger="user-details"]');
        });
    }
    
    public function test_dropdown_keyboard_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/users')
                    ->click('[aria-haspopup="true"]')
                    ->waitFor('[role="menu"]')
                    ->keys('', '{arrow_down}')
                    ->assertFocused('[role="menuitem"]:first-of-type')
                    ->keys('', '{arrow_down}')
                    ->assertFocused('[role="menuitem"]:nth-of-type(2)')
                    ->keys('', '{escape}')
                    ->waitUntilMissing('[role="menu"]');
        });
    }
}
```

---

## Best Practices

### Data Tables

1. **Always provide a caption**: Helps users understand the table's purpose
2. **Use scope attributes**: Properly associate headers with data cells
3. **Keep tables simple**: Avoid nested tables and complex structures
4. **Provide alternative views**: Consider card layouts for mobile devices
5. **Announce dynamic changes**: Use ARIA live regions for sort/filter updates

### Modal Dialogs

1. **Provide descriptive titles**: Help users understand the modal's purpose
2. **Manage focus properly**: Move focus into modal and return it on close
3. **Keep content focused**: Avoid putting too much content in modals
4. **Provide clear actions**: Make it obvious how to proceed or cancel
5. **Test with keyboard only**: Ensure all functionality is keyboard accessible

### Dropdown Menus

1. **Use semantic menu roles**: Helps screen readers understand the structure
2. **Provide clear labels**: Make menu items descriptive and actionable
3. **Group related items**: Use separators or submenus for organization
4. **Indicate current state**: Show checked/selected items clearly
5. **Keep menus short**: Long menus are difficult to navigate

### Pagination

1. **Provide context**: Show current page and total results
2. **Use descriptive labels**: Make page links clear and specific
3. **Limit visible pages**: Use ellipsis for large page ranges
4. **Offer alternatives**: Provide page jumper for large datasets
5. **Announce changes**: Update screen readers when page changes

---

## Resources

- [WCAG 2.2 Guidelines](https://www.w3.org/WAI/WCAG22/quickref/)
- [ARIA Authoring Practices Guide](https://www.w3.org/WAI/ARIA/apg/)
- [WebAIM: Accessible Tables](https://webaim.org/techniques/tables/)
- [WebAIM: Accessible Modal Dialogs](https://webaim.org/techniques/aria/modals)
- [WebAIM: Accessible Dropdown Menus](https://webaim.org/techniques/aria/menus)

---

## Support

For questions or issues with accessible components, please contact the development team or refer to the main accessibility documentation at `docs/frontend/accessibility-guide.md`.
