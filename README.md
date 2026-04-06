# Placement Cell Management System

A Web Technology project for managing a college placement cell. Built with HTML5, CSS3 (Bootstrap 5), JavaScript, PHP, and MySQL.

## Features
- **Authentication**: Secure login/registration for Students. Separate Admin login. Password hashing.
- **Student Dashboard**: View available job postings, apply for jobs, upload PDF resumes, and track application status.
- **Admin Dashboard**: Comprehensive stats, add companies, post job opportunities, manage student applications, and view registered students.
- **Database**: Relational database with tables for students, admins, companies, jobs, and applications.
- **Responsive UI**: Built using Bootstrap 5 for a modern, clean, and responsive design across devices.

## Requirements
- XAMPP (Apache & MySQL)
- Web Browser

## Instructions to Run locally using XAMPP

1. **Start XAMPP**: Open XAMPP Control Panel and start **Apache** and **MySQL**.
2. **Setup Database**:
   - Open your browser and go to `http://localhost/phpmyadmin`
   - Create a new database named `placement_cell` (Or you can just import the SQL file as it contains `CREATE DATABASE` statement).
   - Go to the **Import** tab.
   - Choose the `database.sql` file from this project folder.
   - Click **Import** (or **Go**).
3. **Project Files**:
   - Ensure this entire project folder (`placement cell`) is located inside your XAMPP `htdocs` directory (typically `C:\xampp\htdocs\placement-cell`).
4. **Access the System**:
   - Open your browser and navigate to: `http://localhost/placement-cell`

## Default Access Credentials

**Admin Login:**
- Username: `admin`
- Password: `password`

*(You can also register as a new student from the landing page to access the student portal)*

## Folder Structure
```
/placement-cell
|-- /css               # Custom CSS (if any)
|-- /js                # Custom JS (if any)
|-- /images/resumes    # Uploaded PDFs will be stored here
|-- /includes
|   |-- db.php         # Database connection logic
|   |-- header.php     # Reusable HTML header & navbar
|   |-- footer.php     # Reusable HTML footer
|-- database.sql       # SQL schema and default data
|-- index.php          # Landing page
|-- register.php       # Student registration
|-- login.php          # Student/Admin login
|-- dashboard.php      # Student dashboard
|-- admin_dashboard.php# Admin dashboard
|-- logout.php         # Logout script
|-- README.md          # Project instructions
```
