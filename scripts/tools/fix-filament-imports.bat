@echo off
echo Fixing Filament 4 imports in all pages...

REM Fix SLAThresholdManagement
powershell -Command "(Get-Content 'app\Filament\Pages\SLAThresholdManagement.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'use Filament\\Schemas\\Components\\Fieldset;', 'use Filament\Forms\Components\Fieldset;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\SLAThresholdManagement.php'"

REM Fix AlertConfiguration
powershell -Command "(Get-Content 'app\Filament\Pages\AlertConfiguration.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'use Filament\\Schemas\\Components\\Section;', 'use Filament\Forms\Components\Section;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\AlertConfiguration.php'"

REM Fix ApprovalMatrixConfiguration
powershell -Command "(Get-Content 'app\Filament\Pages\ApprovalMatrixConfiguration.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\ApprovalMatrixConfiguration.php'"

REM Fix BilingualManagement
powershell -Command "(Get-Content 'app\Filament\Pages\BilingualManagement.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace '\\\\Filament\\\\Schemas\\\\Components\\\\Section::', 'Section::' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\BilingualManagement.php'"

REM Fix DataExportCenter
powershell -Command "(Get-Content 'app\Filament\Pages\DataExportCenter.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'use Filament\\Schemas\\Components\\Section;', 'use Filament\Forms\Components\Section;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\DataExportCenter.php'"

REM Fix EmailTemplateManagement
powershell -Command "(Get-Content 'app\Filament\Pages\EmailTemplateManagement.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'use Filament\\Schemas\\Components\\Section;', 'use Filament\Forms\Components\Section;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\EmailTemplateManagement.php'"

REM Fix HelpdeskReports
powershell -Command "(Get-Content 'app\Filament\Pages\HelpdeskReports.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'use Filament\\Schemas\\Components\\Section;', 'use Filament\Forms\Components\Section;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\HelpdeskReports.php'"

REM Fix NotificationPreferences
powershell -Command "(Get-Content 'app\Filament\Pages\NotificationPreferences.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'use Filament\\Schemas\\Components\\Section;', 'use Filament\Forms\Components\Section;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\NotificationPreferences.php'"

REM Fix WorkflowAutomationConfiguration
powershell -Command "(Get-Content 'app\Filament\Pages\WorkflowAutomationConfiguration.php') -replace 'use Filament\\Schemas\\Schema;', 'use Filament\Forms\Form;' -replace 'use Filament\\Schemas\\Components\\Section;', 'use Filament\Forms\Components\Section;' -replace 'public function form\(Schema \$schema\): Schema', 'public function form(Form $form): Form' -replace '\$schema', '$form' | Set-Content 'app\Filament\Pages\WorkflowAutomationConfiguration.php'"

echo.
echo All files fixed! Clearing cache...
php artisan view:clear
php artisan config:clear

echo.
echo Done! All Filament 4 imports corrected.
pause
