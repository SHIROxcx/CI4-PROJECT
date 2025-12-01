# Environment Configuration Setup Guide

## Overview

This project now uses environment-based configuration. This means you can have different settings for:

- **Local Development** (localhost)
- **Production** (Hostinger with SSL/TLS)

This solves the JSON parsing error you were experiencing on Hostinger!

---

## Files Created

### 1. `.env.example`

Template file showing all available configuration options. **Do NOT modify** - it's just documentation.

### 2. `.env.local`

**For LOCAL DEVELOPMENT ONLY**

- Contains localhost settings
- Uses HTTP (not HTTPS)
- Database debug is ON
- Has your local MySQL credentials (Shiro user, capsdb database)

### 3. `.env.hostinger.example`

**Template for HOSTINGER PRODUCTION**

- Shows how to configure for Hostinger
- Has instructions on where to find Hostinger database credentials
- Uses HTTPS with ForceHTTPS enabled
- Database debug is OFF (security)

---

## How Environment Configuration Works

### For Local Development (localhost:8080)

CodeIgniter reads configuration in this order:

1. First, it loads from `app/Config/App.php` and `app/Config/Database.php`
2. Then, it checks for a `.env` file
3. If `.env` exists, values from `.env` **override** the config files

**Currently on your local machine:**

- The system reads `.env.local` (which has localhost settings)
- Your site works perfectly on localhost because HTTPS is disabled

### For Production (Hostinger)

1. Create a `.env` file on Hostinger (copy from `.env.hostinger.example`)
2. Add your Hostinger database credentials
3. The system will automatically:
   - Force HTTPS (fixing the JSON parsing error)
   - Disable database debugging (so no errors print before JSON)
   - Use correct Hostinger database credentials

---

## Configuration Files Changed

### 1. **`app/Config/App.php`**

```php
public function __construct()
{
    parent::__construct();

    // Load base URL from .env file if available
    $this->baseURL = env('app.baseURL', 'http://localhost:8080/');

    // Load SSL/TLS setting from .env
    $this->forceGlobalSecureRequests = (bool) env('app.forceGlobalSecureRequests', false);
}
```

### 2. **`app/Config/Database.php`**

```php
public function __construct()
{
    parent::__construct();

    // Load database configuration from .env file
    $this->default['hostname'] = env('database.hostname', 'localhost');
    $this->default['username'] = env('database.username', 'root');
    $this->default['password'] = env('database.password', '');
    // ... and more settings from .env
}
```

### 3. **`app/Config/Filters.php`**

Now conditionally enables ForceHTTPS filter only when configured:

```php
public array $required = [
    'before' => [
        env('app.forceGlobalSecureRequests', false) ? 'forcehttps' : '',
        'pagecache',
    ],
    // ...
];
```

---

## Setup Instructions for Hostinger

### Step 1: Get Your Database Credentials from Hostinger

1. Log in to **Hostinger Control Panel**
2. Go to **Databases** or **MySQL Databases**
3. Find your database and click **Manage** or **Connection Details**
4. Copy these values:
   - Database Name (e.g., `youruser_capsdb`)
   - Database Username (e.g., `youruser_dbuser`)
   - Database Password (the one you created)
   - Database Host (usually `localhost`)

### Step 2: Create `.env` File on Hostinger

1. Copy the content of `.env.hostinger.example`
2. Use FTP/SFTP to upload or create a new file on Hostinger in the root directory
3. Name it `.env` (just `.env`, not `.env.hostinger`)
4. Replace the placeholder values:
   ```
   app.baseURL=https://yourdomain.com/
   database.hostname=localhost
   database.username=youruser_dbuser
   database.password=your_secure_password
   database.database=youruser_capsdb
   ```

### Step 3: Set File Permissions

```bash
chmod 600 .env
```

This restricts the .env file so only the owner can read it (security).

### Step 4: Test Your Site

1. Visit `https://yourdomain.com`
2. Try loading the admin users page
3. Open browser Developer Tools (F12) → Console
4. Check if the JSON error is gone

---

## Key Differences Between Local and Production

| Setting                         | Local (localhost)        | Production (Hostinger)    |
| ------------------------------- | ------------------------ | ------------------------- |
| `app.baseURL`                   | `http://localhost:8080/` | `https://yourdomain.com/` |
| `app.forceGlobalSecureRequests` | `false`                  | `true`                    |
| `database.debug`                | `true`                   | `false`                   |
| `CI_DEBUG`                      | `1`                      | `0`                       |
| `LOG_LEVEL`                     | `debug`                  | `error`                   |

---

## What This Fixes

### The JSON Parsing Error

**Before:** On Hostinger, when ForceHTTPS redirected HTTP requests to HTTPS, it output redirect headers that corrupted your JSON:

```
HTTP/1.1 301 Moved Permanently
Location: https://yourdomain.com/...
[blank line]
{"success": true, "users": [...]}
```

**After:** Now that `app.baseURL` is correctly set to `https://` and `forceGlobalSecureRequests` is enabled:

- Users access the site via HTTPS directly
- No redirect happens
- JSON response is clean
- Browser receives valid JSON and can parse it

### Database Connection Issues

**Before:** Hardcoded localhost credentials meant Hostinger couldn't connect
**After:** `.env` file allows different credentials per environment

---

## Important Notes

### DO NOT commit `.env` to Git

Add this to your `.gitignore`:

```
.env
.env.local
.env.*.php
```

This prevents accidentally uploading production passwords to Git.

### CodeIgniter's .env Loading

By default, CodeIgniter looks for `.env` file in the project root. If it doesn't find `.env`, it uses the default values from config files.

---

## Troubleshooting

### Still seeing JSON parsing error?

1. **Verify `.env` is in the correct location:**

   - Should be in root folder (same level as `composer.json`, `spark`, `app/`)
   - Not in `app/Config/`

2. **Check that database.debug is false:**

   ```
   database.debug=false
   ```

3. **Verify database credentials are correct:**

   - Test connection from Hostinger's MySQL manager
   - Make sure username/password/database name are exactly right

4. **Check Hostinger logs:**

   - Usually at `/logs/apache_errors.log` or `/logs/error_log`
   - May show actual database connection errors

5. **Verify HTTPS is working:**
   - Visit `http://yourdomain.com` - should redirect to `https://yourdomain.com`

### Site shows blank page?

1. Check Hostinger error logs
2. Verify all database settings in `.env`
3. Make sure file permissions are correct (755 for folders, 644 for files)
4. Check that PHP is version 8.1+

---

## Environment Variables Available

### App Settings

- `app.baseURL` - Your application URL
- `app.forceGlobalSecureRequests` - Force HTTPS (true/false)
- `CI_DEBUG` - Debug mode (1 for on, 0 for off)

### Database Settings

- `database.hostname` - Database server hostname
- `database.username` - Database username
- `database.password` - Database password
- `database.database` - Database name
- `database.port` - Database port (usually 3306)
- `database.driver` - DB driver (MySQLi, Postgre, etc.)
- `database.charset` - Character set (utf8mb4)
- `database.collation` - Collation (utf8mb4_general_ci)
- `database.debug` - Show database errors (true/false)

### Logging Settings

- `LOG_LEVEL` - Log level (debug, info, error, etc.)
- `LOG_CHANNEL` - Where to log (single, daily, etc.)

---

## How to Update Settings in Production

If you need to change settings on Hostinger:

1. Connect via FTP/SFTP
2. Edit the `.env` file directly
3. Changes take effect immediately
4. No need to redeploy code

---

## Questions?

Refer to:

- `.env.example` - See all available options
- `.env.local` - Example of working development configuration
- `.env.hostinger.example` - Instructions for Hostinger setup
