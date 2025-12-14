# Senarai Semak Deployment Integrasi AI Ollama (Ollama AI Deployment Checklist)

**Sistem ICTServe v3.6.0**  
**Tarikh:** 12 Disember 2025  
**Pematuhan:** D11 v3.6.0 Technical Design Documentation  
**Bahasa:** Bahasa Melayu sahaja (D15 v3.6.0)

---

## Pra-Deployment (Pre-Deployment)

### Persediaan Infrastruktur (Infrastructure Preparation)

- [ ] **Server Requirements Met**
  - [ ] Ubuntu 22.04 LTS atau CentOS 8+ installed
  - [ ] Minimum 16GB RAM available
  - [ ] Minimum 100GB SSD storage
  - [ ] 8+ CPU cores available
  - [ ] Network connectivity verified

- [ ] **Software Dependencies**
  - [ ] PHP 8.2.12+ installed dan configured
  - [ ] MySQL 8.0+ installed dan configured
  - [ ] Redis 7.0+ installed dan configured
  - [ ] Nginx 1.20+ atau Apache 2.4+ installed
  - [ ] Composer installed globally
  - [ ] Node.js 20+ dan npm installed

- [ ] **Ollama Server Setup**
  - [ ] Ollama binary downloaded dan installed
  - [ ] llama3.1 model downloaded (quantized Q4_K_M)
  - [ ] Ollama service configured untuk auto-start
  - [ ] Port 11434 accessible locally only
  - [ ] Model loading tested successfully

### Security Preparation

- [ ] **SSL/TLS Certificates**
  - [ ] Valid SSL certificate obtained untuk ictserve.motac.gov.my
  - [ ] Certificate chain verified
  - [ ] Private key secured dengan proper permissions
  - [ ] OCSP stapling configured

- [ ] **Firewall Configuration**
  - [ ] UFW atau iptables configured
  - [ ] Port 22 (SSH) allowed untuk admin access
  - [ ] Port 80 (HTTP) allowed untuk redirect
  - [ ] Port 443 (HTTPS) allowed untuk application
  - [ ] Port 6001 (Reverb) allowed untuk internal WebSocket
  - [ ] Port 11434 (Ollama) blocked dari external access
  - [ ] All other ports denied by default

- [ ] **Access Control**
  - [ ] SSH key-based authentication configured
  - [ ] Root login disabled
  - [ ] Sudo access configured untuk deployment user
  - [ ] Database user created dengan limited privileges
  - [ ] Redis password configured

---

## Deployment Process

### Application Deployment

- [ ] **Code Deployment**
  - [ ] Repository cloned ke /var/www/ictserve-ai
  - [ ] Correct branch/tag checked out
  - [ ] File permissions set correctly (www-data:www-data)
  - [ ] Storage dan cache directories writable
  - [ ] Symbolic link created untuk public storage

- [ ] **Dependencies Installation**
  - [ ] Composer dependencies installed (--no-dev --optimize-autoloader)
  - [ ] NPM dependencies installed (npm ci)
  - [ ] Frontend assets built (npm run build)
  - [ ] Vendor assets published

- [ ] **Environment Configuration**
  - [ ] .env file created dari .env.production.example
  - [ ] Application key generated (php artisan key:generate)
  - [ ] Database credentials configured
  - [ ] Redis credentials configured
  - [ ] Mail configuration set
  - [ ] Ollama configuration verified
  - [ ] Laravel Reverb configuration set
  - [ ] Laravel Sanctum domains configured

### Database Setup

- [ ] **Database Configuration**
  - [ ] Production database created
  - [ ] Database user created dengan proper privileges
  - [ ] Character set set ke utf8mb4_unicode_ci
  - [ ] Connection tested successfully

- [ ] **Schema Deployment**
  - [ ] Migrations run successfully (php artisan migrate --force)
  - [ ] Seeders run jika diperlukan (AdminUserSeeder)
  - [ ] Database indexes verified
  - [ ] Foreign key constraints verified

### Service Configuration

- [ ] **Web Server Configuration**
  - [ ] Nginx/Apache virtual host configured
  - [ ] SSL configuration verified
  - [ ] Security headers configured
  - [ ] WebSocket proxy configured untuk Reverb
  - [ ] PHP-FPM pool configured
  - [ ] Log rotation configured

- [ ] **Background Services**
  - [ ] Laravel Horizon installed dan configured
  - [ ] Horizon systemd service created dan enabled
  - [ ] Laravel Reverb systemd service created dan enabled
  - [ ] Queue workers configured untuk auto-restart
  - [ ] Cron jobs configured untuk scheduled tasks

### Cache and Optimization

- [ ] **Application Optimization**
  - [ ] Configuration cached (php artisan config:cache)
  - [ ] Routes cached (php artisan route:cache)
  - [ ] Views cached (php artisan view:cache)
  - [ ] Autoloader optimized
  - [ ] OPcache enabled dan configured

- [ ] **Redis Configuration**
  - [ ] Redis cache store configured
  - [ ] Session storage configured
  - [ ] Queue backend configured
  - [ ] Broadcasting backend configured
  - [ ] Memory limits set appropriately

---

## Post-Deployment Verification

### Functional Testing

- [ ] **Basic Application Functions**
  - [ ] Homepage loads successfully
  - [ ] User registration works (@motac.gov.my emails)
  - [ ] User login works (email dan username)
  - [ ] Admin panel accessible (/admin)
  - [ ] API endpoints respond correctly

- [ ] **AI Features Testing**
  - [ ] Ollama server connectivity verified
  - [ ] FAQ Bot responds dalam Bahasa Melayu
  - [ ] Document upload dan processing works
  - [ ] Auto-reply generation works
  - [ ] Approval workflow functions correctly

- [ ] **Real-time Features**
  - [ ] WebSocket connection established
  - [ ] Laravel Reverb broadcasting works
  - [ ] Real-time notifications delivered
  - [ ] Queue jobs processed successfully

### Performance Verification

- [ ] **Response Time Testing**
  - [ ] Homepage loads dalam <2.5 seconds (LCP)
  - [ ] API responses dalam <5 seconds (95th percentile)
  - [ ] AI responses generated dalam target time
  - [ ] Database queries optimized (<100ms average)

- [ ] **Load Testing**
  - [ ] Application handles 100 concurrent users
  - [ ] Memory usage stays below 16GB
  - [ ] CPU usage acceptable under load
  - [ ] No memory leaks detected

- [ ] **Core Web Vitals**
  - [ ] Largest Contentful Paint (LCP) < 2.5s
  - [ ] First Input Delay (FID) < 100ms
  - [ ] Cumulative Layout Shift (CLS) < 0.1
  - [ ] Lighthouse Performance Score > 90

### Security Verification

- [ ] **SSL/TLS Security**
  - [ ] SSL Labs rating A+ achieved
  - [ ] HSTS headers present
  - [ ] Security headers configured correctly
  - [ ] No mixed content warnings

- [ ] **Application Security**
  - [ ] External API calls blocked successfully
  - [ ] PII detection working correctly
  - [ ] Audit logging functioning
  - [ ] Rate limiting enforced
  - [ ] CSRF protection enabled

- [ ] **Network Security**
  - [ ] Ollama port 11434 blocked externally
  - [ ] Only required ports accessible
  - [ ] No unnecessary services running
  - [ ] Intrusion detection configured

### Monitoring Setup

- [ ] **Health Checks**
  - [ ] Application health endpoint responding
  - [ ] Ollama health check working
  - [ ] Database connectivity verified
  - [ ] Redis connectivity verified

- [ ] **Laravel Pulse Dashboard**
  - [ ] Pulse dashboard accessible di /admin/pulse
  - [ ] Performance metrics collecting
  - [ ] Slow query detection working
  - [ ] Queue job monitoring active

- [ ] **Laravel Telescope (Superuser Only)**
  - [ ] Telescope accessible di /admin/telescope
  - [ ] Request monitoring working
  - [ ] Query debugging available
  - [ ] Job tracking functional

- [ ] **Log Monitoring**
  - [ ] Application logs writing correctly
  - [ ] Error logs configured
  - [ ] Log rotation working
  - [ ] Critical error alerting setup

### Backup Verification

- [ ] **Backup Systems**
  - [ ] Database backup script configured
  - [ ] Application backup script configured
  - [ ] Backup storage accessible
  - [ ] Backup restoration tested

- [ ] **Disaster Recovery**
  - [ ] Rollback procedures documented
  - [ ] Recovery time objectives defined
  - [ ] Emergency contact list updated
  - [ ] Incident response plan ready

---

## Compliance Verification (D00-D17 v3.6.0)

### Architecture Compliance

- [ ] **D00 v3.6.0: True Hybrid Architecture**
  - [ ] Guest forms accessible tanpa login
  - [ ] Self-registration working untuk @motac.gov.my
  - [ ] Flexible login (email/username) functional
  - [ ] Account linking feature working
  - [ ] Nullable user_id FK pattern implemented

- [ ] **D09 v3.6.0: Dual Audit System**
  - [ ] owen-it/laravel-auditing configured untuk compliance
  - [ ] spatie/laravel-activitylog configured untuk operations
  - [ ] Audit logs writing correctly
  - [ ] Data lineage tracking working
  - [ ] 7-year retention policy configured

### Technology Integration

- [ ] **D11 v3.6.0: Technical Infrastructure**
  - [ ] Laravel Pulse v1.3.0 monitoring active
  - [ ] Laravel Telescope v5.x debugging available (superuser only)
  - [ ] Laravel Sanctum v4.0 API authentication working
  - [ ] Performance targets met

- [ ] **D16 v3.6.0: Broadcasting Setup**
  - [ ] Laravel Reverb v1.6.2 WebSocket server running
  - [ ] Real-time notifications working
  - [ ] Broadcasting channels configured
  - [ ] WebSocket security implemented

- [ ] **D17 v3.6.0: Queue Management**
  - [ ] Laravel Horizon queue management active
  - [ ] Background jobs processing correctly
  - [ ] Queue monitoring dashboard accessible
  - [ ] Failed job retry logic working

### User Interface Compliance

- [ ] **D15 v3.6.0: Bahasa Melayu Sahaja**
  - [ ] All user interfaces dalam Bahasa Melayu
  - [ ] Language switcher disabled/removed
  - [ ] AI responses dalam Bahasa Melayu sahaja
  - [ ] Error messages dalam Bahasa Melayu
  - [ ] Email notifications dalam Bahasa Melayu

- [ ] **D12-D14 v3.6.0: WCAG 2.2 AA Compliance**
  - [ ] Color contrast ratios verified (4.5:1 text, 3:1 UI)
  - [ ] Keyboard navigation working
  - [ ] Screen reader compatibility tested
  - [ ] Focus indicators visible
  - [ ] Touch targets minimum 44×44px
  - [ ] ARIA labels dan landmarks present

### Security and Privacy

- [ ] **PDPA 2010 Compliance**
  - [ ] PII detection dan sanitization working
  - [ ] Data residency dalam Malaysia verified
  - [ ] User consent mechanisms implemented
  - [ ] Data access rights supported
  - [ ] Data deletion capabilities working

- [ ] **AI Security**
  - [ ] Local LLM processing only (no external APIs)
  - [ ] Network monitoring detecting external calls
  - [ ] Audit trail untuk all AI operations
  - [ ] Immutable log integrity verified
  - [ ] Cryptographic hashing working

---

## Sign-off

### Technical Team

- [ ] **System Administrator**
  - Name: ________________
  - Date: ________________
  - Signature: ________________

- [ ] **Database Administrator**
  - Name: ________________
  - Date: ________________
  - Signature: ________________

- [ ] **Security Officer**
  - Name: ________________
  - Date: ________________
  - Signature: ________________

### Management Team

- [ ] **Project Manager**
  - Name: ________________
  - Date: ________________
  - Signature: ________________

- [ ] **IT Manager**
  - Name: ________________
  - Date: ________________
  - Signature: ________________

### Final Approval

- [ ] **Deployment Approved untuk Production**
  - Approver: ________________
  - Date: ________________
  - Signature: ________________

---

## Emergency Rollback Plan

Jika masalah kritikal ditemui selepas deployment:

1. **Immediate Actions**
   - [ ] Stop incoming traffic (maintenance mode)
   - [ ] Assess impact dan severity
   - [ ] Notify stakeholders

2. **Rollback Steps**
   - [ ] Revert application code ke previous stable version
   - [ ] Rollback database migrations jika diperlukan
   - [ ] Clear all caches
   - [ ] Restart services
   - [ ] Verify functionality

3. **Post-Rollback**
   - [ ] Document issues encountered
   - [ ] Plan remediation steps
   - [ ] Schedule re-deployment

**Emergency Contact**: +603-XXXX-XXXX (24/7 Support)
