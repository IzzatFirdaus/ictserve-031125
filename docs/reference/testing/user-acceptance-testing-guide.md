# User Acceptance Testing (UAT) Guide

## Overview

This guide provides comprehensive UAT scenarios, checklists, and procedures for validating the ICTServe system across all user roles and modules.

**Document Information:**
- **Version:** 1.0
- **Last Updated:** 2025-10-30
- **Trace:** D03 §5.1, §6 (Requirements), D12 §4 (UI/UX), D14 §3 (WCAG 2.2 AA)
- **Requirements:** 9.4, 9.5, 10.4, 10.5

## UAT Objectives

1. Validate that the system meets all business requirements
2. Ensure all user workflows function correctly
3. Verify accessibility compliance (WCAG 2.2 Level AA)
4. Confirm bilingual support (Bahasa Melayu and English)
5. Test cross-browser compatibility
6. Validate responsive design across devices
7. Verify performance meets targets (LCP < 2.5s, FID < 100ms, CLS < 0.1)

## User Roles

### 1. End User (Staff Member)
- Create and view helpdesk tickets
- Submit asset loan applications
- View notifications
- Access dashboard

### 2. Helpdesk Agent
- View assigned tickets
- Update ticket status
- Add comments and resolutions
- Monitor SLA compliance

### 3. Asset Loan Approver
- Review loan applications
- Approve or reject requests
- View approval dashboard
- Monitor asset availability

### 4. System Administrator
- Manage users and permissions
- View system reports
- Configure system settings
- Monitor system health

## UAT Test Scenarios

### Scenario 1: Helpdesk Ticket Creation

**User Role:** End User

**Prerequisites:**
- User is logged in
- User has permission to create tickets

**Test Steps:**
1. Navigate to Helpdesk > Create Ticket
2. Fill in ticket subject (minimum 3 characters)
3. Fill in ticket description (minimum 10 characters)
4. Select priority (Low, Medium, High, Critical)
5. Select category (Hardware, Software, Network, Other)
6. Attach file (optional, max 10MB, PDF/JPG/PNG only)
7. Click "Submit Ticket"

**Expected Results:**
- Form validates all required fields
- Real-time validation shows errors immediately
- Success message displays after submission
- User redirected to ticket detail page
- Ticket number is generated and displayed
- Email notification sent to user
- Ticket appears in user's ticket list

**Acceptance Criteria:**
- ✅ All form fields validate correctly
- ✅ Error messages are clear and helpful
- ✅ Success notification is displayed
- ✅ Ticket is created in database
- ✅ Email notification is sent
- ✅ Keyboard navigation works
- ✅ Screen reader announces form errors
- ✅ Works in all supported browsers
- ✅ Responsive on mobile devices

### Scenario 2: Asset Loan Application

**User Role:** End User

**Prerequisites:**
- User is logged in
- Assets are available in the system

**Test Steps:**
1. Navigate to Asset Loan > Request Asset
2. **Step 1:** Select asset from dropdown
3. View asset details and availability
4. Click "Next"
5. **Step 2:** Select start date (must be future date)
6. Select end date (must be after start date)
7. Click "Next"
8. **Step 3:** Enter purpose (minimum 10 characters)
9. Select approval workflow
10. Click "Next"
11. **Step 4:** Review all details
12. Check declaration checkbox
13. Click "Submit Request"

**Expected Results:**
- Multi-step wizard displays progress indicator
- Each step validates before proceeding
- Asset availability is checked in real-time
- Date conflicts are detected
- Success message displays after submission
- Application number is generated
- Email notification sent to user and approver
- Application appears in user's request list

**Acceptance Criteria:**
- ✅ All steps validate correctly
- ✅ Progress indicator shows current step
- ✅ Back/Next buttons work correctly
- ✅ Asset availability is accurate
- ✅ Date validation prevents conflicts
- ✅ Success notification is displayed
- ✅ Application is created in database
- ✅ Email notifications are sent
- ✅ Keyboard navigation works
- ✅ Screen reader announces step changes
- ✅ Works in all supported browsers
- ✅ Responsive on mobile devices

### Scenario 3: Ticket Status Update (Agent)

**User Role:** Helpdesk Agent

**Prerequisites:**
- Agent is logged in
- Tickets are assigned to agent

**Test Steps:**
1. Navigate to Agent Dashboard
2. View list of assigned tickets
3. Click on a ticket to view details
4. Review ticket information
5. Add internal comment (optional)
6. Update ticket status (In Progress, Resolved, Closed)
7. Add resolution notes (if resolving)
8. Click "Update Status"

**Expected Results:**
- Agent dashboard shows assigned tickets
- Ticket details display all information
- Status update validates required fields
- Success message displays after update
- Email notification sent to user
- Ticket status updated in database
- Activity timeline shows status change
- SLA timer updates accordingly

**Acceptance Criteria:**
- ✅ Dashboard displays correct tickets
- ✅ Ticket details are complete
- ✅ Status update validates correctly
- ✅ Success notification is displayed
- ✅ Email notification is sent
- ✅ Database is updated
- ✅ Activity timeline is accurate
- ✅ SLA timer is correct
- ✅ Keyboard navigation works
- ✅ Screen reader announces updates
- ✅ Works in all supported browsers

### Scenario 4: Loan Application Approval

**User Role:** Asset Loan Approver

**Prerequisites:**
- Approver is logged in
- Loan applications are pending approval

**Test Steps:**
1. Navigate to Approver Dashboard
2. View list of pending approvals
3. Click on an application to view details
4. Review application information
5. Check asset availability
6. Click "Approve" or "Reject"
7. Enter approval/rejection comments
8. Click "Confirm"

**Expected Results:**
- Approver dashboard shows pending applications
- Application details display all information
- Approval/rejection validates comments
- Success message displays after action
- Email notification sent to applicant
- Application status updated in database
- Asset availability updated (if approved)
- Approval history recorded

**Acceptance Criteria:**
- ✅ Dashboard displays correct applications
- ✅ Application details are complete
- ✅ Approval/rejection validates correctly
- ✅ Success notification is displayed
- ✅ Email notification is sent
- ✅ Database is updated
- ✅ Asset availability is updated
- ✅ Approval history is recorded
- ✅ Keyboard navigation works
- ✅ Screen reader announces actions
- ✅ Works in all supported browsers

## UAT Checklists

### Functional Testing Checklist

#### Helpdesk Module
- [ ] Create ticket with all required fields
- [ ] Create ticket with optional attachments
- [ ] View ticket list with filters
- [ ] Search tickets by keyword
- [ ] View ticket details
- [ ] Add comments to ticket
- [ ] Update ticket status
- [ ] Resolve ticket with notes
- [ ] Close ticket
- [ ] View SLA countdown
- [ ] Receive email notifications
- [ ] View agent dashboard
- [ ] View ticket analytics

#### Asset Loan Module
- [ ] Create loan application (multi-step)
- [ ] Check asset availability
- [ ] View application list with filters
- [ ] Search applications by keyword
- [ ] View application details
- [ ] Approve application
- [ ] Reject application
- [ ] View approval dashboard
- [ ] View asset availability calendar
- [ ] Receive email notifications
- [ ] View application analytics

#### Common Features
- [ ] Login with valid credentials
- [ ] Logout successfully
- [ ] View unified dashboard
- [ ] Switch language (EN/MS)
- [ ] View notifications
- [ ] Mark notifications as read
- [ ] Update user profile
- [ ] Change password
- [ ] View help documentation

### Accessibility Testing Checklist

- [ ] Skip links work correctly
- [ ] All images have alt text
- [ ] Form labels are properly associated
- [ ] ARIA landmarks are present
- [ ] Heading hierarchy is correct
- [ ] Color contrast meets WCAG AA (4.5:1)
- [ ] Keyboard navigation works
- [ ] Focus indicators are visible
- [ ] ARIA live regions announce changes
- [ ] Form validation errors are announced
- [ ] Buttons have accessible names
- [ ] Links have descriptive text
- [ ] Tables have proper headers
- [ ] Modals trap focus
- [ ] Touch targets are minimum 44x44px
- [ ] Language attribute is set
- [ ] Page titles are descriptive
- [ ] Status messages are announced
- [ ] Error messages are clear
- [ ] Loading states are announced

### Cross-Browser Testing Checklist

#### Chrome (Latest)
- [ ] Homepage renders correctly
- [ ] Login works
- [ ] Forms submit correctly
- [ ] JavaScript functions work
- [ ] CSS layouts display correctly
- [ ] Responsive design works

#### Firefox (Latest)
- [ ] Homepage renders correctly
- [ ] Login works
- [ ] Forms submit correctly
- [ ] JavaScript functions work
- [ ] CSS layouts display correctly
- [ ] Responsive design works

#### Safari (Latest)
- [ ] Homepage renders correctly
- [ ] Login works
- [ ] Forms submit correctly
- [ ] JavaScript functions work
- [ ] CSS layouts display correctly
- [ ] Responsive design works

#### Edge (Latest)
- [ ] Homepage renders correctly
- [ ] Login works
- [ ] Forms submit correctly
- [ ] JavaScript functions work
- [ ] CSS layouts display correctly
- [ ] Responsive design works

### Responsive Design Testing Checklist

#### Mobile (320px - 767px)
- [ ] Navigation menu works
- [ ] Forms are usable
- [ ] Tables are responsive
- [ ] Touch targets are adequate
- [ ] Text is readable
- [ ] Images scale correctly

#### Tablet (768px - 1023px)
- [ ] Navigation menu works
- [ ] Forms are usable
- [ ] Tables are responsive
- [ ] Touch targets are adequate
- [ ] Text is readable
- [ ] Images scale correctly

#### Desktop (1024px+)
- [ ] Navigation menu works
- [ ] Forms are usable
- [ ] Tables display correctly
- [ ] Layout is optimal
- [ ] Text is readable
- [ ] Images display correctly

### Performance Testing Checklist

- [ ] LCP < 2.5 seconds
- [ ] FID < 100 milliseconds
- [ ] CLS < 0.1
- [ ] TTFB < 600 milliseconds
- [ ] Page load time < 3 seconds
- [ ] Lighthouse Performance score ≥ 90
- [ ] Lighthouse Accessibility score = 100
- [ ] Lighthouse Best Practices score = 100
- [ ] Lighthouse SEO score ≥ 90

### Bilingual Support Testing Checklist

- [ ] Language switcher is visible
- [ ] Language switcher works correctly
- [ ] All text is translated (EN)
- [ ] All text is translated (MS)
- [ ] No hardcoded text visible
- [ ] Date formats are localized
- [ ] Number formats are localized
- [ ] Currency formats are localized
- [ ] Error messages are translated
- [ ] Success messages are translated
- [ ] Email notifications are translated
- [ ] PDF reports are translated

## UAT Test Data

### Test Users

```
End User:
- Email: user@example.com
- Password: Password123!
- Role: Staff Member

Helpdesk Agent:
- Email: agent@example.com
- Password: Password123!
- Role: Helpdesk Agent

Asset Loan Approver:
- Email: approver@example.com
- Password: Password123!
- Role: Approver

System Administrator:
- Email: admin@example.com
- Password: Password123!
- Role: Administrator
```

### Test Assets

```
Asset 1:
- Name: Dell Latitude 5420 Laptop
- Category: Laptop
- Status: Available
- Location: BPM Office

Asset 2:
- Name: HP LaserJet Pro Printer
- Category: Printer
- Status: Available
- Location: Meeting Room 1

Asset 3:
- Name: Logitech Webcam C920
- Category: Webcam
- Status: In Use
- Location: Training Room
```

## UAT Execution Process

### 1. Preparation Phase
- Review UAT scenarios and checklists
- Set up test environment
- Create test users and data
- Prepare test devices and browsers
- Schedule UAT sessions with stakeholders

### 2. Execution Phase
- Execute test scenarios in order
- Document results for each test
- Capture screenshots of issues
- Record video of critical workflows
- Note any deviations from expected results

### 3. Reporting Phase
- Compile test results
- Categorize issues by severity
- Create issue tickets for defects
- Generate UAT summary report
- Present findings to stakeholders

### 4. Resolution Phase
- Prioritize issues for fixing
- Retest fixed issues
- Verify all critical issues resolved
- Obtain sign-off from stakeholders

## Issue Severity Levels

### Critical
- System crash or data loss
- Security vulnerability
- Complete feature failure
- Accessibility blocker (WCAG A)

### High
- Major feature malfunction
- Incorrect data display
- Performance issue (> 5s load time)
- Accessibility issue (WCAG AA)

### Medium
- Minor feature issue
- UI inconsistency
- Usability problem
- Translation missing

### Low
- Cosmetic issue
- Minor text error
- Enhancement request
- Nice-to-have feature

## UAT Sign-Off Criteria

The system is ready for production when:

1. ✅ All critical and high severity issues are resolved
2. ✅ All UAT scenarios pass successfully
3. ✅ Accessibility compliance is verified (WCAG 2.2 Level AA)
4. ✅ Cross-browser compatibility is confirmed
5. ✅ Responsive design is validated
6. ✅ Performance targets are met
7. ✅ Bilingual support is complete
8. ✅ Security testing is passed
9. ✅ Stakeholders provide written approval
10. ✅ User documentation is complete

## UAT Tools and Resources

### Testing Tools
- **Browser DevTools:** Chrome, Firefox, Safari, Edge
- **Accessibility:** axe DevTools, WAVE, Lighthouse
- **Performance:** Lighthouse, WebPageTest, Chrome DevTools
- **Screen Readers:** NVDA, JAWS, VoiceOver
- **Mobile Testing:** BrowserStack, real devices

### Documentation
- UAT Test Plan
- Test Case Templates
- Issue Report Template
- UAT Summary Report Template
- Sign-Off Form

### Support
- UAT Coordinator: [Name]
- Technical Support: [Email]
- Issue Tracking: [System URL]
- Documentation: [Wiki URL]

## Appendix

### A. Test Case Template

```markdown
# Test Case: [ID] - [Title]

**Module:** [Helpdesk/Asset Loan/Common]
**User Role:** [End User/Agent/Approver/Admin]
**Priority:** [Critical/High/Medium/Low]

## Prerequisites
- [List prerequisites]

## Test Steps
1. [Step 1]
2. [Step 2]
3. [Step 3]

## Expected Results
- [Expected result 1]
- [Expected result 2]

## Actual Results
- [To be filled during testing]

## Status
- [ ] Pass
- [ ] Fail
- [ ] Blocked

## Notes
[Any additional notes]
```

### B. Issue Report Template

```markdown
# Issue Report: [ID] - [Title]

**Severity:** [Critical/High/Medium/Low]
**Module:** [Helpdesk/Asset Loan/Common]
**Browser:** [Chrome/Firefox/Safari/Edge]
**Device:** [Desktop/Tablet/Mobile]

## Description
[Detailed description of the issue]

## Steps to Reproduce
1. [Step 1]
2. [Step 2]
3. [Step 3]

## Expected Behavior
[What should happen]

## Actual Behavior
[What actually happens]

## Screenshots
[Attach screenshots]

## Additional Information
- User Role: [Role]
- Environment: [UAT/Staging]
- Date: [Date]
- Tester: [Name]
```

### C. UAT Summary Report Template

```markdown
# UAT Summary Report

**Project:** ICTServe System
**UAT Period:** [Start Date] - [End Date]
**Report Date:** [Date]

## Executive Summary
[Brief overview of UAT results]

## Test Coverage
- Total Test Cases: [Number]
- Passed: [Number] ([Percentage]%)
- Failed: [Number] ([Percentage]%)
- Blocked: [Number] ([Percentage]%)

## Issues Summary
- Critical: [Number]
- High: [Number]
- Medium: [Number]
- Low: [Number]

## Key Findings
1. [Finding 1]
2. [Finding 2]
3. [Finding 3]

## Recommendations
1. [Recommendation 1]
2. [Recommendation 2]

## Sign-Off Status
- [ ] Ready for Production
- [ ] Requires Additional Testing
- [ ] Not Ready for Production

## Stakeholder Approval
- Product Owner: _________________ Date: _______
- Technical Lead: _________________ Date: _______
- QA Lead: _________________ Date: _______
```

---

**Document Control:**
- **Version:** 1.0
- **Author:** ICTServe Development Team
- **Approved By:** [Name]
- **Next Review Date:** [Date]
