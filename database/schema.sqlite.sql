-- SQLite twin of schema.sql. Used only by the local demo fallback.
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    id_number TEXT NOT NULL UNIQUE,
    sex TEXT NOT NULL,
    program TEXT, year_level TEXT,
    password_hash TEXT NOT NULL,
    account_type TEXT NOT NULL DEFAULT 'member',
    position TEXT,
    status TEXT NOT NULL DEFAULT 'active',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL, description TEXT, category TEXT,
    start_datetime TEXT NOT NULL, end_datetime TEXT, location TEXT,
    is_public INTEGER NOT NULL DEFAULT 1,
    created_by INTEGER NOT NULL REFERENCES users(id),
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE forms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL, description TEXT, link TEXT NOT NULL,
    created_by INTEGER NOT NULL REFERENCES users(id),
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE fines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    amount REAL NOT NULL, reason TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'unpaid', due_date TEXT NOT NULL,
    created_by INTEGER NOT NULL REFERENCES users(id),
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE tickets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    subject TEXT NOT NULL, category TEXT NOT NULL DEFAULT 'General',
    description TEXT NOT NULL, status TEXT NOT NULL DEFAULT 'open',
    created_at TEXT DEFAULT CURRENT_TIMESTAMP, updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE ticket_replies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ticket_id INTEGER NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id),
    message TEXT NOT NULL, created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE cbl_documents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    amendment_no TEXT NOT NULL, title TEXT NOT NULL, description TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending_adviser',
    created_by INTEGER NOT NULL REFERENCES users(id),
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE cbl_approvals (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cbl_id INTEGER NOT NULL REFERENCES cbl_documents(id) ON DELETE CASCADE,
    position TEXT NOT NULL, decided_by INTEGER NOT NULL REFERENCES users(id),
    decision TEXT NOT NULL, remarks TEXT, decided_at TEXT DEFAULT CURRENT_TIMESTAMP
);
