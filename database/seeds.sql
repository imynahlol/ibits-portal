-- Demo officer accounts use admin123 and must change it at first login. Member password remains ibits2026.
USE ibits_portal;
SET @pw = '$2y$12$2bWkB.8FydBaORJzl13nP.A0qQrViA985uMTHBXJIikvY465Ocqye';
SET @member_pw = '$2y$10$CfV2r0Ai7XNoVAAS0smmDO3yl3Br96iQVCjuQujTwoXUMeov8MqUW';

INSERT INTO users (full_name,id_number,sex,program,year_level,password_hash,account_type,position,must_change_password) VALUES
('Allea Grace C. Ranillo',          '2024-00534-LQ-0','Female',     'BSIT','3rd Year',@pw,'admin','President',1),
('Johahnie Ynah Marise C. Gilbuena','2025-00122-LQ-0','Female',     'BSIT','2nd Year',@pw,'admin','Vice President',1),
('EIyza Irene B. Ventura',          '2025-00121-LQ-0','Female',     'BSIT','2nd Year',@pw,'admin','Secretary',1),
('Irish Nicole J. Japone',          '2025-00127-LQ-0','Female',     'BSIT', '2nd Year',@pw,'admin','Treasurer',1),
('Harvey S. Pumariga',              '2025-00589-LQ-0','Male',       'DIT','2nd Year',@pw,'admin','Auditor',1),
('Angel Ivy D. Prada',              '2025-00138-LQ-0','Female',     'BSIT','2nd Year',@pw,'admin','STRAW',1),
('Kathleen V. Maceda',              '2025-00125-LQ-0','Female',     'BSIT','2nd Year',@pw,'admin','STRAW',1),

INSERT INTO events (title,description,category,start_datetime,end_datetime,location,is_public,created_by) VALUES
('IT Week 2026','Technology week with contests, seminars and exhibits.','Technology','2026-10-12 08:00','2026-10-16 17:00','PUP Lopez Campus',1,1),
('Hackathon: Build as One','24 hours of building together.','Competition','2026-12-05 08:00','2026-12-06 08:00','Computer Lab',1,1);