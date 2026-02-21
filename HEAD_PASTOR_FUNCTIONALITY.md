# Head Pastor Functionality Documentation

## Overview
This document describes the head pastor functionality that allows administrators to assign head pastors to churches and gives head pastors access to church-specific administrative functions.

## Features Implemented

### 1. Head Pastor Assignment
- **Admins can assign head pastors to churches** through the church management interface
- **Head pastors can be removed** from churches by admins
- **Head pastor information is displayed** on the church details page

### 2. Head Pastor Permissions
- **Head pastors get access to church-specific administrative functions**:
  - Membership management
  - Financial management
  - Attendance tracking
  - Event management
  - Project management
  - Follow-up management
- **Access is restricted to the specific church** they are assigned to
- **Admins have full access to all churches**

### 3. Dashboard Integration
- **Head pastors see a welcome message** on their dashboard showing their assigned church
- **Quick links to church management functions** are provided
- **Church-specific statistics** are displayed when available

## Implementation Details

### Database Changes
- Added `head_pastor_user_id` column to the `churches` table
- Foreign key constraint to the `users` table
- Migration: `027_add_head_pastor_to_churches.php`

### Models Updated
- **Church Model**: Added methods for head pastor management
  - `assignHeadPastor()`
  - `removeHeadPastor()`
  - `getPossibleHeadPastors()`
  - `isHeadPastorOfAnyChurch()`
  - `getChurchByHeadPastor()`

### Controllers Updated
- **BaseController**: Added head pastor permission checking
- **DashboardController**: Added head pastor-specific dashboard information
- **ChurchController**: Added head pastor assignment/removal functionality
- **UnitController**: Added head pastor access to unit management
- **AttendanceController**: Added head pastor access to attendance management
- **FinanceController**: Added head pastor access to financial management
- **EventController**: Added head pastor access to event management
- **ProjectController**: Added head pastor access to project management
- **FollowUpController**: Added head pastor access to follow-up management
- **MemberDirectoryController**: Added head pastor access to member directory

### Session Management
- **Session class** updated with head pastor methods:
  - `isHeadPastor()`
  - `getHeadPastorChurchId()`

### Views Updated
- **Church Show View**: Added head pastor assignment interface
- **Dashboard View**: Added head pastor-specific welcome message and links

### Permissions System
- **New role**: `head_pastor` with specific church management permissions
- **Permission-based access control** for all controllers

## How to Use

### Assigning a Head Pastor
1. Go to the Church Management section
2. View the church details page
3. If no head pastor is assigned, you'll see an assignment form
4. Select a user from the dropdown list
5. Click "Assign Head Pastor"

### Head Pastor Access
1. When a user logs in and is assigned as a head pastor of a church
2. They will see a welcome message on their dashboard
3. They can access church-specific administrative functions
4. Their access is limited to the church they are assigned to

### Removing a Head Pastor
1. Go to the Church Management section
2. View the church details page
3. If a head pastor is assigned, you'll see a "Remove" button
4. Click the button to remove the head pastor assignment

## Security Features
- **Role-based access control** ensures proper permissions
- **Church-specific restrictions** prevent cross-church access
- **CSRF protection** on all forms
- **Admin-only assignment** functionality
- **Session-based caching** for performance

## Routes Added
- `POST /churches/{id}/assign-head-pastor` - Assign head pastor to church
- `POST /churches/{id}/remove-head-pastor` - Remove head pastor from church

## Files Modified/Added
- **Models**: `Church.php`, `User.php`
- **Controllers**: `BaseController.php`, `DashboardController.php`, `ChurchController.php`, `UnitController.php`, `AttendanceController.php`, `FinanceController.php`, `EventController.php`, `ProjectController.php`, `FollowUpController.php`, `MemberDirectoryController.php`
- **Core**: `Session.php`
- **Migrations**: `027_add_head_pastor_to_churches.php`
- **Views**: `churches/show.php`, `dashboard/index.php`
- **Routes**: `web.php`

## Testing
The functionality has been implemented and should work as expected. For testing:
1. Create a test user account
2. Assign them as head pastor to a church
3. Log in as that user
4. Verify access to church-specific functions
5. Verify restrictions on other churches

## Troubleshooting
- If head pastor functionality doesn't work, check database migration status
- Verify user permissions and role assignments
- Check session caching for head pastor status
- Ensure proper route configuration