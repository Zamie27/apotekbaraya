# Email Queue System Documentation - Apotek Baraya

## Overview

Sistem queue email notifications telah diimplementasikan untuk menangani pengiriman email secara asynchronous, sehingga tidak memblokir proses utama aplikasi. Sistem ini menggunakan Laravel Queue dengan database driver.

## Components

### 1. Job Class: SendEmailNotification

**Location:** `app/Jobs/SendEmailNotification.php`

**Purpose:** Menangani pengiriman email notifications secara asynchronous

**Features:**
- Queue khusus untuk email (`emails` queue)
- Retry mechanism (3 attempts)
- Timeout handling (60 seconds)
- Comprehensive logging
- Error handling dan failure tracking

### 2. Mailable Class: UserNotificationMail

**Location:** `app/Mail/UserNotificationMail.php`

**Purpose:** Template email untuk berbagai jenis notifikasi user

**Features:**
- Dynamic subject berdasarkan notification type
- Configurable sender information
- Data passing ke email template

### 3. Email Template

**Location:** `resources/views/emails/user-notification.blade.php`

**Purpose:** Template HTML untuk email notifications

**Features:**
- Responsive design dengan Tailwind CSS
- Support untuk berbagai notification types:
  - User Created
  - User Updated
  - User Deleted
  - Login Attempt
- Professional styling dengan branding Apotek Baraya

### 4. Queue Command

**Location:** `app/Console/Commands/ProcessEmailQueue.php`

**Purpose:** Command untuk menjalankan queue worker

**Usage:**
```bash
php artisan email:process-queue [options]
```

**Options:**
- `--timeout=30`: Timeout per job (default: 30 seconds)
- `--sleep=3`: Sleep time between jobs (default: 3 seconds)
- `--tries=3`: Maximum retry attempts (default: 3)

### 5. Enhanced EmailNotificationService

**Location:** `app/Services/EmailNotificationService.php`

**New Methods:**
- `queueUserCreatedNotification()`
- `queueUserUpdatedNotification()`
- `queueUserDeletedNotification()`
- `queueLoginAttemptNotification()`

## Usage Examples

### 1. Queue User Created Notification

```php
use App\Services\EmailNotificationService;

$emailService = new EmailNotificationService();
$emailService->queueUserCreatedNotification($user, [
    'created_by' => auth()->user()->name,
    'ip_address' => request()->ip()
]);
```

### 2. Queue User Updated Notification

```php
$emailService->queueUserUpdatedNotification($user, [
    'updated_by' => auth()->user()->name,
    'changes' => $user->getChanges(),
    'ip_address' => request()->ip()
]);
```

### 3. Queue Login Attempt Notification

```php
$emailService->queueLoginAttemptNotification($user, [
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'attempted_at' => now()
]);
```

## Queue Management

### Starting Queue Worker

**Option 1: Using Custom Command**
```bash
php artisan email:process-queue --timeout=30 --sleep=3 --tries=3
```

**Option 2: Using Laravel Queue Command**
```bash
php artisan queue:work --queue=emails --sleep=3 --tries=3 --timeout=60
```

**Option 3: Using Batch Script (Windows)**
```bash
start-queue-worker.bat
```

### Monitoring Queue

**Check Queue Status:**
```bash
php artisan queue:monitor emails
```

**View Failed Jobs:**
```bash
php artisan queue:failed
```

**Retry Failed Jobs:**
```bash
php artisan queue:retry all
```

**Clear Failed Jobs:**
```bash
php artisan queue:flush
```

## Configuration

### Queue Configuration

**File:** `config/queue.php`

Ensure database driver is configured:
```php
'default' => env('QUEUE_CONNECTION', 'database'),

'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
    ],
],
```

### Mail Configuration

**File:** `config/mail.php`

Configure mail settings:
```php
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'noreply@apotekbaraya.com'),
    'name' => env('MAIL_FROM_NAME', 'Apotek Baraya'),
],
```

### Environment Variables

Add to `.env`:
```env
QUEUE_CONNECTION=database
MAIL_FROM_ADDRESS=noreply@apotekbaraya.com
MAIL_FROM_NAME="Apotek Baraya"
```

## Database Tables

### Jobs Table

Migration sudah ada di Laravel. Jika belum, jalankan:
```bash
php artisan queue:table
php artisan migrate
```

### Failed Jobs Table

```bash
php artisan queue:failed-table
php artisan migrate
```

## Logging

Semua aktivitas queue dicatat di:
- **Laravel Log:** `storage/logs/laravel.log`
- **Queue Worker Log:** Console output saat menjalankan worker

**Log Levels:**
- `INFO`: Job dispatched, processed successfully
- `WARNING`: Job retried
- `ERROR`: Job failed permanently

## Best Practices

### 1. Production Deployment

- Gunakan supervisor atau systemd untuk menjalankan queue worker
- Set up monitoring untuk queue worker
- Configure proper logging rotation

### 2. Performance Optimization

- Adjust `--sleep` parameter berdasarkan volume email
- Monitor memory usage queue worker
- Set appropriate `--timeout` values

### 3. Error Handling

- Monitor failed jobs regularly
- Set up alerts untuk failed jobs
- Review error logs untuk pattern issues

### 4. Security

- Validate data sebelum dispatch ke queue
- Sanitize email content
- Monitor untuk spam atau abuse

## Troubleshooting

### Common Issues

**1. Queue Worker Not Processing Jobs**
- Check database connection
- Verify jobs table exists
- Ensure queue worker is running

**2. Email Not Sending**
- Check mail configuration
- Verify SMTP settings
- Check email template syntax

**3. Jobs Failing**
- Review error logs
- Check job payload
- Verify dependencies

**4. Memory Issues**
- Restart queue worker regularly
- Monitor memory usage
- Optimize job payload size

## Monitoring Commands

```bash
# Check queue size
php artisan queue:monitor emails

# View queue statistics
php artisan queue:stats

# Clear all jobs
php artisan queue:clear

# Restart queue workers
php artisan queue:restart
```

## Future Enhancements

1. **Email Templates:** Tambah template untuk notification types lain
2. **Queue Priorities:** Implement priority queues untuk email urgent
3. **Batch Processing:** Group multiple notifications
4. **Email Analytics:** Track open rates, click rates
5. **WhatsApp Integration:** Extend untuk WhatsApp notifications
6. **Admin Dashboard:** UI untuk monitoring queue status

---

**Created:** $(date)
**Version:** 1.0
**Author:** Apotek Baraya Development Team