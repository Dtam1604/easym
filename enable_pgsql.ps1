$content = Get-Content 'D:\app\xampp\php\php.ini'
$content = $content -replace '^;extension=pdo_pgsql', 'extension=pdo_pgsql' -replace '^;extension=pgsql', 'extension=pgsql'
Set-Content 'D:\app\xampp\php\php.ini' $content
