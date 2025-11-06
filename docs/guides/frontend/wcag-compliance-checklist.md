# WCAG 2.2 Level AA Compliance Checklist
## Updated ICT Asset Loan Module

**Document Version**: 1.0.0  
**Last Updated**: 2025-11-04  
**Author**: Pasukan BPM MOTAC  
**Standards**: WCAG 2.2 Level AA, ISO/IEC 40500  

### Requirements Traceability

- **D03-FR-006.1**: Accessibility Requirements
- **D03-FR-006.2**: Keyboard Navigation
- **D03-FR-006.3**: Screen Reader Support
- **D04 §6.1**: Accessibility Compliance
- **D12 §9**: WCAG 2.2 AA Compliance
- **D14 §9**: Accessibility Standards

---

## 1. Perceivable (WCAG Principle 1)

### 1.1 Text Alternatives (SC 1.1.1 - Level A)

- [ ] **Images**: All informative images have meaningful alt text
- [ ] **Decorative Images**: Decorative images have empty alt="" or role="presentation"
- [ ] **Complex Images**: Charts/graphs have detailed descriptions
- [ ] **Icons**: Icon buttons have accessible names via aria-label
- [ ] **Logos**: MOTAC logo has appropriate alt text

**Test Method**: Manual inspection + automated tools  
**Tools**: Screen reader, axe-core, WAVE

### 1.2 Time-based Media (SC 1.2.1-1.2.9)

- [ ] **Audio Content**: Audio-only content has text transcripts (if applicable)
- [ ] **Video Content**: Videos have captions and audio descriptions (if applicable)
- [ ] **Live Content**: Live audio/video has real-time captions (if applicable)

**Test Method**: Manual review  
**Note**: Currently not applicable to loan module

### 1.3 Adaptable (SC 1.3.1-1.3.6)

#### 1.3.1 Info and Relationships (Level A)

- [ ] **Semantic HTML**: Proper use of headings (h1-h6), lists, tables
- [ ] **Form Labels**: All form inputs have associated labels
- [ ] **Table Headers**: Data tables use th elements with scope attributes
- [ ] **Fieldsets**: Related form controls grouped with fieldset/legend
- [ ] **ARIA Landmarks**: page regions marked with appropriate roles

**Test Method**: Code inspection + screen reader testing

#### 1.3.2 Meaningful Sequence (Level A)

- [ ] **Reading Order**: Content order makes sense when CSS is disabled
- [ ] **Tab Order**: Keyboard navigation follows logical sequence
- [ ] **Focus Management**: Focus moves predictably through interface

**Test Method**: Disable CSS, keyboard navigation testing

#### 1.3.3 Sensory Characteristics (Level A)

- [ ] **Instructions**: Don't rely solely on shape, size, position, or sound
- [ ] **Color Independence**: Information not conveyed by color alone
- [ ] **Visual Cues**: Multiple ways to identify interactive elements

**Test Method**: Manual inspection, color blindness simulation

#### 1.3.4 Orientation (Level AA)

- [ ] **Device Orientation**: Content works in both portrait and landscape
- [ ] **Responsive Design**: Layout adapts to different screen sizes
- [ ] **Mobile Support**: Touch targets minimum 44×44px

**Test Method**: Device testing, responsive design tools

#### 1.3.5 Identify Input Purpose (Level AA)

- [ ] **Autocomplete**: Form fields use appropriate autocomplete attributes
- [ ] **Input Types**: Proper input types (email, tel, date, etc.)
- [ ] **Field Purpose**: Clear indication of expected input format

**Test Method**: Code inspection, form testing

### 1.4 Distinguishable (SC 1.4.1-1.4.13)

#### 1.4.1 Use of Color (Level A)

- [ ] **Color Independence**: Information not conveyed by color alone
- [ ] **Status Indicators**: Multiple ways to show status (icons + color)
- [ ] **Error Messages**: Errors indicated by text, not just color

**Test Method**: Grayscale testing, color blindness simulation

#### 1.4.3 Contrast (Minimum) (Level AA)

- [ ] **Text Contrast**: Minimum 4.5:1 ratio for normal text
- [ ] **Large Text**: Minimum 3:1 ratio for large text (18pt+ or 14pt+ bold)
- [ ] **Compliant Colors**: Using approved MOTAC color palette
  - Primary #0056b3 (6.8:1 contrast)
  - Success #198754 (4.9:1 contrast)
  - Warning #ff8c00 (4.5:1 contrast)
  - Danger #b50c0c (8.2:1 contrast)

**Test Method**: Color contrast analyzer, automated tools

#### 1.4.4 Resize Text (Level AA)

- [ ] **Text Scaling**: Text can be resized to 200% without horizontal scrolling
- [ ] **Layout Integrity**: Layout remains functional when text is enlarged
- [ ] **Responsive Typography**: Text scales appropriately on mobile devices

**Test Method**: Browser zoom testing, mobile device testing

#### 1.4.5 Images of Text (Level AA)

- [ ] **Text Images**: Avoid images of text except for logos
- [ ] **Customizable Text**: Text can be styled by user preferences
- [ ] **Vector Graphics**: Use SVG or icon fonts instead of text images

**Test Method**: Manual inspection

#### 1.4.10 Reflow (Level AA)

- [ ] **Horizontal Scrolling**: No horizontal scrolling at 320px width
- [ ] **Vertical Scrolling**: No vertical scrolling at 256px height
- [ ] **Content Reflow**: Content reflows without loss of information

**Test Method**: Responsive design testing, mobile simulation

#### 1.4.11 Non-text Contrast (Level AA)

- [ ] **UI Components**: Minimum 3:1 contrast for UI components
- [ ] **Focus Indicators**: Minimum 3:1 contrast for focus indicators
- [ ] **Interactive Elements**: Clear visual distinction for interactive elements

**Test Method**: Color contrast analyzer for UI elements

#### 1.4.12 Text Spacing (Level AA)

- [ ] **Line Height**: Minimum 1.5 times font size
- [ ] **Paragraph Spacing**: Minimum 2 times font size
- [ ] **Letter Spacing**: Minimum 0.12 times font size
- [ ] **Word Spacing**: Minimum 0.16 times font size

**Test Method**: CSS override testing

#### 1.4.13 Content on Hover or Focus (Level AA)

- [ ] **Dismissible**: Hover/focus content can be dismissed
- [ ] **Hoverable**: Hover content remains visible when hovering over it
- [ ] **Persistent**: Content remains visible until dismissed or invalid

**Test Method**: Interactive testing of tooltips, dropdowns

---

## 2. Operable (WCAG Principle 2)

### 2.1 Keyboard Accessible (SC 2.1.1-2.1.4)

#### 2.1.1 Keyboard (Level A)

- [ ] **Full Keyboard Access**: All functionality available via keyboard
- [ ] **No Keyboard Traps**: Users can navigate away from any component
- [ ] **Standard Navigation**: Tab, Shift+Tab, Arrow keys, Enter, Space work as expected
- [ ] **Custom Controls**: Custom interactive elements support keyboard

**Test Method**: Keyboard-only navigation testing

#### 2.1.2 No Keyboard Trap (Level A)

- [ ] **Focus Movement**: Focus can move away from any component
- [ ] **Modal Dialogs**: Focus trapped within modals, but can be closed
- [ ] **Dropdown Menus**: Can navigate in and out of dropdown menus
- [ ] **Form Controls**: No infinite loops in form navigation

**Test Method**: Keyboard navigation testing

#### 2.1.4 Character Key Shortcuts (Level A)

- [ ] **Single Key Shortcuts**: Avoid single character key shortcuts
- [ ] **Modifier Keys**: Use Ctrl/Alt/Cmd combinations for shortcuts
- [ ] **Customizable**: Allow users to disable or remap shortcuts

**Test Method**: Keyboard shortcut testing

### 2.2 Enough Time (SC 2.2.1-2.2.6)

#### 2.2.1 Timing Adjustable (Level A)

- [ ] **Session Timeouts**: Warn users before session expires
- [ ] **Time Limits**: Allow users to extend or disable time limits
- [ ] **Auto-refresh**: Avoid automatic page refreshes

**Test Method**: Session timeout testing

#### 2.2.2 Pause, Stop, Hide (Level A)

- [ ] **Moving Content**: Provide controls to pause/stop animations
- [ ] **Auto-updating**: Allow users to control auto-updating content
- [ ] **Blinking Content**: Avoid blinking or flashing content

**Test Method**: Animation and auto-update testing

### 2.3 Seizures and Physical Reactions (SC 2.3.1-2.3.3)

#### 2.3.1 Three Flashes or Below Threshold (Level A)

- [ ] **Flash Frequency**: No content flashes more than 3 times per second
- [ ] **Flash Area**: Large flashing areas avoided
- [ ] **Photosensitive**: No content likely to cause seizures

**Test Method**: Visual inspection, automated tools

### 2.4 Navigable (SC 2.4.1-2.4.13)

#### 2.4.1 Bypass Blocks (Level A)

- [ ] **Skip Links**: "Skip to main content" link provided
- [ ] **Navigation Bypass**: Way to skip repetitive navigation
- [ ] **Keyboard Shortcuts**: Quick navigation for keyboard users

**Test Method**: Keyboard navigation testing

#### 2.4.2 Page Titled (Level A)

- [ ] **Unique Titles**: Each page has unique, descriptive title
- [ ] **Title Format**: Consistent title format across site
- [ ] **Context**: Titles describe page purpose or content

**Test Method**: Manual inspection of page titles

#### 2.4.3 Focus Order (Level A)

- [ ] **Logical Order**: Focus order follows meaningful sequence
- [ ] **Visual Order**: Focus order matches visual layout
- [ ] **Predictable**: Focus behavior is consistent across pages

**Test Method**: Keyboard navigation testing

#### 2.4.4 Link Purpose (Level A)

- [ ] **Descriptive Links**: Link text describes destination or purpose
- [ ] **Context**: Link purpose clear from text or surrounding context
- [ ] **Unique Links**: Different destinations have different link text

**Test Method**: Screen reader testing, manual inspection

#### 2.4.5 Multiple Ways (Level AA)

- [ ] **Navigation Methods**: Multiple ways to find pages (menu, search, sitemap)
- [ ] **Search Function**: Site search available if applicable
- [ ] **Breadcrumbs**: Breadcrumb navigation where appropriate

**Test Method**: Site navigation testing

#### 2.4.6 Headings and Labels (Level AA)

- [ ] **Descriptive Headings**: Headings describe topic or purpose
- [ ] **Descriptive Labels**: Form labels describe purpose or content
- [ ] **Heading Hierarchy**: Proper heading structure (h1-h6)

**Test Method**: Screen reader testing, code inspection

#### 2.4.7 Focus Visible (Level AA)

- [ ] **Focus Indicators**: Visible focus indicators on all interactive elements
- [ ] **Contrast**: Focus indicators meet 3:1 contrast ratio
- [ ] **Consistent**: Focus indicators consistent across site
- [ ] **Clear**: Focus indicators clearly show which element has focus

**Test Method**: Keyboard navigation testing, visual inspection

#### 2.4.11 Focus Not Obscured (Minimum) (Level AA)

- [ ] **Visible Focus**: Focused element not completely hidden by other content
- [ ] **Partial Obscuring**: Minimal obscuring of focused elements acceptable
- [ ] **Sticky Headers**: Sticky elements don't hide focused content

**Test Method**: Keyboard navigation with sticky elements

### 2.5 Input Modalities (SC 2.5.1-2.5.8)

#### 2.5.1 Pointer Gestures (Level A)

- [ ] **Simple Gestures**: All functionality available with single pointer
- [ ] **Alternative Methods**: Alternatives to complex gestures
- [ ] **Path-based Gestures**: Avoid gestures requiring specific paths

**Test Method**: Touch device testing

#### 2.5.2 Pointer Cancellation (Level A)

- [ ] **Down Event**: Avoid using down-event for activation
- [ ] **Abort/Undo**: Provide way to abort or undo actions
- [ ] **Up Event**: Use up-event for activation when possible

**Test Method**: Mouse and touch testing

#### 2.5.3 Label in Name (Level A)

- [ ] **Accessible Name**: Accessible name includes visible label text
- [ ] **Speech Recognition**: Voice control users can activate by visible label
- [ ] **Consistent Naming**: Consistent naming between visual and programmatic labels

**Test Method**: Screen reader testing, voice control testing

#### 2.5.4 Motion Actuation (Level A)

- [ ] **Alternative Input**: Alternatives to motion-based input
- [ ] **Disable Motion**: Users can disable motion actuation
- [ ] **Accidental Activation**: Prevent accidental motion activation

**Test Method**: Device motion testing

#### 2.5.5 Target Size (Minimum) (Level AA)

- [ ] **44px Minimum**: Interactive elements minimum 44×44px
- [ ] **Touch Targets**: Adequate spacing between touch targets
- [ ] **Mobile Optimization**: Optimized for mobile touch interaction

**Test Method**: Mobile device testing, measurement tools

#### 2.5.7 Dragging Movements (Level AA)

- [ ] **Alternative Methods**: Alternatives to dragging functionality
- [ ] **Single Pointer**: Dragging functionality available with single pointer
- [ ] **Accessibility**: Dragging accessible to users with disabilities

**Test Method**: Assistive technology testing

#### 2.5.8 Target Size (Enhanced) (Level AAA - Optional)

- [ ] **24px Minimum**: Interactive elements minimum 24×24px (enhanced)
- [ ] **Spacing**: Adequate spacing between small targets

**Test Method**: Detailed measurement, mobile testing

---

## 3. Understandable (WCAG Principle 3)

### 3.1 Readable (SC 3.1.1-3.1.6)

#### 3.1.1 Language of Page (Level A)

- [ ] **Page Language**: HTML lang attribute set correctly
- [ ] **Primary Language**: Default language identified (ms or en)
- [ ] **Language Declaration**: Language declared in HTML element

**Test Method**: Code inspection

#### 3.1.2 Language of Parts (Level AA)

- [ ] **Language Changes**: Changes in language marked with lang attribute
- [ ] **Bilingual Content**: Mixed language content properly marked
- [ ] **Language Switcher**: Language switching functionality accessible

**Test Method**: Code inspection, screen reader testing

### 3.2 Predictable (SC 3.2.1-3.2.5)

#### 3.2.1 On Focus (Level A)

- [ ] **No Context Change**: Focus doesn't trigger unexpected context changes
- [ ] **Predictable Focus**: Focus behavior is predictable
- [ ] **User Control**: Users control when context changes occur

**Test Method**: Keyboard navigation testing

#### 3.2.2 On Input (Level A)

- [ ] **No Auto-Submit**: Form inputs don't automatically submit forms
- [ ] **Predictable Input**: Input behavior is predictable
- [ ] **User Initiated**: Changes only occur when user initiates them

**Test Method**: Form interaction testing

#### 3.2.3 Consistent Navigation (Level AA)

- [ ] **Navigation Order**: Navigation components in consistent order
- [ ] **Menu Structure**: Consistent menu structure across pages
- [ ] **Navigation Labels**: Consistent labeling of navigation items

**Test Method**: Multi-page navigation testing

#### 3.2.4 Consistent Identification (Level AA)

- [ ] **Component Consistency**: Same functionality identified consistently
- [ ] **Icon Consistency**: Same icons used for same functions
- [ ] **Label Consistency**: Same labels for same functions

**Test Method**: Cross-page component comparison

#### 3.2.6 Consistent Help (Level A)

- [ ] **Help Location**: Help mechanisms in consistent locations
- [ ] **Help Access**: Consistent way to access help
- [ ] **Help Format**: Consistent help format and presentation

**Test Method**: Help system testing

### 3.3 Input Assistance (SC 3.3.1-3.3.9)

#### 3.3.1 Error Identification (Level A)

- [ ] **Error Detection**: Errors automatically detected and identified
- [ ] **Error Description**: Errors described in text
- [ ] **Error Location**: Location of errors clearly indicated

**Test Method**: Form validation testing

#### 3.3.2 Labels or Instructions (Level A)

- [ ] **Form Labels**: All form inputs have labels or instructions
- [ ] **Required Fields**: Required fields clearly indicated
- [ ] **Input Format**: Expected input format described

**Test Method**: Form testing, screen reader testing

#### 3.3.3 Error Suggestion (Level AA)

- [ ] **Correction Suggestions**: Suggestions provided for errors when possible
- [ ] **Helpful Errors**: Error messages help users correct mistakes
- [ ] **Security Exceptions**: No suggestions for security-sensitive fields

**Test Method**: Form validation testing

#### 3.3.4 Error Prevention (Legal, Financial, Data) (Level AA)

- [ ] **Confirmation**: Important submissions require confirmation
- [ ] **Review**: Users can review and correct before submission
- [ ] **Reversible**: Submissions are reversible when possible

**Test Method**: Form submission testing

#### 3.3.7 Redundant Entry (Level A)

- [ ] **Auto-fill**: Previously entered information auto-filled when possible
- [ ] **Session Data**: Information persists within session
- [ ] **User Choice**: Users can choose to re-enter information

**Test Method**: Multi-step form testing

#### 3.3.8 Accessible Authentication (Minimum) (Level AA)

- [ ] **Cognitive Function**: Authentication doesn't rely on cognitive function tests
- [ ] **Alternative Methods**: Alternative authentication methods available
- [ ] **Password Managers**: Compatible with password managers

**Test Method**: Authentication testing

---

## 4. Robust (WCAG Principle 4)

### 4.1 Compatible (SC 4.1.1-4.1.3)

#### 4.1.1 Parsing (Level A) - Deprecated in WCAG 2.2

- [ ] **Valid HTML**: HTML validates according to specification
- [ ] **Unique IDs**: All IDs are unique within page
- [ ] **Proper Nesting**: Elements properly nested

**Test Method**: HTML validator

#### 4.1.2 Name, Role, Value (Level A)

- [ ] **Accessible Names**: All UI components have accessible names
- [ ] **Roles**: Roles properly defined for custom components
- [ ] **States**: States and properties programmatically determinable

**Test Method**: Screen reader testing, accessibility inspector

#### 4.1.3 Status Messages (Level AA)

- [ ] **Status Announcements**: Status messages announced to screen readers
- [ ] **Live Regions**: Appropriate use of ARIA live regions
- [ ] **Success Messages**: Success confirmations accessible

**Test Method**: Screen reader testing

---

## Testing Tools and Methods

### Automated Testing Tools

1. **axe-core**: Browser extension for automated accessibility testing
2. **WAVE**: Web accessibility evaluation tool
3. **Lighthouse**: Google's accessibility audit tool
4. **Pa11y**: Command-line accessibility testing tool
5. **Custom Command**: `php artisan accessibility:validate`

### Manual Testing Tools

1. **Screen Readers**: NVDA (Windows), JAWS (Windows), VoiceOver (macOS)
2. **Keyboard Testing**: Tab, Shift+Tab, Arrow keys, Enter, Space, Escape
3. **Color Contrast**: WebAIM Contrast Checker, Colour Contrast Analyser
4. **Mobile Testing**: Real devices, browser dev tools
5. **Browser Testing**: Chrome, Firefox, Safari, Edge

### Browser Extensions

- **axe DevTools**: Automated accessibility testing
- **WAVE**: Visual accessibility evaluation
- **Accessibility Insights**: Microsoft's accessibility testing tool
- **Colour Contrast Analyser**: Real-time contrast checking
- **HeadingsMap**: Heading structure visualization

### Screen Reader Testing Commands

- **NVDA**: Insert + F7 (elements list), Insert + T (title), Insert + H (headings)
- **JAWS**: Insert + F6 (headings list), Insert + F5 (form fields), Insert + F7 (links)
- **VoiceOver**: VO + U (rotor), VO + Command + H (headings), VO + Command + L (links)

---

## Compliance Verification Process

### Phase 1: Automated Testing

1. Run `php artisan accessibility:validate` command
2. Use browser extensions (axe, WAVE) on all pages
3. Run Lighthouse accessibility audit
4. Execute PHPUnit accessibility tests: `php artisan test tests/Feature/Accessibility/`
5. Run Dusk browser tests: `php artisan dusk tests/Browser/AccessibilityTest.php`

### Phase 2: Manual Testing

1. **Keyboard Navigation**: Test all functionality with keyboard only
2. **Screen Reader**: Test with NVDA/JAWS/VoiceOver
3. **Color Contrast**: Verify all color combinations meet standards
4. **Mobile Testing**: Test on real mobile devices
5. **Zoom Testing**: Test at 200% zoom level

### Phase 3: User Testing

1. **Assistive Technology Users**: Test with actual users of screen readers
2. **Keyboard-only Users**: Test with users who rely on keyboard navigation
3. **Low Vision Users**: Test with users who use magnification software
4. **Cognitive Disabilities**: Test with users who have cognitive disabilities

### Phase 4: Documentation

1. **Test Results**: Document all test results and findings
2. **Remediation Plan**: Create plan for addressing any violations
3. **Compliance Statement**: Create accessibility compliance statement
4. **User Guide**: Create accessibility user guide

---

## Compliance Statement Template

```
ICTServe Updated Loan Module - Accessibility Compliance Statement

This application has been designed and tested to meet WCAG 2.2 Level AA standards.

Compliance Status: [COMPLIANT/PARTIALLY COMPLIANT/NON-COMPLIANT]
Last Tested: [DATE]
Testing Method: Automated tools, manual testing, user testing
Standards: WCAG 2.2 Level AA, ISO/IEC 40500

Known Issues: [LIST ANY REMAINING ISSUES]
Planned Fixes: [REMEDIATION TIMELINE]

Contact: [ACCESSIBILITY CONTACT INFORMATION]
```

---

## Maintenance and Monitoring

### Regular Testing Schedule

- **Weekly**: Automated testing with CI/CD pipeline
- **Monthly**: Manual spot checks on key user journeys
- **Quarterly**: Comprehensive manual testing
- **Annually**: Full accessibility audit with external experts

### Continuous Monitoring

- Integrate accessibility testing into development workflow
- Train developers on accessibility best practices
- Regular accessibility reviews in code reviews
- User feedback collection and response process

### Updates and Changes

- Test accessibility impact of all UI changes
- Update this checklist as WCAG standards evolve
- Maintain accessibility documentation
- Regular training updates for development team

---

**Document Control**

- **Version**: 1.0.0
- **Approved By**: [APPROVAL AUTHORITY]
- **Next Review**: [DATE]
- **Distribution**: Development Team, QA Team, Project Management
