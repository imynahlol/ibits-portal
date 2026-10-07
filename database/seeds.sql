-- database/seeds.sql
-- Test data for iBITS Portal
-- Default password for all test accounts: password123

-- Students (format: 20XX-XXXXX-LQ-0)
INSERT INTO users (full_name, id_number, sex, program, year_level, password_hash, role) VALUES
('Juan Dela Cruz',  '2020-04567-LQ-0', 'Male',       'BSIT', '3rd Year', '$2y$10$REPLACE_WITH_REAL_HASH', 'member'),
('Maria Santos',    '2021-04671-LQ-0', 'Female',     'BSCS', '2nd Year', '$2y$10$REPLACE_WITH_REAL_HASH', 'president'),
('Alex Rivera',     '2018-00188-LQ-0', 'Non-Binary', 'BSIT', '4th Year', '$2y$10$REPLACE_WITH_REAL_HASH', 'treasurer'),
('Pedro Reyes',     '2019-00203-LQ-0', 'Male',       'BSIS', '1st Year', '$2y$10$REPLACE_WITH_REAL_HASH', 'member');

-- Advisers (format: XXXXX)
INSERT INTO users (full_name, id_number, sex, program, year_level, password_hash, role) VALUES
('Dr. Anna Cruz',     '00812', 'Female', NULL, NULL, '$2y$10$REPLACE_WITH_REAL_HASH', 'adviser'),
('Prof. Ben Lim',     '00457', 'Male',   NULL, NULL, '$2y$10$REPLACE_WITH_REAL_HASH', 'adviser');

-- FAQs
INSERT INTO faqs (question, answer, is_public) VALUES
('What is iBITS?', 'iBITS is the official IT organization with the motto "We Code as One".', 1),
('How do I join?', 'Register on the portal using your student number.', 1);