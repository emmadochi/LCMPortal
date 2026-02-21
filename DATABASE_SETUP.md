# 🗄️ Database Setup Guide

## Step 1: Configure Database

Edit `.env` file and set your database credentials:

```env
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=your_password
DB_DATABASE=church_reporting_portal
```

## Step 2: Create Database

Open MySQL (phpMyAdmin or command line) and create the database:

```sql
CREATE DATABASE church_reporting_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Step 3: Run Migrations

From the project root directory, run:

```bash
php database/migrate.php up
```

This will create all tables:
- ✅ users
- ✅ units
- ✅ unit_user (junction table)
- ✅ unit_directors (junction table)
- ✅ reports
- ✅ report_files
- ✅ attendance
- ✅ finance_records
- ✅ media
- ✅ projects
- ✅ project_units (junction table)
- ✅ activity_logs

## Step 4: Create Admin User

Run the seeder to create the default admin user:

```bash
php database/seeds/create_admin_user.php
```

**Default Admin Credentials:**
- Email: `admin@church.com`
- Password: `admin123`

⚠️ **IMPORTANT**: Change the password after first login!

## Step 5: Test Login

1. Start your web server (XAMPP Apache)
2. Visit: `http://localhost/ADMIN_PORTAL`
3. Login with admin credentials
4. You should see the dashboard

## Migration Commands

### Run All Migrations
```bash
php database/migrate.php up
```

### Rollback All Migrations
```bash
php database/migrate.php down
```

### Fresh Start (Drop all tables and recreate)
```bash
php database/migrate.php fresh
```

## Database Schema Overview

### Core Tables
- **users** - User accounts and authentication
- **units** - Church units/departments
- **unit_user** - Many-to-many: Users can belong to multiple units
- **unit_directors** - Many-to-many: Users can direct multiple units

### Reporting Tables
- **reports** - Unit reports
- **report_files** - Attachments for reports
- **attendance** - Attendance records
- **finance_records** - Financial transactions

### Additional Tables
- **media** - Media library files
- **projects** - Projects and events
- **project_units** - Many-to-many: Projects can involve multiple units
- **activity_logs** - System activity audit trail

## Troubleshooting

### Migration Fails
- Check database credentials in `.env`
- Ensure database exists
- Check MySQL user has CREATE/DROP permissions
- Check for foreign key constraint errors

### Admin User Creation Fails
- Ensure migrations have run successfully
- Check that users table exists
- Verify database connection

### Foreign Key Errors
- Run migrations in order (they're numbered)
- Check that referenced tables exist
- Verify table names match exactly

## Next Steps

After database setup:
1. ✅ Test login functionality
2. ✅ Verify admin user can access dashboard
3. 🔄 Start building modules (Units, Reports, etc.)

---

**Phase 2 Complete!** Database schema and authentication are ready! 🎉

