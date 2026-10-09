-- DEMO accounts for MySQL. Every password below is: ibits2026  (change before going live!)
USE ibits_portal;
SET @pw = '$2y$10$CfV2r0Ai7XNoVAAS0smmDO3yl3Br96iQVCjuQujTwoXUMeov8MqUW';

INSERT INTO users (full_name,id_number,sex,program,year_level,password_hash,account_type,position) VALUES
('Ada Lovelace',      '2023-00012-LQ-0','Female','BSIT','4th Year',@pw,'admin','President'),
('Johahnie Gilbuena', '2024-00001-LQ-0','Female','BSIT','3rd Year',@pw,'admin','Vice President'),
('Sam Secretary',     '2024-00002-LQ-0','Male',  'BSIT','3rd Year',@pw,'admin','Secretary'),
('Tina Treasurer',    '2024-00003-LQ-0','Female','DIT', '3rd Year',@pw,'admin','Treasurer'),
('Alex Auditor',      '2024-00004-LQ-0','Male',  'BSIT','2nd Year',@pw,'admin','Auditor'),
('Stella STRAW',      '2024-00005-LQ-0','Female','BSIT','2nd Year',@pw,'admin','STRAW'),
('Prof. Reyes',       '00812',          'Male',  NULL,  NULL,       @pw,'admin','Adviser'),
('Mia Member',        '2025-00001-LQ-0','Female','BSIT','1st Year',@pw,'member',NULL);

INSERT INTO events (title,description,category,start_datetime,end_datetime,location,is_public,created_by) VALUES
('IT Week 2026','Technology week with contests, seminars and exhibits.','Technology','2026-10-12 08:00','2026-10-16 17:00','PUP Lopez Campus',1,1),
('Hackathon: Build as One','24 hours of building together.','Competition','2026-12-05 08:00','2026-12-06 08:00','Computer Lab',1,1);
