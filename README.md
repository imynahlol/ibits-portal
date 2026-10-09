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
| `register.php` | join as a member |
| `member/index.php`, `member/helpdesk.php` | members: view and submit help desk concerns only |
| `admin/index.php` + `events`, `forms`, `fines`, `helpdesk`, `org`, `cbl` | admins; only the pages their position allows |

## Positions to features (edit in `app/config.php`)
| Position | Features |
|---|---|
| President, Vice President | Events, Forms, Fines, Help Desk, Organization, CBL |
| Secretary | Events, Forms, Help Desk, CBL (can propose) |
| Treasurer, Assistant Treasurer | Fines |
| Auditor, Assistant Auditor | Fines (view only) |
| Institutional | Events |
| STRAW | Help Desk |
| Adviser | CBL (first approver), Organization (view only) |

CBL amendments go Adviser, then President, then Vice President.

## Demo accounts (password `ibits2026`; change before going live)
Member `2025-00001-LQ-0` · President `2023-00012-LQ-0` · VP `2024-00001-LQ-0` · Secretary `2024-00002-LQ-0` ·
Treasurer `2024-00003-LQ-0` · Auditor `2024-00004-LQ-0` · STRAW `2024-00005-LQ-0` · Adviser `00812`
