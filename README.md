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

---

##  Our Database Setup

Since we are using Laravel, our tables are built using migration files. Instead of a separate student table, our group decided to put all users into one place and separate them by roles. 

### 1. users table
This table holds everyone who can log into the app, including admins, lecturers, and students.
* `id` - The unique number for each user.
* `name` and `email` - The user's name and login email.
* `password` - The encrypted login password.
* `role` - This decides if the user is an 'admin', 'instructor', or 'student'.
* `student_number` - The unique school ID (only filled in if they are a student).
* `phone` and `date_of_birth` - Extra personal details for the student profile.

### 2. courses table
This table holds the classes that are available in the system.
* `id` and `code` - The database ID and the short course code (like IT-101).
* `name` and `description` - The title of the course and what it is about.
* `credits` - How many credits the course is worth (defaults to 15).
* `max_capacity` - The max number of students allowed in the class (defaults to 30).
* `instructor_id` - Links back to the user who is teaching the class.

### 3. enrolments table
This table connects students to the courses they want to take.
* `id` - The unique ID for the enrollment record.
* `student_id` - Links back to the user's ID in the users table.
* `course_id` - Links back to the course ID in the courses table.
* `status` - Shows if the application is pending, approved, rejected, or withdrawn.
* `grade` and `mark` - The final letter grade and the percentage score (0-100).

---

### How the tables connect (Foreign Keys):
* If an instructor account is deleted, the course stays active but the `instructor_id` just turns to empty (NULL) so nothing breaks.
* If a student user account is deleted, all of their course enrollments are automatically deleted too (cascade delete) so we don't have old data hanging around.
* A student cannot enroll in the exact same course twice because the database blocks duplicate student-course pairs.
