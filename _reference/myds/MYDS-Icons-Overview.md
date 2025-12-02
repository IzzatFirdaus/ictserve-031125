# MYDS Icons Overview

This document provides a comprehensive reference and guidance for all icons in the Malaysia Government Design System (MYDS). It covers icon types, design rules, accessibility, integration, best practices, and a complete list of available icons grouped by category.

---

## 1. Introduction

Icons in MYDS are designed to communicate meaning quickly, aid navigation, clarify actions, and support accessibility for all Malaysians. Every icon adheres to MYDS and MyGovEA principles: citizen-centricity, inclusivity, clarity, and technical consistency.

---

## 2. Icon Types & Groups

MYDS icons are organized into the following main groups:

| Group             | Description / Typical Usage                                                           |
| ----------------- | ------------------------------------------------------------------------------------- |
| **Generic**       | Common UI actions and system controls (search, add, edit, delete, settings, etc.)     |
| **WYSIWYG**       | Content formatting tools for text editors (bold, italic, underline, lists, etc.)      |
| **Social Media**  | Official social platform logos (Facebook, Instagram, LinkedIn, YouTube, etc.)         |
| **Media**         | File type indicators (PDF, Word, Excel, PowerPoint, etc.)                             |
| **Agency/Legacy** | Government, department, and legacy symbols (malaysia-flag, jata-negara, agency icons) |

---

## 3. Icon Design Guidelines

- **Grid Size:** All icons are drawn on a 20×20px grid for pixel-perfect alignment.
- **Stroke Width:** Standard stroke width is 1.5px at base size. Strokes scale proportionally for other sizes.
- **Sizes:** Recommended:
  - 16×16px (small buttons)
  - 20×20px (medium buttons, default)
  - 24×24px (large buttons)
  - 32×32px, 42×42px (alerts/dialogs)
- **Variants:** Outline (stroke only) and Filled (solid).
- **Format:** SVG, with clear, scalable paths.
- **Visual Consistency:** Icons should be visually balanced, clear at all sizes, and easily recognizable.

---

## 4. Accessibility & Usage Best Practices

- **Pair with text:** Icons must not be the sole indicator of status/action. Always pair with labels or tooltips.
- **ARIA:** For interactive icons, use `aria-label` or descriptive text.
- **Contrast:** Ensure icons meet at least 4.5:1 contrast ratio (WCAG AA).
- **Keyboard navigation:** All interactive icons must be focusable and operable via keyboard.
- **Do not use color alone:** Use shape or text for status, not color-only.
- **Consistent meaning:** Use standard iconography for common actions.

---

## 5. Integration

- **React:** Import from `@govtechmy/myds-react/icon` (e.g., `import { SearchIcon } from "@govtechmy/myds-react/icon";`)
- **Figma:** Official Figma Kit contains all icons for design teams.
- **SVG:** Download and use SVG directly for any framework.

---

## 6. Icon Reference List (Grouped)

Below is the complete list of MYDS icons grouped by category.  
_Note: Icon names are case-sensitive and match their official SVG/component names._

### 6.1 Generic Icons

| Icon Name           | Notes / Usage                |
| ------------------- | ---------------------------- |
| malaysia-flag       | Malaysia flag                |
| jata-negara         | National emblem              |
| hamburger-menu      | Navigation menu              |
| sun                 | Theme (light)                |
| moon                | Theme (dark)                 |
| home                | Homepage                     |
| search              | Search functions             |
| bell                | Notifications                |
| user                | User profile                 |
| user-group          | Group of users               |
| edit                | Edit action                  |
| trash               | Delete/remove                |
| plus                | Add/create                   |
| plus-circle         | Add (circle background)      |
| zoom-in             | Zoom in                      |
| minus               | Subtract/remove              |
| minus-circle        | Subtract (circle background) |
| zoom-out            | Zoom out                     |
| table               | Table/grid                   |
| grid                | Grid view                    |
| list                | List view                    |
| upload              | Upload file                  |
| download            | Download file                |
| check               | Confirm/select               |
| check-circle        | Confirm (circle)             |
| cross               | Cancel/close                 |
| cross-circle        | Cancel (circle)              |
| govt-office         | Government office            |
| putrajaya           | Putrajaya city               |
| lock                | Locked                       |
| lock-2              | Alternative lock icon        |
| expand              | Expand/collapse              |
| flag                | Flag                         |
| thumbs-up           | Like/upvote                  |
| thumbs-down         | Dislike/downvote             |
| cursor              | Pointer/cursor               |
| accessible          | Accessibility indicator      |
| heart               | Favorite/like                |
| component           | Components                   |
| copy                | Copy to clipboard            |
| duplicate           | Duplicate/clone              |
| link                | Link/hyperlink               |
| link-diagonal       | External link                |
| eye-show            | Show/reveal                  |
| eye-hide            | Hide/conceal                 |
| ellipsis            | More options (horizontal)    |
| ellipsis-vertical   | More options (vertical)      |
| calendar            | Calendar/date                |
| clock               | Time                         |
| filter              | Filter                       |
| filter-desc         | Descending filter            |
| filter-asc          | Ascending filter             |
| map                 | Map/location                 |
| direction           | Directions                   |
| pin                 | Pin/location marker          |
| warning             | Warning/alert                |
| warning-diamond     | Warning (diamond shape)      |
| warning-circle      | Warning (circle shape)       |
| info                | Information                  |
| question-circle     | Help/question                |
| printer             | Print                        |
| book                | Book/manual                  |
| globe               | Global/world                 |
| money               | Money/currency               |
| qr-code             | QR code                      |
| share               | Share                        |
| folder              | Folder                       |
| folder-plus         | Add folder                   |
| folder-minus        | Remove folder                |
| document-filled     | Filled document              |
| document            | Outline document             |
| document-add        | Add document                 |
| document-minus      | Remove document              |
| excel               | Excel file                   |
| pdf                 | PDF file                     |
| word                | Word file                    |
| attachment          | File attachment              |
| play                | Play media                   |
| pause               | Pause media                  |
| email               | Email/message                |
| phone               | Phone/call                   |
| mobile              | Mobile device                |
| tablet              | Tablet device                |
| desktop             | Desktop device               |
| bolt                | Lightning/flash              |
| setting             | Settings/configuration       |
| chat-bubble         | Chat/message                 |
| chevron-right       | Next/right navigation        |
| chevron-left        | Previous/left navigation     |
| chevron-down        | Down navigation/expand       |
| chevron-up          | Up navigation/collapse       |
| column-expand       | Expand table column          |
| column-collapse     | Collapse table column        |
| arrow-up            | Arrow up                     |
| arrow-down          | Arrow down                   |
| arrow-incoming      | Incoming arrow               |
| arrow-outgoing      | Outgoing arrow               |
| arrow-back          | Back arrow                   |
| arrow-forward       | Forward arrow                |
| arrow-forward-close | Forward with close           |
| arrow-back-close    | Back with close              |
| undo                | Undo action                  |
| redo                | Redo action                  |
| logout              | Logout/session end           |
| drop-arrow-down     | Dropdown arrow               |
| check-shield        | Verified/secure              |
| check-star          | Starred/important            |
| trophy              | Achievement/trophy           |
| star                | Favorite/star                |
| swap                | Swap/exchange                |
| reload              | Reload/refresh               |
| megaphone           | Announcement                 |
| section             | Section divider              |
| text                | Text input                   |
| button              | Button                       |
| gallery             | Gallery/photos               |
| carousel            | Carousel/slider              |
| video               | Video/media                  |
| forms               | Forms                        |
| input-field         | Input field                  |
| database            | Database/server              |
| org-chart           | Organization chart           |

#### Filled Generic Icons (Suffix `-fill`)

| Icon Name          | Notes / Usage        |
| ------------------ | -------------------- |
| chevron-down-fill  | Filled chevron down  |
| chevron-right-fill | Filled chevron right |
| chevron-left-fill  | Filled chevron left  |
| chevron-up-fill    | Filled chevron up    |
| warning-fill       | Filled warning       |
| check-circle-fill  | Filled check circle  |
| lock-fill          | Filled lock          |
| star-fill          | Filled star          |
| cross-fill         | Filled cross         |

---

### 6.2 WYSIWYG Icons

| Icon Name     | Notes / Usage      |
| ------------- | ------------------ |
| font          | Font style         |
| bold          | Bold text          |
| italic        | Italic text        |
| underline     | Underline text     |
| strikethrough | Strikethrough text |
| link          | Insert link        |
| unlink        | Remove link        |
| numbered-list | Numbered list      |
| bulleted-list | Bulleted list      |
| align-left    | Align left         |
| align-center  | Align center       |
| align-right   | Align right        |
| align-justify | Justify text       |
| reset-style   | Reset formatting   |

---

### 6.3 Social Media Icons

| Icon Name | Notes / Usage        |
| --------- | -------------------- |
| rss       | RSS feed             |
| facebook  | Facebook             |
| x         | X (formerly Twitter) |
| linkedin  | LinkedIn             |
| instagram | Instagram            |
| youtube   | YouTube              |
| whatsapp  | WhatsApp             |
| tiktok    | TikTok               |
| figma     | Figma                |
| telegram  | Telegram             |
| github    | GitHub               |
| google    | Google               |

---

### 6.4 Media/File Type Icons

| Icon Name        | Notes / Usage   |
| ---------------- | --------------- |
| powerpoint-media | PowerPoint file |
| excel-media      | Excel file      |
| word-media       | Word document   |
| pdf-media        | PDF file        |

---

### 6.5 Agency/Legacy Icons

| Icon Name                  | Notes / Usage                         |
| -------------------------- | ------------------------------------- |
| dosm                       | Department of Statistics              |
| identity-jpn               | National Registration Department      |
| car-jpj                    | Road Transport Department             |
| book-moe                   | Ministry of Education                 |
| internet-mcmc              | MCMC (Communications & Multimedia)    |
| money-kwap                 | KWAP (Retirement Fund)                |
| helmet-jpa                 | JPA (Public Service Department)       |
| train-mot                  | Ministry of Transport                 |
| unhcr                      | UNHCR                                 |
| ribbon-ntrc                | National Transformation Ribbon        |
| weather-met                | Meteorological Department             |
| flood-jps                  | JPS (Drainage & Irrigation)           |
| jakoa                      | JAKOA                                 |
| blood-pdn                  | National Blood Bank                   |
| ballot-spr                 | SPR (Election Commission)             |
| fire-bomba                 | Bomba (Fire Dept)                     |
| ambulance-mers             | MERS (Emergency Response)             |
| hospital-moh               | MOH (Health Ministry)                 |
| phcorp                     | Pharmacy Corp                         |
| passport-imigresen         | Immigration (Passport)                |
| bnm                        | Bank Negara Malaysia                  |
| money-epf                  | EPF (Employees Provident Fund)        |
| money-lhdn                 | Inland Revenue Board                  |
| mampu                      | MAMPU                                 |
| socso-perkeso              | SOCSO (Social Security)               |
| light-bulb-ipr-epu         | IPR/EPU (Innovation, Planning Unit)   |
| police-pdrm                | PDRM (Royal Malaysia Police)          |
| helping-hand-icu-jpm       | ICU/JPM (Implementation Coordination) |
| document-mof               | MOF (Finance Ministry)                |
| mini-phcorp                | Mini Pharmacy Corp                    |
| mini-mampu                 | Mini MAMPU                            |
| mini-ribbon-ntrc           | Mini National Transformation          |
| mini-bnm                   | Mini BNM                              |
| mini-dosm                  | Mini DOSM                             |
| mini-helmet-jpa            | Mini JPA                              |
| mini-document-mof          | Mini MOF                              |
| mini-train-mot             | Mini MOT                              |
| mini-ambulance-mers        | Mini MERS                             |
| mini-hospital-moh          | Mini MOH                              |
| mini-police-pdrm           | Mini Police                           |
| mini-weather-climate       | Mini Meteorology                      |
| mini-fire-bomba            | Mini Bomba                            |
| mini-flood-warning         | Mini Flood warning                    |
| mini-light-bulb-ipr-epu    | Mini Innovation/Planning              |
| mini-helping-hands-icu-jpm | Mini ICU/JPM                          |
| mini-money-kwap            | Mini KWAP                             |
| mini-ballot-spr            | Mini SPR                              |
| mini-money-epf             | Mini EPF                              |
| mini-book-moe              | Mini MOE                              |
| mini-passport-imigresen    | Mini Passport                         |
| mini-internet-mcmc         | Mini MCMC                             |
| mini-blood-pdn             | Mini Blood Bank                       |
| mini-car-jpj               | Mini JPJ                              |
| mini-identity-jpn          | Mini JPN                              |
| mini-socso                 | Mini SOCSO                            |
| mini-money-lhdn            | Mini LHDN                             |
| mini-unhcr                 | Mini UNHCR                            |
| mini-jakoa                 | Mini JAKOA                            |
| mini-website               | Mini Website                          |

---

## 7. Icon Usage Examples

### React Example

```jsx
import { Button, ButtonIcon } from '@govtechmy/myds-react/button';
import { SearchIcon } from '@govtechmy/myds-react/icon';

<Button>
  <ButtonIcon>
    <SearchIcon />
  </ButtonIcon>
  Search
</Button>;
```

### HTML Example

```html
<button aria-label="Search">
  <svg width="20" height="20" ...><!-- Search icon SVG --></svg>
  Search
</button>
```

---

## 8. Contribution & Extension

- **Request New Icon:** Submit a request via GitHub or Figma feedback.
- **Contribution Requirements:** Use 20×20 grid, 1.5px stroke, clear symbolism, outline and filled variants.
- **Review:** All submissions are checked for clarity, accessibility, and consistency.

---

## 9. References & Resources

- [MYDS Official Icon Gallery](https://design.digital.gov.my/en/icon)
- [MYDS Design Guidelines: Icon](https://design.digital.gov.my/en/docs/design/icon)
- [MYDS React Icons (npm)](https://www.npmjs.com/package/@govtechmy/myds-react)
- [MYDS Figma Kit](https://www.figma.com/design/svmWSPZarzWrJ116CQ8zpV/MYDS--Beta-)
- [MyGovEA Principles](https://mygovea.jdn.gov.my/page-prinsip-reka-bentuk/)

---

> This document ensures all icon usage in MYDS is citizen-centric, inclusive, and technically robust. For the latest updates and downloads, always refer to the official gallery above.
