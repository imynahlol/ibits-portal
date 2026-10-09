# iBITS Portal

Multi-page PHP site. Every feature is its own page.

## Run
```bash
php -S localhost:8000 -t public     # then open http://localhost:8000
```
No MySQL? With `APP_ENV=local` it auto-creates a **demo** SQLite database (`storage/demo.sqlite`).
For MySQL: import `database/schema.sql` then `database/seeds.sql`, copy `.env.example` to `.env`.
Delete `storage/demo.sqlite` to reset the demo data.

## Pages
| Page | Who |
|---|---|
| `index.php` | public: header, Who We Are, Population, Mission & Vision, Events, footer |
| `login.php` | Secure Access (Member / Admin) then the log-in form. Admin form requires a **position** that must match the account |
| `register.php` | register as a member or self-register as an organization officer/admin |
| `member/index.php`, `member/helpdesk.php` | members: view and submit help desk concerns only |
| `admin/index.php` + `events`, `forms`, `fines`, `finance`, `helpdesk`, `org`, `cbl` | admins; only the pages their position allows |

## Positions to features (edit in `app/config.php`)
| Position | Features |
|---|---|
| President, Vice President | Events, Forms, Fines, Finances, Help Desk, Organization, CBL |
| Secretary | Events, Forms, Fines, Help Desk, CBL (can propose) |
| Treasurer | Fines and Finances |
| Assistant Treasurer | Fines |
| Auditor, Assistant Auditor | Fines (view only) |
| Institutional | Events |
| STRAW | Help Desk |

CBL amendments go to the President, then the Vice President.

## Demo accounts (password `ibits2026`; change before going live)
Member `2025-00001-LQ-0` · President `2023-00012-LQ-0` · VP `2024-00001-LQ-0` · Secretary `2024-00002-LQ-0` ·
Treasurer `2024-00003-LQ-0` · Auditor `2024-00004-LQ-0` · STRAW `2024-00005-LQ-0`

## Officer registration
Officers can create their own accounts through Register by selecting Organization Officer / Admin and their assigned position. No separate account creation by the President or Vice President is required. Because public self-registration allows a registrant to select a position, deploy this portal only where the registration process is trusted, or add an institutional verification step before production use.

## Role permissions

- **President / Vice President:** can view every portal feature. Editing is limited to Help Desk replies and CBL updates/proposals/approvals.
- **Secretary:** manages events, excuse-letter/forms, and fines.
- **Treasurer:** manages fines and finances.
- **STRAW:** manages Help Desk tickets and replies to member concerns.
- **Members:** can view the portal feature pages, view only their own fines, and submit/follow up on their own Help Desk concerns. All member feature pages are read-only.

Permissions are enforced server-side as well as reflected in the dashboard navigation.

## Recent Help Desk and fines updates

- All organization officer accounts can submit their own Help Desk concerns and review their own submitted concerns and replies. Only President, Vice President, and STRAW accounts can reply to tickets or update ticket statuses, according to the role permissions.
- When issuing or editing a fine, authorized officers manually enter the member's exact student ID number (for example, `2024-12345-LQ-0`). The server resolves this to an active member account and stores the fine against that account; no member dropdown is used.
- Treasurer accounts have Create, Read, Update, and Delete access to the finance ledger.
