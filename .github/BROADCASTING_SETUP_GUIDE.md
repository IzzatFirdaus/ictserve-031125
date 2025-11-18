# Laravel Echo Broadcasting Setup Guide — ICTServe

**Status**: ✅ Composer dependencies installed & verified  
**Infrastructure**: Fully prepared (events, channels, frontend Echo client ready)  
**Next Step**: Select broadcaster and configure credentials

---

## ✅ Verification Status

| Component | Status | Details |
|-----------|--------|---------|
| **Pusher PHP SDK** | ✅ Ready | pusher/pusher-php-server v7.2.7 installed |
| **Laravel Framework** | ✅ Ready | v12.38.1 with broadcasting support |
| **Broadcast Events** | ✅ Ready | 3 events (NotificationCreated, StatusUpdated, CommentPosted) |
| **Channel Authorization** | ✅ Ready | Private user channels in routes/channels.php |
| **Frontend Echo Client** | ✅ Ready | Reverb-primary + Pusher fallback in resources/js/bootstrap.js |
| **Configuration Files** | ✅ Ready | config/broadcasting.php with all drivers |
| **.env.example** | ✅ Ready | PUSHER_*and BROADCAST_* variables documented |

---

## 🚀 Implementation Path: Pusher (Recommended)

### Why Pusher?

- ✅ Fully managed (no server operations)
- ✅ Free tier available (100 concurrent connections, 200K messages/day)
- ✅ Zero version conflicts with Laravel 12
- ✅ Scales seamlessly (production-ready)
- ✅ Works immediately (3-step setup)

### Step 1: Create Pusher Account & Get Credentials

1. Go to [pusher.com](https://pusher.com)
2. Sign up for free tier
3. Create new app (Channels)
4. Copy these credentials:
   - **App ID** (numeric)
   - **Key** (alphanumeric)
   - **Secret** (long alphanumeric)
   - **Cluster** (e.g., `mt1` for Malaysia/Singapore)

### Step 2: Configure .env

Edit `.env` in project root:

# Local (developer):

```bash
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

# Frontend variables (expose to JavaScript)
VITE_PUSHER_APP_KEY=your_app_key
VITE_PUSHER_HOST=api-mt1.pusher.com
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
VITE_PUSHER_APP_CLUSTER=mt1
```

### Step 3: Start Queue Worker

Broadcasting is async (powered by Redis queue). Run in separate terminal:

# Linux/macOS
```bash
php artisan queue:work redis --queue=default,broadcast
```

For production, use Supervisor or Laravel Horizon to keep worker running.

### Step 4: Build Frontend

Expose VITE variables to browser:

# Windows PowerShell
```powershell
npm ci
npm run dev    # or 'npm run build' for production
```

### Step 5: Test Broadcasting

**Backend - Manually Trigger Event** (Tinker):

```bash
php artisan tinker

> event(new App\Events\NotificationCreated($user, $notification))
```

**Frontend - Listen for Events** (JavaScript Console):

```javascript
// In any blade template with Echo initialized
Echo.private('App.Models.User.' + userId)
    .listen('NotificationCreated', (event) => {
        console.log('Event received:', event);
    });
```

When event is triggered, browser console shows event data in real-time ✓

---

## 🔄 Alternative: Reverb (Self-Hosted)

If you prefer self-hosted after Pusher testing:

```bash
# Already configured in config/broadcasting.php
# Just change BROADCAST_CONNECTION

BROADCAST_CONNECTION=reverb
```

See D16_BROADCASTING_SETUP.md § 4.2 for Reverb setup.

### Starting Reverb

For convenience we provide an artisan wrapper to start the Reverb server locally and via Supervisor in production.

### Local (developer)

```bash
# From project root
php artisan reverb:serve --host=127.0.0.1 --port=8080 --scheme=http
```

### Using the included wrapper scripts

Linux/macOS

```bash
scripts/reverb-start.sh 127.0.0.1 8080 http
```

Windows PowerShell

```powershell
.\scripts\reverb-start.ps1 -Host 127.0.0.1 -Port 8080 -Scheme http
```

Supervisor sample config is in `scripts/supervisor/reverb.conf`.

---

## ⏸️ Optional: WebSockets (For Future)

Laravel WebSockets v2.0 (Laravel 12 support) is in beta. When stable:

```bash
composer require beyondcode/laravel-websockets
php artisan websockets:serve
```

See D16_BROADCASTING_SETUP.md § 4.3 for details.

---

## 📊 Architecture Overview

```
User Action (Frontend)
    ↓
Livewire/Laravel Route
    ↓
Backend Service → event(new App\Events\StatusUpdated(...))
    ↓
Queue Worker (Redis)
    ↓
Broadcaster (Pusher/Reverb/WebSockets)
    ↓
Browser WebSocket
    ↓
Echo Client (JavaScript)
    ↓
Real-Time UI Update (No page reload)
```

---

## 🔍 Troubleshooting

### "No echo object" in Browser Console

**Cause**: VITE_PUSHER_* variables not set in .env  
**Fix**: Edit .env, rebuild frontend with `npm run dev`

### Events Not Broadcasting

**Cause 1**: Queue worker not running  
**Fix**: Run `php artisan queue:work redis` in terminal

**Cause 2**: BROADCAST_CONNECTION not set to 'pusher'  
**Fix**: Check `.env` → `BROADCAST_CONNECTION=pusher`

**Cause 3**: Event not implementing ShouldBroadcast  
**Fix**: Verify event class has `implements ShouldBroadcast`

### "guzzlehttp/psr7" Error (Legacy)

**Old Issue**: Removed ✓  
**Why**: Switched from WebSockets v1 (incompatible) to Pusher (stable)

---

## 📝 Next Actions

1. **Create Pusher account** (free tier)
2. **Add credentials to .env**
3. **Run queue worker** in terminal
4. **Build frontend** (npm run dev)
5. **Test broadcasting** through Tinker + browser console
6. **Deploy to production** (use Supervisor for queue worker)

---

## 📚 Related Documentation

- **D16_BROADCASTING_SETUP.md** — Complete technical specifications (10 sections)
- **routes/channels.php** — Channel authorization logic
- **app/Events/*.php** — Broadcast event classes
- **config/broadcasting.php** — Driver configuration
- **resources/js/bootstrap.js** — Frontend Echo initialization

---

## ✅ Completion Checklist

- [ ] Pusher account created + credentials obtained
- [ ] .env configured with PUSHER_*and VITE_PUSHER_* values
- [ ] Queue worker running (`php artisan queue:work redis`)
- [ ] Frontend built (`npm run dev` or `npm run build`)
- [ ] Echo client loads in browser console (`window.Echo` exists)
- [ ] Test event triggered via Tinker
- [ ] Browser receives event in real-time
- [ ] Production deployment plan documented

---

**Document Version**: v1.0.0  
**Last Updated**: Nov 16, 2025  
**Framework**: Laravel 12, Echo 2.2.6, Pusher-JS 8.4.0
