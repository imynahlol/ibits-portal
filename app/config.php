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
    'President'           => ['events' => 'manage', 'forms' => 'manage', 'fines' => 'manage', 'helpdesk' => 'manage', 'org' => 'manage', 'cbl' => 'manage'],
    'Vice President'      => ['events' => 'manage', 'forms' => 'manage', 'fines' => 'manage', 'helpdesk' => 'manage', 'org' => 'manage', 'cbl' => 'manage'],
    'Secretary'           => ['events' => 'manage', 'forms' => 'manage', 'helpdesk' => 'manage', 'cbl' => 'manage'],
    'Treasurer'           => ['fines' => 'manage'],
    'Assistant Treasurer' => ['fines' => 'manage'],
    'Auditor'             => ['fines' => 'view'],
    'Assistant Auditor'   => ['fines' => 'view'],
    'Institutional'       => ['events' => 'manage'],
    'STRAW'               => ['helpdesk' => 'manage'],
    'Adviser'             => ['cbl' => 'manage', 'org' => 'view'],
];

/** Admin features: key => [label, page, description] */
const FEATURES = [
    'events'   => ['Events',        'admin/events.php',   'Create and edit organization events.'],
    'forms'    => ['Forms',         'admin/forms.php',    'Publish forms and documents for members.'],
    'fines'    => ['Fines',         'admin/fines.php',    'Issue fines and record payments.'],
    'helpdesk' => ['Help Desk',     'admin/helpdesk.php', 'Answer concerns submitted by members.'],
    'org'      => ['Organization',  'admin/org.php',      'Manage officers and positions.'],
    'cbl'      => ['CBL Workflow',  'admin/cbl.php',      'Propose and approve Constitution and By-Laws amendments.'],
];

/** CBL approval chain: status => position that must approve it. */
const CBL_CHAIN = [
    'pending_adviser'   => 'Adviser',
    'pending_president' => 'President',
    'pending_vp'        => 'Vice President',
];
const CBL_NEXT = [
    'pending_adviser'   => 'pending_president',
    'pending_president' => 'pending_vp',
    'pending_vp'        => 'published',
];

const PROGRAMS = ['BSIT' => 4, 'DIT' => 3]; // program => number of year levels
