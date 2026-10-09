<?php
declare(strict_types=1);

/** DEMO DATA for the local SQLite fallback only. Never used with MySQL. */
function demo_seed(PDO $pdo): void {
    $pw = password_hash('ibits2026', PASSWORD_DEFAULT);
    $ins = $pdo->prepare('INSERT INTO users (full_name,id_number,sex,program,year_level,password_hash,account_type,position) VALUES (?,?,?,?,?,?,?,?)');

    // Admin accounts: one per position
    $admins = [
        ['Ada Lovelace',                      '2023-00012-LQ-0', 'Female', 'BSIT', '4th Year', 'President'],
        ['Johahnie Gilbuena',                 '2024-00001-LQ-0', 'Female', 'BSIT', '3rd Year', 'Vice President'],
        ['Sam Secretary',                     '2024-00002-LQ-0', 'Male',   'BSIT', '3rd Year', 'Secretary'],
        ['Tina Treasurer',                    '2024-00003-LQ-0', 'Female', 'DIT',  '3rd Year', 'Treasurer'],
        ['Alex Auditor',                      '2024-00004-LQ-0', 'Male',   'BSIT', '2nd Year', 'Auditor'],
        ['Stella STRAW',                      '2024-00005-LQ-0', 'Female', 'BSIT', '2nd Year', 'STRAW'],
        ['Prof. Reyes (Adviser)',             '00812',           'Male',   null,   null,       'Adviser'],
    ];
    foreach ($admins as [$n, $id, $sex, $prog, $yr, $pos]) {
        $ins->execute([$n, $id, $sex, $prog, $yr, $pw, 'admin', $pos]);
    }

    // Demo member that can sign in
    $ins->execute(['Mia Member', '2025-00001-LQ-0', 'Female', 'BSIT', '1st Year', $pw, 'member', null]);

    // Filler members so the population section has something to count.
    $counts = ['BSIT' => [1 => 48, 2 => 50, 3 => 49, 4 => 48], 'DIT' => [1 => 44, 2 => 40, 3 => 32]];
    $suffix = ['1' => '1st Year', '2' => '2nd Year', '3' => '3rd Year', '4' => '4th Year'];
    $i = 100;
    $pdo->beginTransaction();
    foreach ($counts as $prog => $years) {
        foreach ($years as $y => $n) {
            for ($k = 0; $k < $n; $k++, $i++) {
                $ins->execute(["Demo Member $i", sprintf('2026-%05d-LQ-0', $i), 'Male', $prog, $suffix[$y], $pw, 'member', null]);
            }
        }
    }
    $pdo->commit();

    $ev = $pdo->prepare('INSERT INTO events (title,description,category,start_datetime,end_datetime,location,is_public,created_by) VALUES (?,?,?,?,?,?,?,1)');
    $evs = [
        ['Orientation 2026',     'Welcome program for new members.',                 'Academic',    '2026-08-12 08:00', '2026-08-12 12:00', 'PUP Lopez Gym'],
        ['Tech Talk Thursday',   'Guest talk on web careers.',                       'Technology',  '2026-10-08 13:00', '2026-10-08 15:00', 'Room 204'],
        ['Intramurals Code Cup', 'Coding contest running all week.',                 'Competition', '2026-10-05 09:00', '2026-10-10 17:00', 'Computer Lab'],
        ['IT Week 2026',         'Technology week with contests, seminars and exhibits.', 'Technology', '2026-10-12 08:00', '2026-10-16 17:00', 'PUP Lopez Campus'],
        ['Hackathon: Build as One', '24 hours of building together.',                'Competition', '2026-12-05 08:00', '2026-12-06 08:00', 'Computer Lab'],
    ];
    foreach ($evs as $e) $ev->execute([...$e, 1]);

    $pdo->prepare('INSERT INTO forms (title,description,link,created_by) VALUES (?,?,?,?)')
        ->execute(['Excuse Letter Template', 'Template for missing organization events or assemblies.', 'https://example.com/excuse-letter', 3]);

    $pdo->prepare('INSERT INTO cbl_documents (amendment_no,title,description,status,created_by) VALUES (?,?,?,?,?)')
        ->execute(['2026-04', 'Attendance Fines', 'Proposed change to Section 4.1 regarding unexcused absences during general assemblies.', 'pending_adviser', 3]);

    $pdo->prepare('INSERT INTO fines (user_id,amount,reason,due_date,created_by) VALUES (?,?,?,?,?)')
        ->execute([8, 50, 'Late to General Assembly', '2026-10-20', 4]);

    $t = $pdo->prepare('INSERT INTO tickets (user_id,subject,category,description,status) VALUES (?,?,?,?,?)');
    $t->execute([8, 'Cannot open the portal', 'Internet / Portal Issue', 'The page stays blank on mobile.', 'open']);
}
