# Missing Translation Keys Report

**Generated**: 2025-01-22  
**Total Missing Keys**: 206  
**Status**: ✅ `auth.logging_in` added

## Summary

Systematic scan of all Blade templates found 206 missing translation keys across multiple namespaces. These keys are referenced in templates but not defined in `resources/lang/ms/*.php` or `resources/lang/en/*.php` files.

## Missing Keys by Namespace

### Common (common.php)
- `common.trend`
- `common.created_at`
- `common.to`
- `common.motac_full_name`
- `common.all_rights_reserved`
- `common.recent_activity`
- `common.back_to_home`
- `common.site_home`
- `common.user`
- `common.date_from`
- `common.date_to`
- `common.unknown`
- `common.searching`

### Loans (loans.php)
- `loans.number`
- `loans.approval`
- `loans.no_submissions`
- `loans.return_date`
- `loans.return_condition`
- `loans.applicant`
- `loans.loan_history`
- `loans.submit_application_description`
- `loans.wizard_progress`
- `loans.step_1_title`
- `loans.step_2_title`
- `loans.step_3_title`
- `loans.grade_41`, `loans.grade_44`, `loans.grade_48`, `loans.grade_52`, `loans.grade_54`
- `loans.select_division`
- `loans.search_assets`
- `loans.search_placeholder`
- `loans.available_assets`
- `loans.no_assets_found`
- `loans.availability_status`
- `loans.available`
- `loans.not_available`
- `loans.location`
- `loans.confirmation`
- `loans.next_steps`
- `loans.next_step_1`, `loans.next_step_2`, `loans.next_step_3`
- `loans.confirmation_email_sent`
- `loans.submit_another`
- `loans.return_home`
- `loans.form_navigation`
- `loans.previous`
- `loans.next`
- `loans.submitting`

### Helpdesk (helpdesk.php)
- `helpdesk.step_navigation`
- `helpdesk.no_divisions_help`
- `helpdesk.terms_of_service`

### Profile (profile.php)
- `profile.updated_successfully`
- `profile.language_updated`
- `profile.subtitle`
- `profile.completeness`
- `profile.system_information`
- `profile.system_information_desc`
- `profile.read_only`
- `profile.department`
- `profile.personal_information`
- `profile.personal_information_desc`
- `profile.mobile`
- `profile.bio`
- `profile.bio_placeholder`
- `profile.characters`
- `profile.change_password`
- `profile.change_password_desc`
- `profile.updating`
- `profile.language_preference`
- `profile.language_preference_desc`
- `profile.save_language`
- `profile.notification_preferences`
- `profile.notification_preferences_desc`
- `profile.notif_ticket_updates`
- `profile.notif_ticket_assignments`
- `profile.notif_ticket_comments`
- `profile.notif_sla_alerts`
- `profile.notif_loan_updates`
- `profile.notif_loan_approvals`
- `profile.notif_loan_reminders`
- `profile.notif_system_announcements`
- `profile.save_preferences`
- `profile.preferences_description`
- `profile.security_description`

### Portal (portal.php)
- `portal.profile_title`
- `portal.filter_activity`

### Auth (auth.php)
- ✅ `auth.login` (exists)
- ✅ `auth.logging_in` (ADDED)

### Asset Loan (asset_loan.php)
- `asset_loan.applicant`
- `asset_loan.submission_date`

## Affected Files

Most missing keys appear in:
- `submit-application.blade.php` (loan wizard)
- `user-profile.blade.php` (profile management)
- `submission-table.blade.php` (data tables)
- `authenticated-dashboard.blade.php` (dashboard)
- `approval-interface.blade.php` (approvals)
- `claim-submissions.blade.php` (claiming)

## Recommendations

1. **Priority 1 (Critical)**: Add missing keys for user-facing forms
   - Loan application wizard (`loans.*`)
   - Profile management (`profile.*`)
   - Common UI elements (`common.*`)

2. **Priority 2 (High)**: Add missing keys for admin features
   - Approval interface (`asset_loan.*`)
   - Submission tables (`loans.number`, `loans.approval`)

3. **Priority 3 (Medium)**: Add missing keys for help text
   - `helpdesk.no_divisions_help`
   - `helpdesk.terms_of_service`

## Next Steps

1. Create translation files for missing namespaces (if needed)
2. Add all missing keys with proper MS/EN translations
3. Re-run `scripts/check-missing-translations.ps1` to verify
4. Test affected pages to ensure translations display correctly

## Script Usage

```powershell
# Run translation checker
powershell -ExecutionPolicy Bypass -File scripts\check-missing-translations.ps1
```

## Notes

- Some keys may be intentionally missing if features are incomplete
- Verify with product owner before adding translations for unused features
- Follow existing translation patterns for consistency
