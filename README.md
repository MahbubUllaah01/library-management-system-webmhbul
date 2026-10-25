# Library Management System

A web-based Library Management System built with **HTML, CSS, JavaScript, PHP and MySQL**.

> **Note:** This is a practice re-implementation of my 6th-semester *Web Tools and Technology* course project, rebuilt in 2026 to refresh the same concepts. The original course submission is stored separately.

## Features
- Add, edit, delete and search books (title, author, ISBN, category, copies)
- Add and delete members, with email validation
- Issue books with a due date and return them; availability updates automatically
- Dashboard with totals and an overdue list
- Server-side validation (prepared statements, input escaping) and client-side form checks

## Technologies
HTML5, CSS3, JavaScript, PHP (PDO), MySQL

## How to Run
1. Install XAMPP (or any PHP + MySQL stack) and start Apache and MySQL.
2. Copy this folder into `htdocs`.
3. Open phpMyAdmin and import `schema.sql` (creates the `library_db` database and sample data).
4. If your MySQL user or password differs, edit `db.php`.
5. Open `http://localhost/library-management-system/` in your browser.

## Author
**Mahbub Ullah**, B.Sc. in Computer Science & Engineering, IIUC
GitHub: [MahbubUllaah01](https://github.com/MahbubUllaah01)
