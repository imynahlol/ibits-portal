<?php
declare(strict_types=1);

/**
 * Roles and permissions.
 *
 * Every admin account has a POSITION. What an admin can open is decided only by
 * this map: feature => 'manage' (full access) or 'view' (read-only).
 * Edit this one table to change who can do what.
 */
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
