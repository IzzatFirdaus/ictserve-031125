# Extract hardcoded strings from Filament widgets
$widgets = Get-ChildItem "app/Filament/Widgets" -Filter "*.php" -Recurse

$hardcodedStrings = @()

foreach ($widget in $widgets) {
    $content = Get-Content $widget.FullName -Raw
    
    # Extract ->label('...') patterns
    $labels = [regex]::Matches($content, "->label\('([^']+)'\)")
    foreach ($match in $labels) {
        $hardcodedStrings += [PSCustomObject]@{
            File = $widget.Name
            Type = "label"
            String = $match.Groups[1].Value
        }
    }
    
    # Extract ->description('...') patterns
    $descriptions = [regex]::Matches($content, "->description\('([^']+)'\)")
    foreach ($match in $descriptions) {
        $hardcodedStrings += [PSCustomObject]@{
            File = $widget.Name
            Type = "description"
            String = $match.Groups[1].Value
        }
    }
    
    # Extract ->heading('...') patterns
    $headings = [regex]::Matches($content, "->heading\('([^']+)'\)")
    foreach ($match in $headings) {
        $hardcodedStrings += [PSCustomObject]@{
            File = $widget.Name
            Type = "heading"
            String = $match.Groups[1].Value
        }
    }
    
    # Extract Stat::make('...') patterns
    $stats = [regex]::Matches($content, "Stat::make\('([^']+)'\)")
    foreach ($match in $stats) {
        $hardcodedStrings += [PSCustomObject]@{
            File = $widget.Name
            Type = "stat"
            String = $match.Groups[1].Value
        }
    }
}

$hardcodedStrings | Format-Table -AutoSize
