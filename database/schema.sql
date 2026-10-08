-- iBITS Portal - Initial Database Schema

-- =====================================================
-- USERS DB TABLE
-- stores members, officers, advisers, and admins
-- id_number format:
--   Students : 20XX-XXXXX-LQ-0  (e.g. 2025-00122-LQ-0)
--   Advisers : XXXXX            (e.g. 00812)
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    id_number VARCHAR(20) NOT NULL UNIQUE,
    sex ENUM('Female','Male','Non-Binary') NOT NULL,
    program VARCHAR(50) DEFAULT NULL,        -- NULL for advisers
    year_level VARCHAR(20) DEFAULT NULL,     -- NULL for advisers
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'member',
    profile_picture VARCHAR(255) DEFAULT NULL,
    birthday DATE DEFAULT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT chk_id_number_format
        CHECK (
            id_number REGEXP '^[0-9]{4}-[0-9]{5}-LQ-[0-9]$'  -- student
            OR
            id_number REGEXP '^[0-9]{5}$'                     -- adviser
        )
);

CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_status ON users(status);

-- =====================================================
-- FINES TABLE
-- =====================================================
CREATE TABLE fines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('unpaid','paid') DEFAULT 'unpaid',
    due_date DATE NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE INDEX idx_fines_user ON fines(user_id);
CREATE INDEX idx_fines_status ON fines(status);

-- =====================================================
-- EVENTS TABLE
-- =====================================================
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    category ENUM('past','current','ongoing','future') NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME DEFAULT NULL,
    location VARCHAR(150),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE INDEX idx_events_category ON events(category);
CREATE INDEX idx_events_start ON events(start_datetime);

-- =====================================================
-- TICKETS TABLE (Help Desk / STRAW Support)
-- =====================================================
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open','in_progress','resolved') DEFAULT 'open',
    priority ENUM('low','medium','high') DEFAULT 'medium',
    assigned_to INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_tickets_status ON tickets(status);

-- =====================================================
-- TICKET REPLIES TABLE
-- =====================================================
CREATE TABLE ticket_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =====================================================
-- CBL DOCUMENTS TABLE
-- =====================================================
CREATE TABLE cbl_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    version VARCHAR(20) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM(
        'draft',
        'pending_adviser',
        'pending_president',
        'pending_vp',
        'published',
        'rejected'
    ) DEFAULT 'draft',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- =====================================================
-- CBL APPROVALS TABLE
-- Adviser -> President -> VP approval chain
-- =====================================================
CREATE TABLE cbl_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cbl_id INT NOT NULL,
    role VARCHAR(30) NOT NULL,
    approved_by INT DEFAULT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    remarks TEXT,
    approved_at TIMESTAMP NULL,
    FOREIGN KEY (cbl_id) REFERENCES cbl_documents(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =====================================================
-- FINANCES TABLE
-- =====================================================
CREATE TABLE finances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_ DATE NOT NULL,
    event_ TEXT NOT NULL,                -- Holds additional text notes/descriptions
    event_id INT NOT NULL,               -- Foreign key pointing to your master events table
    budget DECIMAL(10,2) NOT NULL,
    expense DECIMAL(10,2) NOT NULL,
    inflow DECIMAL(10,2) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Corrected target table name to match your schema ('events'):
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE faq_categories (
    id SERIAL PRIMARY KEY, -- Evaluates to BIGINT UNSIGNED
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    display_order INT DEFAULT 0
);

-- 2. FAQs Table
CREATE TABLE faqs (
    id SERIAL PRIMARY KEY,
    -- Fixed Type Mismatch: Upgraded to BIGINT UNSIGNED to match faq_categories(id)
    category_id BIGINT UNSIGNED, 
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    is_published BOOLEAN DEFAULT true,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Explicitly declared constraint name for cleaner error tracking
    CONSTRAINT fk_faq_category 
        FOREIGN KEY (category_id) 
        REFERENCES faq_categories(id) 
        ON DELETE SET NULL
);

-- Performance Index to accelerate category filtration lookups on the frontend
CREATE INDEX idx_faqs_category ON faqs(category_id);