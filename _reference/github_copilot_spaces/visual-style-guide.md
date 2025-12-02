# Visual Style Guide & Design Tokens (v3.5.0)

> **Context:** Official visual style definitions for ICTServe. Use this to generate Tailwind CSS v4 configuration and component styling.

## 1. Color System (MyDS Aligned)
**CSS Variables (Tailwind Theme):**

```css
@theme {
    /* Primary Brand Colors */
    --color-primary: #0056b3;           /* MOTAC Blue */
    --color-primary-hover: #004494;
    --color-primary-light: #e6f0ff;

    /* Semantic Status Colors */
    --color-success: #198754;           /* Green */
    --color-warning: #ff8c00;           /* Orange */
    --color-danger: #b50c0c;            /* Red */

    /* Text Colors */
    --color-text-primary: #1a1a1a;
    --color-text-secondary: #4a4a4a;

    /* Focus Indicators */
    --color-focus-ring: #0056b3;        /* 3px outline */
}
```

## 2\. Typography System

* **Headings:** `Poppins` (Weights: 400, 500, 600)
* **Body:** `Inter` (Weights: 400, 500)
* **Monospace:** `JetBrains Mono`
* **Scale:**
  * `H1`: 2rem (32px) / Bold
  * `H2`: 1.5rem (24px) / Semibold
  * `Body`: 1rem (16px) / Regular

## 3\. Layout & Grid (12-8-4 System)

The system uses a responsive grid aligned with MyDS standards:

* **Desktop (≥1024px):** 12 Columns, 24px Gap.
* **Tablet (768-1023px):** 8 Columns, 24px Gap.
* **Mobile (≤767px):** 4 Columns, 18px Gap.

## 4\. Shadow & Motion Tokens

**Shadows:**

* **Card:** `0px 2px 6px rgba(0,0,0,0.05)`
* **Dropdown:** `0px 12px 50px rgba(0,0,0,0.10)`

**Motion:**

* **Transition:** `cubic-bezier(0, 0, 0.58, 1)` (Ease Out)
* **Duration:** Short (200ms), Medium (400ms)

## 5\. Specific Component Styling (v3.5.0)

### Registration Form

* **Container:** `max-w-md mx-auto bg-white rounded-lg shadow-card p-6`
* **Inputs:** `rounded-md border-gray-300 focus:ring-primary-500`
* **Password Strength:** Progress bar with Red/Yellow/Green indicators.

### Google SSO Button

* **Style:** White background, gray border, centered icon.
* **Classes:** `flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 px-4 py-2 hover:bg-gray-50`

### Status Badges

**Pattern:** Background at 10% opacity + Dark Text + Icon.

* **Success:** `bg-success/10 text-success`
* **Warning:** `bg-warning/10 text-warning`
* **Danger:** `bg-danger/10 text-danger`
