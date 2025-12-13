# Apache Virtual Host Setup Guide for ICTServe

This guide explains how to set up a proper Apache virtual host for ICTServe development using Laragon.

## Quick Setup (Automated)

### Prerequisites
- Laragon installed at `C:\laragon`
- PowerShell with Administrator privileges
- ICTServe project cloned

### Run Setup Script

1. **Open PowerShell as Administrator**
   - Right-click PowerShell
   - Select "Run as Administrator"

2. **Navigate to project directory**
   ```powershell
   cd C:\laragon\www\ictserve-031125
   ```

3. **Run the setup script**
   ```powershell
   .\setup-vhost.ps1
   ```

4. **Access your application**
   - Open browser: `http://ictserve.test`

## Manual Setup

If you prefer to set up manually or need to customize the configuration:

### Step 1: Create Virtual Host Configuration

1. **Navigate to Apache sites directory**
   ```
   C:\laragon\etc\apache2\sites-enabled\
   ```

2. **Create new file: `ictserve.test.conf`**
   ```apache
   <VirtualHost *:80>
       ServerName ictserve.test
       ServerAlias www.ictserve.test
       DocumentRoot "C:/laragon/www/ictserve-031125/public"
       
       <Directory "C:/laragon/www/ictserve-031125/public">
           Options Indexes FollowSymLinks MultiViews
           AllowOverride All
           Require all granted
           
           <IfModule mod_rewrite.c>
               RewriteEngine On
           </IfModule>
       </Directory>
       
       ErrorLog "C:/laragon/www/ictserve-031125/storage/logs/apache-error.log"
       CustomLog "C:/laragon/www/ictserve-031125/storage/logs/apache-access.log" combined
       
       <FilesMatch \.php$>
           SetHandler "proxy:fcgi://127.0.0.1:9000"
       </FilesMatch>
   </VirtualHost>
   ```

### Step 2: Update Windows Hosts File

1. **Open hosts file as Administrator**
   - Location: `C:\Windows\System32\drivers\etc\hosts`
   - Right-click Notepad → Run as Administrator
   - Open the hosts file

2. **Add entry**
   ```
   127.0.0.1    ictserve.test
   ```

3. **Save and close**

### Step 3: Update .env Configuration

1. **Open `.env` file in project root**

2. **Update APP_URL**
   ```env
   APP_URL=http://ictserve.test
   ```

3. **Verify other settings**
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ictserve
   DB_USERNAME=root
   DB_PASSWORD=
   
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   
   REVERB_HOST=127.0.0.1
   REVERB_PORT=6001
   ```

### Step 4: Clear Laravel Caches

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Step 5: Restart Apache

1. **Open Laragon**
2. **Click "Stop All"**
3. **Click "Start All"**

Or restart Apache service directly:
```powershell
Restart-Service Apache*
```

### Step 6: Test the Setup

1. **Open browser**
2. **Navigate to:** `http://ictserve.test`
3. **Verify homepage loads**

## Verification Checklist

- [ ] Virtual host file created in `sites-enabled`
- [ ] Hosts file updated with domain entry
- [ ] .env APP_URL updated to match domain
- [ ] Laravel caches cleared
- [ ] Apache restarted
- [ ] Homepage loads at `http://ictserve.test`
- [ ] Routes work (e.g., `/helpdesk/create`)
- [ ] Admin panel accessible at `/admin`

## Testing Routes

After setup, test these key routes:

```bash
# Homepage
http://ictserve.test/

# Helpdesk form
http://ictserve.test/helpdesk/create

# Loan application
http://ictserve.test/loan/create

# Admin panel
http://ictserve.test/admin

# Dashboard (requires login)
http://ictserve.test/dashboard

# Status checker
http://ictserve.test/status
```

## Troubleshooting

### Issue: Site doesn't load

**Solution:**
1. Verify Apache is running in Laragon
2. Check virtual host file syntax
3. Restart Apache
4. Clear browser cache

### Issue: 404 errors on all routes

**Solution:**
1. Verify DocumentRoot points to `public` directory
2. Check `.htaccess` file exists in `public/`
3. Ensure `mod_rewrite` is enabled in Apache
4. Clear Laravel route cache: `php artisan route:clear`

### Issue: CSS/JS not loading

**Solution:**
1. Run `npm run build` to compile assets
2. Check APP_URL in .env matches your domain
3. Clear browser cache
4. Verify `public/build` directory exists

### Issue: Database connection failed

**Solution:**
1. Start MySQL in Laragon
2. Verify database exists: `ictserve`
3. Check credentials in .env
4. Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

### Issue: Permission denied errors

**Solution:**
1. Check storage directory permissions
2. Run: `php artisan storage:link`
3. Ensure Apache has read/write access to storage and cache directories

## Advanced Configuration

### Using a Different Domain

To use a different domain (e.g., `ictserve.local`):

1. Update virtual host file: `ServerName ictserve.local`
2. Update hosts file: `127.0.0.1 ictserve.local`
3. Update .env: `APP_URL=http://ictserve.local`
4. Clear caches and restart Apache

### HTTPS Configuration

For local HTTPS development:

1. Generate self-signed certificate
2. Update virtual host to listen on port 443
3. Configure SSL certificate paths
4. Update APP_URL to use `https://`

See Laragon documentation for detailed HTTPS setup.

### Multiple Environments

You can create multiple virtual hosts for different environments:

- `ictserve.test` - Development
- `ictserve-staging.test` - Staging
- `ictserve-demo.test` - Demo

Each with its own virtual host configuration and .env file.

## Comparison: Virtual Host vs php artisan serve

| Feature | Virtual Host | php artisan serve |
|---------|-------------|-------------------|
| Setup Complexity | Medium | Simple |
| Configuration | Requires Apache config | Zero config |
| Performance | Production-like | Development only |
| Multiple Projects | Easy | One at a time |
| Custom Domain | Yes | No (127.0.0.1:8000) |
| HTTPS | Supported | Not supported |
| Team Consistency | High | Medium |

## Recommended Workflow

### For Solo Development
Use `php artisan serve` for quick testing and development.

### For Team Development
Use virtual host for consistency across team members.

### For Production-like Testing
Use virtual host with proper domain and HTTPS.

## Additional Services

After setting up the virtual host, start these services as needed:

```bash
# WebSocket server (for real-time features)
php artisan reverb:start

# Queue worker (for background jobs)
php artisan queue:work

# Vite dev server (for hot module replacement)
npm run dev
```

Or use the combined command:
```bash
composer run dev
```

## Uninstalling

To remove the virtual host setup:

1. Delete virtual host file: `C:\laragon\etc\apache2\sites-enabled\ictserve.test.conf`
2. Remove hosts entry: Edit `C:\Windows\System32\drivers\etc\hosts`
3. Restart Apache
4. Revert .env to use `http://127.0.0.1:8000`

## Support

For issues or questions:
- Check Apache error log: `storage/logs/apache-error.log`
- Check Laravel log: `storage/logs/laravel.log`
- Refer to `LARAGON_SETUP.md` for general setup guidance
- See `APACHE_ALIAS_TEST_RESULTS.md` for troubleshooting tips
