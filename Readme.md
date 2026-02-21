Church Reporting & Administrative Portal

A dynamic multi-department reporting portal designed for churches to manage members, units, leadership roles, media assets, finances, events, and internal operations — with powerful reporting and flexible multi-unit assignments.

🚀 Technology Stack

Backend: PHP 8+ (MVC Architecture)

Database: MySQL 8 (MySQLi with prepared statements)

Frontend: HTML5, Bootstrap 5, jQuery, AJAX

Security: CSRF protection, role-based access control, password hashing, input sanitization

📁 Project Structure
/
├── app/
│   ├── core/          
│   ├── controllers/   
│   ├── models/        
│   ├── views/         
│   ├── middleware/    
│   └── utilities/     
├── config/            
├── public/            
├── routes/            
├── assets/            
├── uploads/           
└── database/          

🛠️ Installation
1. Clone Repository
git clone <repository-url>
cd church-reporting-portal

2. Install Dependencies
composer install

3. Configure Database

Update config/database.php

Create database: church_reporting_portal

4. Set Permissions
chmod -R 755 uploads/

5. Configure Web Server

Document root → /public
Ensure mod_rewrite is enabled.

🔧 Configuration
Database (config/database.php)
return [
    'host' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'church_reporting_portal',
];

App Config (config/config.php)

Base URL

Timezone

Upload limits

Allowed file types

📌 System Overview

This portal is built for flexible departmental structures, powerful data collection, and seamless church-wide reporting workflows.

KEY CAPABILITIES

Units can be created, renamed, updated, or deleted at any time.

A single member can:

Belong to multiple units at the same time

Serve as director over multiple units simultaneously

Each unit submits reports, attendance, finances, projects, and activity logs.

Leadership teams can track department performance and service metrics through aggregated dashboards.

📝 Development Architecture
Core Components

BaseModel: Shared CRUD logic

BaseController: Request handling + response rendering

Database: Singleton database connection

Router: Clean routing with middleware

Security: Authentication, validation, hashing

FileUpload: For media & document uploads

View System: Dynamic layouts with reusable UI components

📊 Modules & Features
1. User & Role Management

Admin

Directors (multi-unit capable)

Unit officers

Pastors/Leadership

Standard users

Each user can have:

Multiple unit memberships

Multiple directorship roles

2. Dynamic Unit Management

Add new units

Edit unit details

Delete units

Assign directors (multiple per unit if needed)

Assign multiple members to multiple units

3. Reporting System

Each unit can submit structured reports such as:

Weekly reports

Event reports

Departmental activities

Outreach & evangelism reports

Media & production reports

Technical & logistics reports

Reports support:

File attachments

Photos

PDF exports

Timestamping

4. Attendance & Engagement Tracking

Service attendance

Unit attendance

Leader activity logs

5. Finance Reporting (per unit)

Offerings

Departmental budgets

Expenditures

Donations & project contributions

6. Media & Resource Library

Upload sermons (audio, video, PDF)

Department files

Flyers, posters, materials

7. Project & Activity Management

Missions

Events

Outreach

Multi-unit collaborations

8. Administrative Dashboard

Provides:

Overview of all units

Pending and submitted reports

Attendance summaries

Financial snapshots

Engagement statistics

🔐 Security Features

Role-based access control (RBAC)

CSRF protection

SQL injection prevention

Password hashing (bcrypt)

XSS sanitization

Secure session handling

File upload validation

📊 Database Requirements

System supports many-to-many relationships:

users

units

unit_user (for multi-unit membership)

unit_directors (for multi-unit leadership)

reports

report_files

attendance

finance_records

🧪 Testing
vendor/bin/phpunit

📄 License

Proprietary – For internal use only

👥 Development Team

Built with scalability, modularity, and flexible department reporting structures to support modern church administration.