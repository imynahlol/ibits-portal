<?php
declare(strict_types=1);

//Roles and permissions.
const POSITIONS = [
    // President and Vice President can open every feature, but may edit only
    // Help Desk replies and CBL updates/approvals.
    'President'           => ['events' => 'view', 'forms' => 'view', 'fines' => 'view', 'finance' => 'view', 'helpdesk' => 'manage', 'org' => 'view', 'cbl' => 'manage'],
    'Vice President'      => ['events' => 'view', 'forms' => 'view', 'fines' => 'view', 'finance' => 'view', 'helpdesk' => 'manage', 'org' => 'view', 'cbl' => 'manage'],
    // Forms represent excuse letters / documents.
    'Secretary'           => ['events' => 'manage', 'forms' => 'manage', 'fines' => 'manage'],
    'Treasurer'           => ['fines' => 'manage', 'finance' => 'manage'],
    'Auditor'             => ['fines' => 'view'],
    'STRAW'               => ['helpdesk' => 'manage'],
];

/** Members can view portal features; their Help Desk remains user-scoped. */
const MEMBER_FEATURES = [
    'events' => 'view', 'forms' => 'view', 'fines' => 'view',
    'finance' => 'view', 'helpdesk' => 'view', 'org' => 'view', 'cbl' => 'view',
];

/** Admin features: key => [label, page, description] */
const FEATURES = [
    'events'   => ['Events',        'admin/events.php',   'Create and edit organization events.'],
    'forms'    => ['Forms',         'admin/forms.php',    'Publish forms and documents for members.'],
    'fines'    => ['Fines',         'admin/fines.php',    'Issue fines and record payments.'],
    'finance'  => ['Finances',      'admin/finance.php',  'Track event budgets, expenses and income.'],
    'helpdesk' => ['Help Desk',     'admin/helpdesk.php', 'Answer concerns submitted by members.'],
    'org'      => ['Organization',  'admin/org.php',      'Manage officers and positions.'],
    'cbl'      => ['CBL Workflow',  'admin/cbl.php',      'Propose and approve Constitution and By-Laws amendments.'],
];

/** CBL approval chain: status => position that must approve it. */
const CBL_CHAIN = [
    'pending_president' => 'President',
    'pending_vp'        => 'Vice President',
];
const CBL_NEXT = [
    'pending_president' => 'pending_vp',
    'pending_vp'        => 'published',
];

const PROGRAMS = ['BSIT' => 4, 'DIT' => 3]; // program => number of year levels

/**
 * Organizational structure, Academic Year 2026-2027 (from the official List of Officers).
 * Entry format: [full name, position title, program - year level]
 */
const ORG_EXECUTIVES = [
    ['Allea Grace C. Ranillo',           'President',            'BSIT - 3'],
    ['Johahnie Ynah Marise C. Gilbuena', 'Vice President',       'BSIT - 2'],
    ['EIyza Irene B. Ventura',           'General Secretary',    'BSIT - 2'],
    ['Roxan Ivy M. Unlayao',             'Assistant Secretary',  'BSIT - 2'],
    ['Irish Nicole J. Japone',           'Treasurer',            'BSIT - 2'],
    ['Sheryn C. Hina',                   'Assistant Treasurer',  'BSIT - 2'],
    ['Harvey S. Pumariga',               'Auditor',              'DIT - 2'],
    ['Rhea Jhane D. Tumala',             'Assistant Auditor',    'BSIT - 2'],
];

/** Committees, listed alphabetically. Members inside each: Chairperson, Vice Chairperson, then Members. */
const ORG_COMMITTEES = [
    'Academic Committee' => [
        ['Rafaela Carabuena',       'Chairperson',      'DIT - 3'],
        ['Cedrex Q. Pelagio',       'Vice Chairperson', 'BSIT - 2'],
        ['Wella T. Retardo',        'Member',           'BSIT - 2'],
    ],
    'Cultural Committee' => [
        ['Gian Carlo M. Depilo',            'Chairperson',      'BSIT - 2'],
        ['Jesu Ian Kristoffer B. Aventurado', 'Vice Chairperson', 'BSIT - 2'],
    ],
    'Institutional Committee' => [
        ['Althea A. Dangalan',      'Chairperson',      'BSIT - 3'],
        ['Lugelle T. Zabella',      'Vice Chairperson', 'BSIT - 3'],
    ],
    'Networking Committee' => [
        ['Shaira Cybelle V. Bitoin',     'Chairperson',      'BSIT - 2'],
        ['Sean Siegfred P. Salabo',      'Vice Chairperson', 'BSIT - 4'],
        ['Liynkert Clarence B. Carillo', 'Member',           'BSIT - 2'],
        ['Justin Lloyd B. Mendez',       'Member',           'BSIT - 2'],
    ],
    'Publicity and Creative Media Committee' => [
        ['Jon Carlo Marasigan',     'Chairperson',      'BSIT - 3'],
        ['John Ariel C. Espinos',   'Vice Chairperson', 'DIT - 3'],
        ['Renier A. Acobado',       'Member',           'DIT - 3'],
        ['Philip Joshua O. Barba',  'Member',           'BSIT - 3'],
        ['Cyrine Jill S. Damasco',  'Member',           'BSIT - 2'],
    ],
    'Sports Committee' => [
        ['Jerome B. Sode',          'Chairperson',      'DIT - 2'],
    ],
    "STRAW (Student's Rights and Welfare) Committee" => [
        ['Angel Ivy D. Prada',      'Chairperson',      'BSIT - 2'],
        ['Kathleen V. Maceda',      'Vice Chairperson', 'BSIT - 2'],
    ],
];