<?php

return [
    /*
     |---------------------------------------------------------------
     | Public roles (allowed to register from website)
     |---------------------------------------------------------------
     | Only these roles will be allowed from public registration pages.
     */
    'public_roles' => [
        'student',
        'teacher',
        'client',
        'intern',
        'volunteer',
        'donor',
        'corporate',
    ],

    /*
     |---------------------------------------------------------------
     | Restricted roles (only SuperAdmin can create/assign)
     |---------------------------------------------------------------
     | All internal / admin roles that must be created/managed by SuperAdmin.
     | Use the canonical DB slugs (lowercase) that exist in your `roles` table.
     */
    'restricted_roles' => [
        'superadmin',
        'admin',
        'hr',
        'finance',
        'training',
        'exam',
        'usermgmt',
        'service',
        'consultant',
        'partner',
        'franchise',
        'affiliate',
    ],

    'default_public_role' => 'student',
];
