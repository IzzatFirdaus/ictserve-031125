# Component Metadata Template

## Standard Header Format

All Blade components must include the following metadata header at the top of the file:

```blade
{{--
@component Component Name
@description Brief description of the component's purpose and functionality
@author Pasukan BPM MOTAC
@trace D03-REQ-X.X, D04-§X.X (Requirements and Design references)
@wcag WCAG 2.2 Level AA (Compliance level)
@updated YYYY-MM-DD
@version X.X.X (SemVer)

@props
- prop1: type - Description
- prop2: type - Description

@usage
<x-category.component-name
    prop1="value"
    prop2="value"
/>

@accessibility
- Keyboard navigation: Description
- Screen reader support: Description
- ARIA attributes: Description
- Focus indicators: Description
- Touch targets: 44×44px minimum

@responsive
- Mobile (320px-414px): Behavior description
- Tablet (768px-1024px): Behavior description
- Desktop (1280px-1920px): Behavior description

@notes
- Additional implementation notes
- Known limitations
- Browser-specific considerations
--}}
```

## Example: Button Component

```blade
{{--
@component Primary Button
@description WCAG 2.2 AA compliant primary action button with focus indicators and touch targets
@author Pasukan BPM MOTAC
@trace D03-REQ-6.3, D03-REQ-14.4, D04-§6.1
@wcag WCAG 2.2 Level AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.8)
@updated 2025-11-03
@version 1.0.0

@props
- type: string - Button type (button|submit|reset), default: button
- disabled: boolean - Disabled state, default: false
- class: string - Additional CSS classes

@usage
<x-ui.button type="submit" class="w-full">
    Submit Form
</x-ui.button>

@accessibility
- Keyboard navigation: Tab to focus, Enter/Space to activate
- Screen reader support: Button role announced, disabled state announced
- ARIA attributes: aria-disabled when disabled
- Focus indicators: 4px ring with 2px offset, 6.8:1 contrast ratio
- Touch targets: 44×44px minimum (min-h-[44px] min-w-[44px])

@responsive
- Mobile (320px-414px): Full width on mobile, proper touch targets
- Tablet (768px-1024px): Inline-block with proper spacing
- Desktop (1280px-1920px): Inline-block with hover states

@notes
- Uses compliant MOTAC blue (#0056b3) for primary actions
- Hover state uses darker shade (#004085)
- Disabled state uses gray-300 with reduced opacity
- Focus ring uses MOTAC blue with 6.8:1 contrast ratio
--}}

@props([
    'type' => 'button',
    'disabled' => false,
])

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-4 py-2.5 bg-motac-blue border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-motac-blue-dark focus:bg-motac-blue-dark active:bg-motac-blue-dark focus:outline-none focus:ring-4 focus:ring-motac-blue focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed'
    ]) }}
>
    {{ $slot }}
</button>
```

## Metadata Field Descriptions

### Required Fields

- **@component**: Component name in Title Case
- **@description**: One-sentence description of purpose
- **@author**: Always "Pasukan BPM MOTAC"
- **@trace**: D03 requirements and D04 design section references
- **@wcag**: WCAG compliance level and relevant success criteria
- **@updated**: Last update date in YYYY-MM-DD format
- **@version**: Semantic version number (MAJOR.MINOR.PATCH)

### Optional Fields

- **@props**: Component properties with types and descriptions
- **@usage**: Code example showing how to use the component
- **@accessibility**: Accessibility features and compliance details
- **@responsive**: Responsive behavior across viewport sizes
- **@notes**: Additional implementation notes and considerations

## Requirements Traceability

### D03 Requirements Format

- Format: `D03-REQ-X.X` where X.X is the requirement number
- Example: `D03-REQ-6.3` (Requirement 6, Acceptance Criteria 3)

### D04 Design Specifications Format

- Format: `D04-§X.X` where X.X is the section number
- Example: `D04-§6.1` (Section 6.1 Frontend Component Architecture)

### WCAG Success Criteria Format

- Format: `SC X.X.X` where X.X.X is the success criterion number
- Example: `SC 2.4.7` (Focus Visible)

## Version History Format

When updating a component, add a version history entry:

```blade
@version 1.1.0
@changelog
- 1.1.0 (2025-11-03): Added ARIA live region support
- 1.0.1 (2025-11-02): Fixed focus indicator contrast ratio
- 1.0.0 (2025-11-01): Initial implementation
```

## Compliance Checklist

Before marking a component as complete, verify:

- [ ] All required metadata fields present
- [ ] D03 requirements linked
- [ ] D04 design specifications linked
- [ ] WCAG 2.2 Level AA compliance documented
- [ ] Accessibility features documented
- [ ] Responsive behavior documented
- [ ] Usage example provided
- [ ] Props documented with types
- [ ] Version number follows SemVer
- [ ] Update date is current

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-03  
**Author**: Frontend Engineering Team
