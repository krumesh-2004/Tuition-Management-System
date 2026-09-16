# Brightpath Tuition Management System (TMS)

A complete, OOP-based PHP + MySQL Final Year Project implementing the
requirements and diagrams from the supplied SRS and Diagram Document.

---

## 1. Technology Stack

- **Backend:** PHP 8+ (procedural pages calling an OOP class layer)
- **Database:** MySQL / MariaDB (via PDO)
- **Frontend:** Custom HTML/CSS (no framework dependency), vanilla JS
- **Architecture:** MVC-style separation — `classes/` (Models), page files (Controllers + Views), `includes/` (shared layout)

## 2. Folder Structure

```
tms/
├── admin/              # Admin dashboard & management pages
├── student/            # Student dashboard & self-service pages
├── tutor/              # Tutor dashboard & reporting pages
├── classes/            # OOP classes (Database, User, Admin, Student, Tutor,
│                         Attendance, Payment, Timetable, Notification, Auth, Validator)
├── config/             # database.php - DB connection settings
├── includes/           # init.php bootstrap, header/footer/sidebar templates
├── assets/
│   ├── css/style.css   # Design system
│   ├── js/script.js    # UI interactions
│   └── uploads/        # Uploaded profile photos (students/, tutors/)
├── database/
│   └── tms_database.sql  # Full schema + sample data
├── index.php           # Landing page + login (Admin/Tutor/Student)
├── about.php           # Public About page
├── contact.php         # Public Contact page (saved to DB)
├── register.php        # Student self-registration (generates QR code)
└── logout.php
```

## 3. OOP Design Summary

- **`Database`** — Singleton PDO wrapper (encapsulation of connection details).
- **`User`** (abstract) — shared login/logout/password-hash logic;
  **`Admin`**, **`Student`**, **`Tutor`** extend it (inheritance) and each
  implement `getTableName()`, `getIdColumn()`, `getRole()` (abstraction/polymorphism).
- **`Attendance`**, **`Payment`**, **`Timetable`**, **`Notification`** — standalone
  entity classes encapsulating their own table's CRUD.
- **`Auth`** — central login dispatcher (tries Admin → Tutor → Student), session
  management, and role-based route guarding (`Auth::requireRole()`).
- **`Validator`** — reusable fluent validation helper used by every form.

## 4. Installation (XAMPP / WAMP / LAMP + phpMyAdmin)

1. **Copy the project** into your server's web root, e.g.
   - XAMPP: `C:\xampp\htdocs\tms`
   - WAMP: `C:\wamp64\www\tms`
   - Linux/LAMP: `/var/www/html/tms`

2. **Create the database:**
   - Open phpMyAdmin → **Import** tab → choose `database/tms_database.sql` → Go.
   - This creates the `tms_db` database, all tables, and sample data.

3. **Configure the connection** in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'tms_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');       // set your MySQL password if any
   ```

4. **Set folder permissions** so uploaded photos can be saved:
   - `assets/uploads/students/` and `assets/uploads/tutors/` must be writable.

5. **Browse to the app:**
   `http://localhost/tms/`

## 5. Test / Demo Login Credentials

| Role    | Email                 | Password    |
|---------|------------------------|-------------|
| Admin   | admin@tms.com          | admin123    |
| Tutor   | kasun@tms.com          | tutor123    |
| Tutor   | nadeesha@tms.com       | tutor123    |
| Tutor   | ruwan@tms.com          | tutor123    |
| Student | amal@student.com       | student123  |
| Student | ishara@student.com     | student123  |
| Student | dilshan@student.com    | student123  |

New students can also self-register at `register.php`; a unique QR code is
generated automatically for each new student.

## 6. Requirements Coverage (traceability)

| SRS ID | Feature | Where implemented |
|--------|---------|---------------------|
| FR-01  | User Login (Admin/Student/Tutor) | `index.php`, `classes/Auth.php` |
| FR-02  | Student Registration + QR code | `register.php`, `classes/Student.php::register()` |
| FR-03  | Student Profile | `student/profile.php` |
| FR-04  | Tutor Management (Admin CRUD) | `admin/tutors.php`, `classes/Tutor.php` |
| FR-05  | Tutor Details View (Student) | `student/tutors.php` |
| FR-06  | Attendance (QR-based + manual) | `admin/attendance.php`, `classes/Attendance.php` |
| FR-07  | Fee / Payment Management | `admin/payments.php`, `classes/Payment.php` |
| FR-08  | Payment Notification (simulated SMS) | `classes/Notification.php`, shown in `student/payment.php` |
| FR-09  | Timetable Management (Admin) | `admin/timetable.php` |
| FR-10  | Timetable Viewing (Student) | `student/timetable.php` |
| FR-11  | Student Record Management (Admin CRUD) | `admin/students.php` |
| FR-12  | Logout | `logout.php` |
| FR-13  | Public About/Contact pages | `about.php`, `contact.php` |
| FR-14  | Tutor daily class/income report | `tutor/report.php`, `classes/Tutor.php::submitReport()` |

Note: a real SMS gateway and payment gateway are not specified in the source
documents, so FR-08 is implemented as an in-app "notification" record (visible
to Admin and the Student) that stands in for an SMS provider — matching the
"Out of Scope / Not Confirmed" note in the SRS.

## 7. Security Notes

- All passwords are hashed with `password_hash()` (bcrypt) — never stored in plain text.
- All database queries use PDO prepared statements (protection against SQL injection).
- Sessions are regenerated on login and expire after inactivity (`SESSION_LIFETIME`).
- Role-based access control via `Auth::requireRole()` guards every admin/tutor/student page.
- Uploaded photos are restricted to image extensions and renamed before storage.

## 8. Notes for Presentation / Demo

- Use the demo logins above to show all three dashboards.
- Register a new student to show the live QR code generation.
- From Admin → Attendance, paste a student's QR code value (shown on their
  profile/dashboard) to demonstrate QR-based attendance marking.
- Record a payment as "Paid" from Admin → Payments to demonstrate the
  automatic parent-notification flow, then view it from Student → My Payments.
