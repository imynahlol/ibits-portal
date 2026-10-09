# iBITS Portal

Multi-page PHP web application for the **Institute of Brilliant Information
Technology Students** (PUPLC). Built as the official portal for members,
officers, and public visitors — with role-based access, fines tracking,
finance ledger, event calendar, form publishing, a help desk ticketing
system, and a two-stage CBL (Constitution & By-Laws) approval workflow.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Quick Start](#quick-start)
- [Environment Keys](#environment-keys)
- [Pages](#pages)
- [Roles & Permissions](#roles--permissions)
- [Demo Accounts](#demo-accounts)
- [Features](#features)
- [Repo Layout](#repo-layout)
- [Database](#database)
- [Development Team](#development-team)
- [Contributing](#contributing)
- [Security Policy](#security-policy)
- [Changelog](#changelog)
- [Known Limitations](#known-limitations)
- [Roadmap](#roadmap)
- [License](#license)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8+ (with the `pdo_mysql` extension) |
| Database | MySQL / MariaDB |
| Frontend | HTML5, CSS3, vanilla JavaScript |
| Exports | jsPDF + jsPDF-AutoTable, ExcelJS (loaded from CDN on demand) |
| Version control | Git + GitHub |

---

## Quick Start

**Prerequisites:** PHP 8+, MySQL or MariaDB, and Git.

```bash
# 1. Clone the repository
git clone https://github.com/<your-username>/ibits-portal.git
cd ibits-portal

# 2. Configure your environment
cp .env.example .env
# edit .env with your MySQL credentials

# 3. Import the schema, then (optionally) the demo fixtures
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seeds.sql   # DESTRUCTIVE — truncates all tables first

# 4. Serve the app
php -S localhost:8000 -t public
```

Then open **http://localhost:8000** in your browser.

> Skip `seeds.sql` on any environment that holds real data.

---

## Environment Keys

`app/lib/db.php` reads these keys from `.env`:

| Key | Purpose |
|---|---|
| `DB_HOST` | MySQL host (e.g. `127.0.0.1`) |
| `DB_NAME` | Schema name (e.g. `ibits_portal`) |
| `DB_USER` | MySQL user |
| `DB_PASS` | MySQL password |
| `DB_CHARSET` | Connection charset (defaults to `utf8mb4`) |

> If your `.env` uses Laravel-style names (`DB_DATABASE`, `DB_USERNAME`,
> `DB_PASSWORD`), they will be **ignored** — rename them to match the table
> above.

Sample `.env.example`:

```dotenv
APP_ENV=local
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_NAME=ibits_portal
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

---

## Pages

| Page | Who | What |
|---|---|---|
| `public/index.php` | Public | Hero, Who We Are, Population, Mission & Vision, Events |
| `public/org-structure.php` | Public | Organizational chart + committee tables |
| `public/members.php` | Logged-in users | Member directory with Print / PDF / Excel export |
| `public/faqs.php` | Public | Frequently asked questions |
| `public/login.php` | Public | Secure Access (Member / Admin), then login form |
| `public/register.php` | Public | Member self-registration |
| `public/change-password.php` | Any signed-in user | Change password |
| `public/change-member-password.php` | Public | Member password reset (ID + current password) |
| `public/change-admin-password.php` | Public | Admin password reset (ID + position) |
| `member/index.php` | Members | Dashboard: fine counts + feature tiles |
| `member/helpdesk.php` | Members | Submit and follow concerns |
| `admin/index.php` | Officers | Dashboard (only features their position allows) |
| `admin/events.php` | `events` | GPOA calendar + cover photos |
| `admin/forms.php` | `forms` | Publish file or link forms |
| `admin/fines.php` | `fines` | Issue, edit, mark paid, delete; live member search |
| `admin/finance.php` | `finance` | Ledger with Print / PDF / Excel export |
| `admin/helpdesk.php` | `helpdesk` | Reply to tickets, update status |
| `admin/org.php` | `org` | Add officers, change positions, deactivate |
| `admin/cbl.php` | `cbl` | CBL amendment workflow |

---

## Roles & Permissions

Defined in `app/config.php`. Values are `view` (read-only) or `manage` (edit).

| Position | Features |
|---|---|
| **President** | All features (view) · Help Desk (manage) · CBL (manage) |
| **Vice President** | Same as President |
| **Secretary** | Events (manage) · Forms (manage) · Fines (manage) |
| **Treasurer** | Fines (manage) · Finances (manage) |
| **Auditor** | Fines (view) |
| **STRAW** | Help Desk (manage) |
| **Member** | Read-only access to portal features · own fines · own help desk tickets |

### CBL workflow

```
pending_president  →  pending_vp  →  published
        │                 │
        └───── rejected ──┘
```

Only the position that owns the current step can approve or reject.

### Export rules

- **Fines** export — Secretary and Treasurer only.
- **Finance** export — anyone who can open the page.
- **Members** directory export — any signed-in user.

---

## Demo Accounts

Password for all seeded accounts: **`ibits2026`**

> ⚠️ Change these before going live.

| Role | ID Number |
|---|---|
| Member | `2025-00001-LQ-0` |
| President | `2024-00534-LQ-0` |
| Vice President | `2025-00122-LQ-0` |
| Secretary | `2025-00121-LQ-0` |
| Treasurer | `2025-00127-LQ-0` |
| Auditor | `2025-00589-LQ-0` |
| STRAW | `2025-00138-LQ-0` |

---

## Features

### Help Desk
- Every signed-in user can submit concerns and follow their own replies.
- Only **President, Vice President, and STRAW** can reply to tickets or
  change status.
- Status flow: `open` → `in_progress` → `resolved`.

### Fines
- Officers issue fines by typing the member's **student ID number** into a
  live search — no dropdown.
- Members see only their own fines.
- Officers with `fines.manage` can add, edit, mark paid/unpaid, and delete.

### Forms
- Each form is either a **file upload** (PDF / DOC / DOCX / XLS / XLSX up to
  10 MB, capped by server limits) or an **online link** (`http` / `https`,
  ≤ 255 chars).
- Uploaded files are stored in `storage/form-files/` with randomized names.
- Missing attachment columns are auto-migrated on first visit.

### Events
- Month calendar with category filter tabs and a day-detail panel.
- Optional cover photos (JPG / PNG / GIF / WebP, ≤ 2 MB).
- Homepage event cards fall back to a generated banner if no image is set.

### Finance
- Ledger entries linked to events (budget / expense / inflow).
- Export to Print, PDF, or Excel with summary cards and signatories.

### Organization
- Public org chart (President → VP → Secretaries → Treasurers → Auditors).
- Committee breakdown with Chairperson / Vice Chair / Members.
- Officer accounts management: add, change position, deactivate.

### Security
- All POST forms are CSRF-protected (`csrf_check()` + `csrf_field()`).
- All output is escaped via `e()` (`htmlspecialchars` wrapper).
- Permission checks are enforced server-side (`require_feature`,
  `require_manage`, `can_manage`).
- Failed logins are rate-limited per session (`login_locked_for()`).
- Passwords are hashed with `password_hash(…, PASSWORD_DEFAULT)`.

---

## Repo Layout

```
app/
  bootstrap.php        bootstrap + env loader
  config.php           POSITIONS, FEATURES, CBL_CHAIN, ORG_EXECUTIVES, ORG_COMMITTEES
  lib/
    db.php             PDO connection
    auth.php           login, session, permission helpers
    helpers.php        url(), e(), asset(), flash()
    data.php           events / fines / tickets / population queries
    layout.php         html_head(), dash_start(), dash_end(), html_foot()
    export_kit.php     shared Print / PDF / Excel kit
public/                webroot (index.php, login.php, register.php, …)
member/                member-only pages
admin/                 officer-only pages
database/
  schema.sql           tables + constraints
  seeds.sql            demo fixtures (destructive)
storage/
  form-files/          uploaded attachments
```

---

## Database

Two SQL files, both meant to be run against a MySQL / MariaDB server.

**`schema.sql`** — creates the `ibits_portal` database and all tables:

- `users` — members and officers. Enforces `id_number` format and the rule
  that admins must have a `position`.
- `events` — GPOA entries, optional cover photo.
- `forms` — published forms (file uploads or online links).
- `fines` — issued fines.
- `finances` — ledger entries linked to events.
- `tickets` and `ticket_replies` — Help Desk.
- `cbl_documents` and `cbl_approvals` — CBL workflow history.

**`seeds.sql`** — ⚠️ **DESTRUCTIVE: TRUNCATES ALL TABLES BEFORE INSERTING.**
Seeds a full demo dataset: 7 officers, ~250 members, 40 events, ~40 fines,
18 finance records, 3 help desk tickets with replies. Never run against a
live environment.

Format rules enforced by the schema:

- `id_number` must match `^[0-9]{4}-[0-9]{5}-LQ-[0-9]$` (e.g. `2025-00001-LQ-0`).
- `account_type = 'admin'` requires a non-null `position`.
- All `updated_at` columns auto-update on row change.

---

## Development Team

| Developer | Role |
|---|---|
| **Johahnie Ynah Marise** | Developer |
| **Sheryn C. Hina** | Developer |
| **Kathleen V. Maceda** | Developer |
| **Jhalaine A. Agustin** | Developer |

---

## Contributing

Thanks for helping improve the iBITS Portal.

### Branching

Use short, topic-named branches off `main`, in the format
`<type>/<short-slug>`:

```
feat/light-theme
fix/fines-export-totals
docs/readme-rewrite
style/reindent-admin-pages
```

### Commit messages

Use conventional commit prefixes:

| Prefix | Meaning |
|---|---|
| `feat:` | New feature |
| `fix:` | Bug fix |
| `style:` | Formatting / whitespace only |
| `docs:` | Documentation |
| `refactor:` | Code change that isn't a fix or feature |
| `chore:` | Build / tooling / dependency update |

Keep the subject under 72 characters. Add a body when the change needs
context. Examples:

```
feat(cbl): add adviser step to approval chain
fix(db): align .env key names and use env() helper
style(admin): normalize indentation across admin pages
```

### Code Style

**PHP**
- PSR-12 where possible.
- One property per line in array literals when the array has more than three
  elements.
- Braces on their own line for top-level functions.
- Always use `declare(strict_types=1);` at the top of new PHP files.
- Escape all output with `e()`.
- Bind SQL parameters — never string-concatenate user input.

**HTML / Templates**
- Indent nested markup 4 spaces.
- Keep PHP open/close tags on the same line as the HTML they belong to.
- One `.field` / `.card` / `<tr>` block per logical line group.

**CSS**
- One rule per line, but properties may be packed on a single line if the
  rule is short.
- Comma-separated selectors on their own lines when there are more than two.
- Use the CSS custom properties from `:root` (`--navy`, `--gold`, `--bg`,
  etc.) rather than hard-coding colors.

**JavaScript**
- Vanilla JS only — no frameworks, no build step.
- 2-space indent.
- Always wrap loose scripts in an IIFE.
- Feature-detect before using new APIs.

### What to Test Before Submitting

There is no automated test suite yet. Manually verify:

1. **Login works** for each role (member, President, Secretary, Treasurer,
   Auditor, STRAW).
2. **Permission gates** — an officer without a feature sees a 403 on the
   corresponding admin page.
3. **Forms save** — post a form, reload, edit, delete.
4. **Fines** — issue, mark paid, mark unpaid, delete.
5. **Exports** — click Print, Download PDF, Download Excel on the pages that
   show them.
6. **Auth redirect** — opening an admin URL in a fresh tab without a session
   redirects to login.
7. **CSRF** — submitting a POST form without a valid token is rejected.

### Pull Requests

1. Branch off `main`.
2. Keep PRs small and focused on one change.
3. Include a short description of *why* the change is needed, not just *what*
   changed.
4. Reference related issues (`Fixes #12`).
5. If you touched a template, mention which pages you checked.

### Reporting Issues

When filing a bug, include:

- The role you were logged in as.
- The page URL.
- Steps to reproduce.
- Expected vs. actual behavior.
- Any console / server error output (redact credentials).

---

## Security Policy

### Reporting a Vulnerability

If you discover a security vulnerability in the iBITS Portal, please **do
not open a public GitHub issue**. Instead, contact the current iBITS officers
directly (see the organization's official Facebook page or the Help Desk).

Please include:

- A description of the vulnerability.
- Steps to reproduce it.
- The role you were authenticated as (if any).
- Any relevant URLs, payloads, or screenshots.

We will acknowledge receipt within 7 days and provide an estimated timeline
for a fix.

### Supported Versions

Only the latest `main` branch is actively supported. Older branches are not
maintained.

### Security Measures in Place

- **CSRF protection** — every POST form uses `csrf_check()` and includes
  `csrf_field()`.
- **Output escaping** — all template output is passed through `e()`, which
  wraps `htmlspecialchars($s, ENT_QUOTES, 'UTF-8')`.
- **SQL injection prevention** — all queries use PDO prepared statements with
  bound parameters.
- **Password hashing** — `password_hash(…, PASSWORD_DEFAULT)` (bcrypt).
- **Login rate limiting** — failed login attempts are tracked per session;
  exceeding the maximum locks further attempts for a cooldown period.
- **Server-side permission checks** — `require_feature()` and
  `require_manage()` run before any page renders; missing permission results
  in a 403 response.

### Out of Scope

- Denial of service from resource exhaustion (e.g. very large file uploads).
- Social engineering of officers or members.
- Issues in third-party CDN libraries (jsPDF, ExcelJS) — report those
  upstream.

---

## Changelog

All notable changes to the iBITS Portal are documented here.

Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and
this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

### [Unreleased]

Nothing yet — see the [Roadmap](#roadmap) for planned work.

### [1.0.0] — 2026-01-01

**Added**
- Public homepage: hero, Who We Are, Population counters, Mission & Vision,
  Events, footer.
- Public organizational chart and committee listing.
- Public FAQs.
- Member login and Admin login with position verification.
- Member self-registration with program → year-level linkage.
- Three password reset pages (any user, member, admin).
- Member dashboard with fine counts and feature tiles.
- Member Help Desk (submit + follow own concerns).
- Admin dashboard gated by position.
- Admin Events page (GPOA calendar + cover photo upload).
- Admin Forms page (file uploads + online links).
- Admin Fines page (issue, edit, mark paid, delete, live member search).
- Admin Finance ledger with Print / PDF / Excel export.
- Admin Help Desk (reply + status change).
- Admin Organization page (add officer, change position, deactivate).
- Admin CBL workflow (President → VP → published).
- Members directory with Print / PDF / Excel export.
- Shared export kit used by Finance and Fines pages.
- CSRF protection on all POST forms.
- Login rate-limiting.
- Dark theme stylesheet used across every page.

### [0.1.0] — 2025-11-01

**Added**
- Initial project skeleton (`app/`, `public/`, `member/`, `admin/`,
  `database/`, `storage/`).
- Database schema and demo seed.
- Bootstrap + environment loader.
- Base layout helpers (`html_head`, `html_foot`, `dash_start`, `dash_end`).
- Base auth helpers (session, login, permission checks).

---

## Known Limitations

- No SQLite fallback — MySQL / MariaDB only.
- No self-service profile editing (name, sex, birthday, profile picture).
- Dark theme only — no light-mode toggle wired up.
- CBL chain is President → VP only (no Adviser node).
- No email delivery for password resets — members and admins reset through
  `change-member-password.php` / `change-admin-password.php` by verifying
  their ID plus current password (members) or position (admins).
- Login rate limiting is tracked per session, not per account or IP.
- No automated test suite yet.

---

## Roadmap

- [ ] Light-theme stylesheet + toggle hookup
- [ ] Profile editing page (name, sex, birthday, avatar upload)
- [ ] Adviser role added to the CBL approval chain
- [ ] Fillable excuse-letter template with print-to-PDF
- [ ] Upcoming events + open tickets widgets on the member dashboard
- [ ] Versioned migrations to replace the `TRUNCATE`-first seed flow
- [ ] Audit log for officer actions (who changed which fine, who approved
      which CBL)
- [ ] CSV / API export endpoints for integration with university records

---

## License

Copyright (c) 2026 Institute of Brilliant Information Technology Students (PUPLC)

All rights reserved.

This software and its source code are the intellectual property of the
Institute of Brilliant Information Technology Students (iBITS), Polytechnic
University of the Philippines — Lopez, Quezon Campus.

Permission is granted to the organization's current and former officers,
advisers, and authorized members to view, use, and modify this software for
the internal, non-commercial purposes of the organization only.

Redistribution, sublicensing, or commercial use of this software, in whole or
in part, without prior written permission from the organization is prohibited.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES, OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

---

<p align="center">
  <strong>iBITS — We Code as One.</strong><br>
  Institute of Brilliant Information Technology Students · PUP Lopez, Quezon
</p>