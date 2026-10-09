-- iBITS Portal - MySQL schema
CREATE DATABASE IF NOT EXISTS ibits_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ibits_portal;

-- Members and admins (officers / adviser).
--   account_type 'member' : can only use the Help Desk
--   account_type 'admin'  : has a position (see app/config.php) that decides which features they manage
-- id_number: students 20XX-XXXXX-LQ-0, advisers XXXXX
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    id_number VARCHAR(15) NOT NULL UNIQUE,
    sex ENUM('Female','Male','Non-Binary') NOT NULL,
    program VARCHAR(50) DEFAULT NULL,
    year_level VARCHAR(20) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    account_type ENUM('member','admin') NOT NULL DEFAULT 'member',
    position VARCHAR(40) DEFAULT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_id_number_format CHECK (
        id_number REGEXP '^[0-9]{4}-[0-9]{5}-LQ-[0-9]$' OR id_number REGEXP '^[0-9]{5}$'
    ),
    CONSTRAINT chk_admin_position CHECK (account_type = 'member' OR position IS NOT NULL)
);
CREATE INDEX idx_users_type ON users(account_type);

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    category VARCHAR(40) DEFAULT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME DEFAULT NULL,
    location VARCHAR(150),
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);
CREATE INDEX idx_events_start ON events(start_datetime);

CREATE TABLE forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    link VARCHAR(255) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE fines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid',
    due_date DATE NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);
CREATE INDEX idx_fines_status ON fines(status);

CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    category VARCHAR(60) NOT NULL DEFAULT 'General',
    description TEXT NOT NULL,
    status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX idx_tickets_status ON tickets(status);

CREATE TABLE ticket_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- CBL amendments: adviser -> president -> vice president -> published
CREATE TABLE cbl_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amendment_no VARCHAR(20) NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending_adviser','pending_president','pending_vp','published','rejected') NOT NULL DEFAULT 'pending_adviser',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE cbl_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cbl_id INT NOT NULL,
    position VARCHAR(40) NOT NULL,
    decided_by INT NOT NULL,
    decision ENUM('approved','rejected') NOT NULL,
    remarks TEXT,
    decided_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cbl_id) REFERENCES cbl_documents(id) ON DELETE CASCADE,
    FOREIGN KEY (decided_by) REFERENCES users(id)
);
