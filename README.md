# iBITS Portal

A structured PHP/HTML split of the original iBITS Portal single-page prototype.

## Structure

- `app/` — application logic, controllers, models, helpers, and views
- `config/` — database connection
- `database/` — MySQL schema and seed data
- `public/` — web root, assets, uploads, and entry point
- `storage/logs/` — application logs
- `.env` — local configuration (do not commit)
- `.env.example` — configuration template

## Run locally

1. Create a MySQL database and import:
   - `database/schema.sql`
   - `database/seeds.sql`
2. Copy `.env.example` to `.env` and set the database credentials.
3. Point Apache/Nginx to the `public/` directory, or run:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Open `http://localhost:8000`.

## Important

The original frontend behavior is preserved as a JavaScript prototype. Login, registration, event editing, fines, tickets, and population data still use the mock client-side data in `public/assets/js/app.js`.

The PHP models/controllers and database schema are separated and ready for replacing the mock data with real CRUD/API operations. No real authentication is claimed until those endpoints are wired to the frontend.

## Notes

- The original embedded iBITS logo was extracted into `public/assets/images/ibits-logo.png`.
- Uploaded event images are intended for `public/uploads/`.
- Keep `.env` out of version control.
