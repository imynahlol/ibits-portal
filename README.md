# iBITS Portal - System & Specifications

> **Motto:** "We Code as One"

The official web portal for the iBITS organization. This system is designed to manage member data, track fines, showcase events, distribute forms, provide help desk support, and handle the organizational by-laws (CBL) approval workflow.

## 🛠️ Tech Stack

This project uses a classic, robust stack:
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP 8+
- **Database:** MySQL / MariaDB
- **Local Server:** XAMPP (Apache & MySQL)
- **Version Control:** Git & GitHub
- **Editor:** VSCode

*(Note: This project does NOT use Node.js, Next.js, or TypeScript. It is a pure PHP application served through XAMPP.)*

## ✨ Features by Role

### 1. Public Portal (Unauthenticated)
- **Homepage:** Hero section, motto, CTA to Join/Login.
- **About Us:** Mission, Vision, Core Values.
- **Org Structure:** Interactive chart, Member Directory (pictures/roles), Dynamic Population Statistics.
- **Events Showcase:** Past and upcoming public events.
- **FAQs:** View-only general FAQs.

### 2. Authentication System
- Secure Member Login.
- Admin/Officer Login (Redirects to specific dashboards based on role).
- **Registration:** Full Name, Student Number, Sex, Program, Year Level, Password.

### 3. Member Dashboard
- **Overview Widget:** Personal fines, upcoming events, open help desk tickets.
- **My Fines:** View-only access to personal fines (amount, reason, status, due date).
- **Events/GPOA:** View-only org calendar (filter by Past, Current, Ongoing, Future).
- **Forms Hub:** Excuse Letter (fillable, save, print), CBL (Org By Laws PDF view).
- **Help Desk (STRAW Support):** Ticketing system for concerns. Track status (Open, In Progress, Resolved).

### 4. Admin/Officer Dashboard (RBAC)
- **Executive (President, VP):** Full edit access to Org Structure, co-manage events, final CBL approval.
- **Finance (Treasurer, Auditor, etc.):** Manage financial templates, add/edit fines, mark paid/unpaid, view all org fines.
- **Events & Documentation (Secretary, Institutional, etc.):** Create/edit events, manage Excuse Letter template. Secretary has view-only access to all fines.
- **CBL Workflow:** Strict verification chain: Adviser -> President -> VP.

### 5. Settings & Personalization
- Edit Profile (Name, Sex, Birthday, Profile Picture).
- Change Password.
- Appearance: Light Mode / Dark Mode.
- Support: FAQs, Contact Support, Submit Ticket.

## 📂 Folder Structure

```text
ibits-portal/
├── assets/             # CSS, JS, Images
├── config/             # Database connection (db.php)
├── includes/           # Reusable components (header, footer, auth checks)
├── members/            # Member dashboard pages
├── admin/              # Admin/Officer dashboard pages
├── uploads/            # User profile pictures, CBL PDFs, Excuse letters
├── index.php           # Public Homepage
├── login.php           # Login page
├── register.php        # Registration page
└── .gitignore