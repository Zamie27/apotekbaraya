# 🚀 Production Deployment Guide - Apotek Baraya

## 📋 Overview

Panduan ini menjelaskan cara mengatasi masalah "No payment method available" yang terjadi di production hosting tetapi tidak di local development.

## 🔍 Root Cause Analysis

### Penyebab Umum Perbedaan Local vs Production:

1. **Database Kosong**: Migration dan seeder belum dijalankan di production
2. **Environment Variables**: Konfigurasi database berbeda antara local dan production
3. **Cache Issues**: Cache configuration yang tidak sesuai
4. **Permission Issues**: File permission yang salah di server
5. **PHP/Laravel Version**: Perbedaan versi antara local dan production

## 🛠️ Quick Fix Solutions

### Option 1: Automated Deployment Script

```bash
# Upload dan jalankan script deployment
chmod +x deploy_production.sh
./deploy_production.sh
```

### Option 2: Manual Step-by-Step

```bash
# 1. Update dependencies
composer install --no-dev --optimize-autoloader

# 2. Run migrations
php artisan migrate --force

# 3. Seed payment methods
php artisan db:seed --class=PaymentMethodSeeder --force

# 4. Activate payment methods
php artisan tinker
# Dalam tinker:
use App\Models\PaymentMethod;
PaymentMethod::whereIn('code', ['bank_transfer', 'cod'])->update(['is_active' => true]);
exit

# 5. Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Option 3: Diagnostic First

```bash
# Jalankan diagnostic script untuk melihat masalah
php diagnose_payment_methods.php

# Kemudian jalankan fix script
php fix_payment_methods_production.php
```

## 📁 Files Created for Production Deployment

### 1. `diagnose_payment_methods.php`
- **Purpose**: Mendiagnosa masalah payment methods di production
- **Usage**: `php diagnose_payment_methods.php`
- **Output**: Laporan lengkap status payment methods

### 2. `fix_payment_methods_production.php`
- **Purpose**: Memperbaiki masalah payment methods secara otomatis
- **Usage**: `php fix_payment_methods_production.php`
- **Features**: 
  - Auto-run migrations
  - Auto-seed payment methods
  - Activate default payment methods
  - Clear caches

### 3. `deploy_production.sh`
- **Purpose**: Script deployment lengkap untuk production
- **Usage**: `chmod +x deploy_production.sh && ./deploy_production.sh`
- **Features**: Full deployment pipeline

## 🔧 Environment Configuration Check

### Required .env Variables

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_database
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Application
APP_ENV=production
APP_DEBUG=false
APP_KEY=your_app_key

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Verify Database Connection

```bash
# Test database connection
php artisan tinker
# In tinker:
DB::connection()->getPdo();
# Should not throw error
```

## 📊 Payment Methods Configuration

### Default Payment Methods Structure

```sql
-- Expected payment_methods table structure
CREATE TABLE `payment_methods` (
  `payment_method_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `configuration` json,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`payment_method_id`),
  UNIQUE KEY `payment_methods_code_unique` (`code`)
);
```

### Default Payment Methods Data

```sql
-- Required payment methods
INSERT INTO payment_methods (code, name, description, is_active, sort_order) VALUES
('bank_transfer', 'Transfer Bank', 'Pembayaran melalui transfer bank', 1, 1),
('cod', 'Bayar di Tempat (COD)', 'Pembayaran saat barang diterima', 1, 2),
('e_wallet', 'E-Wallet', 'Pembayaran melalui dompet digital', 0, 3);
```

## 🚨 Troubleshooting Common Issues

### Issue 1: "No payment method available"

**Symptoms**: Error saat checkout di production
**Cause**: Tidak ada payment method yang aktif
**Solution**:
```bash
php artisan tinker
PaymentMethod::whereIn('code', ['bank_transfer', 'cod'])->update(['is_active' => true]);
```

### Issue 2: "Table 'payment_methods' doesn't exist"

**Symptoms**: Database error
**Cause**: Migration belum dijalankan
**Solution**:
```bash
php artisan migrate --force
php artisan db:seed --class=PaymentMethodSeeder --force
```

### Issue 3: "Class 'PaymentMethodSeeder' not found"

**Symptoms**: Seeder error
**Cause**: Autoloader belum di-update
**Solution**:
```bash
composer dump-autoload
php artisan db:seed --class=PaymentMethodSeeder --force
```

### Issue 4: Permission Denied

**Symptoms**: File permission errors
**Cause**: Wrong file permissions
**Solution**:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 📝 Production Deployment Checklist

### Pre-Deployment
- [ ] Backup production database
- [ ] Verify .env configuration
- [ ] Test database connection
- [ ] Check PHP version compatibility

### Deployment Steps
- [ ] Upload files to production server
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan db:seed --class=PaymentMethodSeeder --force`
- [ ] Activate payment methods
- [ ] Clear and cache configurations
- [ ] Set proper file permissions

### Post-Deployment
- [ ] Run diagnostic script
- [ ] Test checkout flow
- [ ] Monitor error logs
- [ ] Verify payment methods are working

## 🔍 Monitoring & Maintenance

### Log Files to Monitor

```bash
# Laravel application logs
tail -f storage/logs/laravel.log

# Web server logs (Apache)
tail -f /var/log/apache2/error.log

# Web server logs (Nginx)
tail -f /var/log/nginx/error.log
```

### Regular Health Checks

```bash
# Check payment methods status
php artisan tinker
PaymentMethod::where('is_active', true)->count();

# Check database connection
DB::connection()->getPdo();

# Check cache status
php artisan config:show
```

## 📞 Support & Contact

Jika masih mengalami masalah setelah mengikuti panduan ini:

1. Jalankan `php diagnose_payment_methods.php` dan kirim outputnya
2. Check error logs dan kirim pesan error yang relevan
3. Pastikan semua environment variables sudah benar
4. Verifikasi bahwa database connection berfungsi

## 🔄 Automated Deployment (Recommended)

Untuk deployment yang lebih mudah di masa depan, pertimbangkan untuk menggunakan:

1. **CI/CD Pipeline** (GitHub Actions, GitLab CI)
2. **Deployment Tools** (Deployer, Envoy)
3. **Container Deployment** (Docker)

### Example GitHub Actions Workflow

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    
    - name: Deploy to server
      run: |
        # SSH to server and run deployment script
        ssh user@server 'cd /path/to/project && ./deploy_production.sh'
```

---

**Last Updated**: $(date)
**Version**: 1.0
**Author**: Bestie BRo AI Assistant