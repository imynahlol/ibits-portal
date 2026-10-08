USE ibits_portal;

INSERT INTO users (full_name, student_id, program, role, password_hash) VALUES
('Johahnie Ynah Marise Capistrano Gilbuena','2024-0001-LQ-0','BSIT - 3rd Yr','Vice President',NULL),
('Ada Lovelace','2023-0012-LQ-0','BSIT - 4th Yr','President',NULL);

INSERT INTO fines (user_id, amount, reason, paid, fine_date) VALUES
(1, 50.00, 'Late to Gen Assembly', 0, '2026-10-05'),
(1, 100.00, 'Absent - IT Week Day 1', 0, '2026-09-20'),
(1, 20.00, 'No ID Lanyard', 1, '2026-08-15');

INSERT INTO events (title,start_date,end_date,start_time,end_time,location,category,description,is_public,is_highlight) VALUES
('Orientation 2024','2024-08-14','2024-08-14','08:00','12:00','PUP Lopez Gym','Academic','Welcome program for new members.',1,1),
('IT Week 2024','2024-10-14','2024-10-18','08:00','17:00','PUP Lopez Campus','Technology','Week of contests and talks.',1,1),
('IT Week 2025','2025-10-13','2025-10-17','08:00','17:00','PUP Lopez Campus','Technology','Seminars, tournaments and exhibits.',1,1),
('Foundation Day 2025','2025-11-20','2025-11-20','09:00','16:00','Covered Court','Social','Celebrating the org founding.',1,1),
('Orientation 2026','2026-08-12','2026-08-12','08:00','12:00','PUP Lopez Gym','Academic','Welcome program for new members.',1,1),
('Intramurals Code Cup','2026-10-05','2026-10-10','09:00','17:00','Computer Lab','Competition','Coding contest running all week.',1,0),
('Tech Talk Thursday','2026-10-08','2026-10-08','13:00','15:00','Room 204','Technology','Guest talk on web careers.',1,0),
('IT Week 2026','2026-10-12','2026-10-16','08:00','12:00','PUP Lopez Campus','Technology','Technology week with contests, seminars and exhibits.',1,1),
('General Assembly','2026-11-14','2026-11-14','09:00','12:00','Auditorium','Org Meeting','Members only: updates and elections.',0,1),
('Hackathon: Build as One','2026-12-05','2026-12-06','08:00','08:00','Computer Lab','Competition','24 hours of building together.',1,1),
('Orientation 2027','2027-08-11','2027-08-11','08:00','12:00','PUP Lopez Gym','Academic','Welcome program for new members.',1,1);

INSERT INTO tickets (owner_id,subject,category,status,description,created_at) VALUES
(1,'Cannot open the portal','Internet / Portal Issue','Open','The page stays blank on mobile.','2026-10-06 00:00:00'),
(2,'Wrong name on my profile','Account Concern','In Progress','Please fix my middle name.','2026-10-05 00:00:00'),
(1,'Need the excuse letter template','Form Request','Resolved','Where can I get it?','2026-09-28 00:00:00');

INSERT INTO cbl_amendments (amendment_no,title,description,status) VALUES
('2026-04','Attendance Fines','Proposed change to Section 4.1 regarding unexcused absences during general assemblies.','Pending');
