# PowerShell equivalent

$MYSQL_ROOT_PASSWORD = 'your_mysql_root_password'
$REMOTE_IP = '193.203.166.204'
$DB_USER = 'u698616357_5UvbY'
$DB_PASSWORD = 'your_password'

# Allow remote access in my.ini
(Get-Content 'C:\ProgramData\MySQL\MySQL Server 8.0\my.ini') -replace 'bind-address=127.0.0.1', 'bind-address=0.0.0.0' | Set-Content 'C:\ProgramData\MySQL\MySQL Server 8.0\my.ini'

# Restart MySQL service
Stop-Service -Name 'MySQL80'
Start-Service -Name 'MySQL80'

# Create MySQL user and grant privileges
$mysqlCommand = "CREATE USER '$DB_USER'@'$REMOTE_IP' IDENTIFIED BY '$DB_PASSWORD'; GRANT ALL PRIVILEGES ON *.* TO '$DB_USER'@'$REMOTE_IP' WITH GRANT OPTION; FLUSH PRIVILEGES;"
Invoke-Expression "mysql -u root -p$MYSQL_ROOT_PASSWORD -e `"$mysqlCommand`""

# Open MySQL port on the firewall
New-NetFirewallRule -DisplayName "MySQL Remote Access" -Direction Inbound -Action Allow -Protocol TCP -LocalPort 3306

Write-Output "MySQL remote access setup complete."
