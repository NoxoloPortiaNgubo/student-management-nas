# Student Management System

A robust, enterprise-grade web application built with Laravel 12, MySQL, and Laravel Herd that manages student life cycles, course enrolments, and academic reporting. This project features a custom multi-role authentication gatekeeper to provide tailored workspaces for Administrators, Lecturers, and Students.

---

##  The Development Team (Group Initials: NAS)
* **Group Leader & QA:** Noxolo Portia — Multi-role authentication, middleware security gates, repository management.
* **Backend Developer:** Alakhe — Database migration optimization, Report controllers, and layout views.
* **Backend Developer:** Sesona — Student CRUD architecture and Service Layer business logic.

---

##  Key Features
* **Multi-Role Security Wall:** Custom route middleware guarding specific panels based on application user roles (admin, lecturer, student).
* **Student CRUD Architecture:** A decoupled Service Layer pattern handling safe creation, editing, and storage of student records.
* **Course & Enrolment Management:** Dynamic mapping of students to academic courses.
* **Academic Reporting:** Specialized controller infrastructure to generate student grade and enrolment summaries.

---

##  Technology Stack
* **Framework:** Laravel 12 (PHP v8.x)
* **Local Environment:** Laravel Herd / Laragon
* **Database Service:** DBngin / MySQL 8.0 / SQLite
* **Frontend Basics:** Blade Templates, HTML, CSS, JavaScript

---

##  Application User Guide

### 1. Authentication & Security Gatekeeper 
* **Accessing the App:** Users are greeted by a central welcome gate to log into their specific workspace.
* **Multi-Role Login:** Enter your registered credentials at the login interface. The custom security middleware will automatically detect your role (Admin, Lecturer, or Student) and route you to your dedicated dashboard panel.

### 2. Student CRUD Management 
* **Viewing Students:** Navigate to the student directory page to view a complete, organized table of all active student records.
* **Adding Profiles:** Click the "Add Student" action button to open the input form. Fill in the required student details and submit to securely store the record.
* **Updating Records:** Click the "Edit" option next to any student profile to modify their information dynamically.
* **Removing Profiles:** Use the "Delete" action button to permanently purge outdated student profiles from the system database.

### 3. Academic Reporting & Enrolments 
* **Course Enrolments:** Users can access the enrolment module to dynamically map and assign students to their respective academic courses.
* **Generating Reports:** Navigate to the reporting section to compile and view summarized overviews of student academic performance and enrollment metrics.
