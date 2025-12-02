$out = 'C:\XAMPP\htdocs\ictserve-031125\scripts\doc_refs_debug.csv'
if(Test-Path $out){ Remove-Item $out }
Get-ChildItem -Path 'C:\XAMPP\htdocs\ictserve-031125\docs' -Filter 'D*.md' -File | ForEach-Object {
    $file = $_
    $lines = Get-Content $file.FullName
    $match = $lines | Select-String -Pattern '##\s*Rujukan Dokumen Berkaitan|##\s*Related Document References' -List | Select-Object -First 1
    if($null -eq $match){ return }
    $idx = $match.LineNumber
    $refs = @()
    for($i = $idx; $i -lt $lines.Count; $i++){
        $line = $lines[$i].Trim()
        if($line -match '^##\s') { break }
        if($line -match '\[([^\]]+\.md)\]'){ $refs += $matches[1] }
        else {
            $m = [regex]::Matches($line,'docs/[A-Za-z0-9_\-./]+\.md')
            foreach($mm in $m){ $refs += $mm.Value }
            $m2 = [regex]::Matches($line,'\b(D\d{2}_[A-Z0-9_\-]+\.md)')
            foreach($mm2 in $m2){ $refs += $mm2.Value }
        }
    }
    if($refs.Count -gt 0){
        $src = [IO.Path]::GetFileNameWithoutExtension($file.Name)
        $targets = $refs | ForEach-Object { [IO.Path]::GetFileNameWithoutExtension($_) }
        foreach($t in $targets | Select-Object -Unique){
            "${src},${t}" | Out-File -Append -FilePath $out -Encoding utf8
        }
    }
}
Write-Output "Wrote mappings to $out"
