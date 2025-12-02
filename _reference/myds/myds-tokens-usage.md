# MYDS Tokens Usage Guide (ICTServe)

This short guide maps commonly used CSS custom properties (MYDS tokens) to their intended component roles.

- **Primary actions (buttons/links):**
  - `--btn-primary-bg`: Primary button background (use on primary CTAs)
  - `--btn-primary-bg-hover`: Hover state for primary buttons
  - `--txt-primary-500`: Primary text/link color

- **Semantic backgrounds & surfaces:**
  - `--bg-white`: Default card/panel background
  - `--bg-washed`: Subtle page background or muted surface
  - `--bg-primary-50`: Light primary surface (status, badges)

- **Text / content:**
  - `--txt-black-900`: Primary body text
  - `--txt-black-700`: Secondary/lead text
  - `--txt-white`: Text on dark backgrounds

- **Status colors:**
  - `--txt-success-600` / `--bg-success-50`: Success messages and badges
  - `--txt-danger` / `--bg-danger-50`: Error states and destructive actions
  - `--txt-warning` / `--bg-warning-50`: Warnings and pending states

- **Outlines, borders & focus:**
  - `--otl-divider`: Default divider color
  - `--fr-primary`: Primary focus ring color (use with `:focus-visible`)
  - `--shadow-button`: Button drop shadow for elevation

- **Spacing & radius:**
  - `--radius-s`, `--radius-m`, `--radius-l`: Small/medium/large radii for components

- **Motion:**
  - `--motion-ease`, `--motion-fast`, `--motion-medium`: Use for transitions and animations

Guideline: Always prefer semantic tokens (above) over raw hex values. For component-level variants, create component tokens that map back to these primitives (e.g., `--btn-primary-bg` -> `--bg-primary-600`).
