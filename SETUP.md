# 🚀 Setup Instructions

## Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Composer
- Apache with mod_rewrite enabled (or Nginx)

## Installation Steps

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
Copy `.env.example` to `.env` and update the values:
```bash
cp .env.example .env
```

Edit `.env` file:
```env
APP_URL=http://localhost/ADMIN_PORTAL
TIMEZONE=UTC
DEBUG=false

DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=your_password
DB_DATABASE=church_reporting_portal
```

### 3. Create Database
```sql
CREATE DATABASE church_reporting_portal;
```

### 4. Set Permissions
```bash
# Windows (PowerShell)
icacls uploads /grant Users:F /T
icacls storage /grant Users:F /T

# Linux/Mac
chmod -R 755 uploads/
chmod -R 755 storage/
```

### 5. Configure Web Server

#### Apache
- Document root should point to `/public` directory
- Ensure `mod_rewrite` is enabled
- `.htaccess` file is already configured

#### Nginx
Add to your server block:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 6. Run Database Migrations
```bash
php database/migrate.php up
```

### 7. Create Admin User
```bash
php database/seeds/create_admin_user.php
```

Default credentials:
- Email: `admin@church.com`
- Password: `admin123`

⚠️ **Change the password after first login!**

### 8. Test Installation
Visit: `http://localhost/ADMIN_PORTAL`

You should see the login page. Login with admin credentials to access the dashboard.

## Next Steps

1. ✅ Database migrations - Complete
2. ✅ Admin user creation - Complete
3. 🔄 Start building modules (Units, Reports, etc.)

## Project Structure

```
ADMIN_PORTAL/
├── app/              # Application code
│   ├── core/         # Core classes (Database, Router, etc.)
│   ├── controllers/  # Controllers
│   ├── models/       # Models
│   ├── views/        # Views
│   ├── middleware/   # Middleware
│   └── utilities/    # Utility classes
├── config/           # Configuration files
├── public/           # Public assets (web root)
├── routes/           # Route definitions
├── database/         # Database migrations/seeds
└── uploads/          # Uploaded files
```

## Development Status

✅ **Phase 1 Complete**: Foundation & Core Infrastructure
- Project structure created
- Core classes implemented
- BaseModel and BaseController ready
- Security utilities in place
- Routing system functional

✅ **Phase 2 Complete**: Database Schema & Authentication
- 12 database tables created
- Migration system functional
- User model with authentication
- Complete login/logout system
- Role-based access control (RBAC)
- Admin user seeder

🔄 **Next**: Core Modules (Units, Reports, Attendance, etc.)

See `DATABASE_SETUP.md` for detailed database setup instructions.

