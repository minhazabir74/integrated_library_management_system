# Integrated Library Management System

A dynamic, web-based library platform designed to automate book inventory, structure distinct borrowing workflows, and manage fine calculations. Features dual-role dashboards tailored specifically for Admins and Students.

---

## Key Features

* **Role-Based Authentication** Separate, secure log-in portals tailored for both Library Admins and University Students.

* **Inventory & Stock Management** Complete control over the book catalog, featuring automated category filtering and real-time stock tracking.

* **Borrow Request Lifecycle** Students can request books seamlessly, while Admins retain full authority to approve or reject requests in real-time.

* **Automated Penalty Engine** Built-in return logic that automatically calculates specific fines and structural penalties for lost books.

* **Real-Time Student Directory** A unified admin directory featuring instant lookup capabilities to track registered student profiles and statuses.

---

## Getting Started and Setup

### Prerequisites
* XAMPP Server installed on your local machine.

### Installation Steps

1. **Deploy Project Folder** Place the complete project directory exactly into your local XAMPP root:  
   `C:/xampp/htdocs/library_system/`

2. **Launch Services** Open your XAMPP Control Panel and start both Apache and MySQL network modules simultaneously.

3. **Database Configuration** Open your web browser and navigate to: http://localhost/phpmyadmin/  
   Create a brand new database named exactly: `library_db`  
   Select the database and use the Import tab to upload the project's `.sql` schema file.

4. **Run the Application** Open a new browser tab and visit the local hosting link:  
   http://localhost/library_system/

---

## Security and Environment Notice

* **Credential Management** User and admin passwords within this staging build are stored as plain text within the relational tables.

* **Environment Configuration** The local deployment architecture utilizes default XAMPP credentials (root user with an empty password setup).

* **Deployment Status** This system is curated exclusively for local sandbox evaluations and academic demonstrations; it is not suitable for live production deployment.

---

## Developer Team's Name and Student ID

* **Omar Minhaz Abir** : 242-053-042

* **Emon Islam** : 242-006-042

* **Marzia Khondokar Soha** : 242-031-042

### Academic Supervised By:
* **Lecturer: Halima Mowla** Department of Computer Science and Engineering (CSE), Primeasia University
