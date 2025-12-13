# 🚀 ICTServe Laragon - Manual Setup Steps (Post-Automation)

## ⚠️ IMPORTANT: Complete These Manual Steps to Activate Your Setup

The automated setup is **100% complete**, but you need to complete 3 manual steps to activate the services.

**Estimated time**: 5-10 minutes  
**Required access**: Administrator (for hosts file)

---

## Step 1️⃣: Add Entry to Windows Hosts File

### Location

```
C:\Windows\System32\drivers\etc\hosts
```

### Instructions

1. **Open Notepad as Administrator**
   - Press `Windows Key`
   - Search: `Notepad`
   - Right-click → "Run as administrator"
   - Click "Yes" on the UAC prompt

2. **Open the hosts file**
   - In Notepad: File → Open (Ctrl+O)
   - Navigate to: `C:\Windows\System32\drivers\etc\`
   - Change file type dropdown to: **"All Files (*.*)"**
   - Click on file: `hosts`
   - Click "Open"

3. **Add the ICTServe entry**
   - Scroll to the bottom of the file
   - Add this line:

     ```
     127.0.0.1       ictserve.local www.ictserve.local
     ```

4. **Save the file**
   - Press `Ctrl+S`
   - Close Notepad

### Verification
After saving, your hosts file should have this at the bottom:

```
127.0.0.1       ictserve.local www.ictserve.local
```

---

## Step 2️⃣: Restart Laragon Services

### Instructions

1. **Open Laragon Application**
   - Look for Laragon in your system tray (bottom right corner)
   - Or search for "Laragon" in Windows Start menu
   - Click to open the Laragon GUI window

2. **Stop all services**
   - In the Laragon window, find the button: **"Stop All"**
   - Click it
   - Wait ~3 seconds for services to stop
   - All service status should show: ⚪ (gray/stopped)

3. **Start all services**
   - Click the button: **"Start All"**
   - Wait 10-15 seconds for all services to start
   - Watch the status indicators change from ⚪ (gray) to 🟢 (green)

4. **Verify all services are running**
   - All service indicators should be 🟢 (GREEN):
     - ✅ Apache
     - ✅ Nginx
     - ✅ MySQL
     - ✅ Redis
     - ✅ PHP
   - If any are still ⚪ or 🔴, wait another 5-10 seconds

### Status Indicators

- 🟢 **Green** = Service is running (GOOD)
- 🟡 **Yellow** = Service is starting (wait a moment)
- 🔴 **Red** = Service failed to start (restart Laragon)
- ⚪ **Gray** = Service is stopped (click "Start All")

---

## Step 3️⃣: Clear Windows DNS Cache (Optional)

This ensures your system recognizes the new `ictserve.local` entry.

### Instructions

1. **Open PowerShell**
   - Press `Windows Key`
   - Search: `PowerShell`
   - Click "Windows PowerShell" (no need for admin here)

2. **Run the DNS flush command**
   - Copy and paste this command:

     ```powershell
     ipconfig /flushdns
     ```

   - Press Enter
   - Wait for the command to complete

3. **Expected output**

   ```
   Windows IP Configuration
   
   Successfully flushed the DNS Resolver Cache.
   ```

4. **Close PowerShell**
   - Type: `exit`
   - Press Enter

---

## 🎯 Access Your Application

Once all manual steps are complete, you can access ICTServe:

### URLs

| URL | Purpose | Notes |
|-----|---------|-------|
| `http://ictserve.local` | Main application | Port 80 (Apache) |
| `http://ictserve.local/admin` | Admin panel (Filament) | For superuser/admin accounts |
| `http://ictserve.local:8080` | WebSocket proxy | For real-time features |

### Test in Browser

1. Open your web browser (Chrome, Firefox, Edge, etc.)
2. In the address bar, type: `http://ictserve.local`
3. Press Enter
4. You should see the ICTServe login page

---

## 🔐 Login Information

All test users are ready to use:

```
Email:    admin@motac.gov.my
Password: password
```

### Other Test Users

- `staff@motac.gov.my` - Basic user (Staff role)
- `approver@motac.gov.my` - Approver user (can approve requests)
- `superuser@motac.gov.my` - Superuser (full system access)

All use the same password: `password`

---

## ✅ Verification Checklist

After completing the manual steps, verify everything works:

- [ ] Hosts file entry added (verify by opening hosts file again)
- [ ] All Laragon services are 🟢 green
- [ ] `http://ictserve.local` loads in browser
- [ ] Login page displays correctly
- [ ] Can login with <admin@motac.gov.my> / password
- [ ] Admin panel at `/admin` is accessible
- [ ] Database data visible (users, assets, tickets in admin)

---

## 🐛 Troubleshooting

### Issue: "Cannot find <http://ictserve.local>"

**Possible causes & solutions**:

1. **Hosts file not updated correctly**
   - Open hosts file again: `C:\Windows\System32\drivers\etc\hosts`
   - Verify the line is there: `127.0.0.1       ictserve.local`
   - Make sure there's no typo (capital letters don't matter)

2. **DNS cache not cleared**
   - Run: `ipconfig /flushdns` again
   - Wait 5 seconds
   - Try browser again (refresh with Ctrl+F5)

3. **Apache not running**
   - Check Laragon: Apache service should be 🟢 green
   - If not, click "Stop All" then "Start All" and wait 15 seconds

4. **Try alternative access method**
   - Instead of `ictserve.local`, try: `127.0.0.1`
   - Or: `localhost`

### Issue: 502 Bad Gateway (port 8080)

This means Nginx can't reach Apache.

**Solution**:

1. Verify Apache is running (🟢 green in Laragon)
2. Verify Nginx is running (🟢 green in Laragon)
3. Restart both:
   - In Laragon, click "Stop All"
   - Wait 3 seconds
   - Click "Start All"
   - Wait 15 seconds

### Issue: MySQL connection refused

**Solution**:

1. Restart MySQL from Laragon GUI
2. Verify `.env` has: `DB_HOST=127.0.0.1`
3. Check your username is `root` with no password

### Issue: Page loads but shows errors

**Solution**:

1. Clear Laravel cache:

   ```powershell
   cd C:\laragon\www\ictserve-031125
   php artisan optimize:clear
   ```

2. Rebuild frontend assets:

   ```powershell
   npm run build
   ```

---

## 📱 What You Can Do

After setup is complete, you can:

### 📋 **Helpdesk Module**

- View and submit support tickets
- Track ticket status
- Leave comments on tickets
- Assign priority levels

### 📦 **Asset Management**

- View available assets
- Search by category
- Check asset status
- View asset details

### 🎁 **Loan Management**

- Submit loan applications
- Check approval status
- Return borrowed items
- View loan history

### 👥 **Administration** (Admin panel at `/admin`)

- Manage users and roles
- View system statistics
- Configure settings
- Monitor activity logs

---

## 💡 Pro Tips

### Faster Access
Add a bookmark to `http://ictserve.local` in your browser for quick access.

### Keep Terminal Handy
For development, keep a PowerShell window open in the project directory:

```powershell
cd C:\laragon\www\ictserve-031125
```

### Development Commands

```powershell
# Clear caches
php artisan optimize:clear

# Watch frontend changes
npm run dev

# Start dev server (alternative to Apache)
php artisan serve

# Run tests
php artisan test
```

### Real-time Features Testing
The WebSocket proxy is on port 8080. Real-time notifications and updates flow through:

```
http://ictserve.local:8080
```

---

## 🎓 Next Learning Steps

Once you have everything running:

1. **Explore the Admin Panel**
   - Visit: `http://ictserve.local/admin`
   - Login with <admin@motac.gov.my>
   - Navigate through different modules

2. **Test Each Module**
   - Helpdesk: Submit a test ticket
   - Assets: Browse available equipment
   - Loans: Create a test application

3. **Review Documentation**
   - Read: `LARAGON_SETUP_COMPLETE.md`
   - Read: `README.md`
   - Check: `docs/` directory for architecture

4. **Understand the Stack**
   - Laravel 12 (backend framework)
   - Filament (admin panel)
   - Livewire 3 + Volt (frontend interactivity)
   - Tailwind CSS 4 (styling)

---

## 📞 Getting Help

If you encounter issues:

1. Check **LARAGON_SETUP_COMPLETE.md** (Troubleshooting section)
2. Check **LARAGON_QUICK_START.md** (Quick reference)
3. Check **SETUP_CHANGES_LOG.md** (What was changed)
4. Review `.github/instructions/` for component-specific guides

---

## ✨ You're All Set

Your ICTServe instance is now ready for:

- ✅ Development
- ✅ Testing
- ✅ Training
- ✅ Demonstration
- ✅ Feature exploration

**Enjoy using ICTServe!** 🎉

---

**Setup Status**: ✅ COMPLETE  
**Last Updated**: December 2, 2025  
**Laragon Version**: Latest (2025)  
**PHP Version**: 8.2.12  
**MySQL Version**: MariaDB 10.4.32  
