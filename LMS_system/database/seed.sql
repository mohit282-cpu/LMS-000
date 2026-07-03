INSERT IGNORE INTO organizations (id, name, code, email, status)
VALUES (1, 'Default Institution', 'DEFAULT', NULL, 'active');

INSERT IGNORE INTO permissions (name, description) VALUES
('dashboard.view', 'View dashboard'),
('users.view', 'View users'),
('users.create', 'Create users'),
('users.update', 'Update users'),
('users.delete', 'Deactivate users'),
('roles.view', 'View roles'),
('roles.manage', 'Manage roles and permissions'),
('students.view', 'View students'),
('students.create', 'Create students'),
('students.update', 'Update students'),
('students.delete', 'Deactivate students'),
('teachers.view', 'View teachers'),
('teachers.create', 'Create teachers'),
('teachers.update', 'Update teachers'),
('teachers.delete', 'Deactivate teachers'),
('courses.view', 'View courses'),
('courses.create', 'Create courses'),
('courses.update', 'Update courses'),
('courses.delete', 'Archive courses'),
('attendance.manage', 'Manage attendance'),
('timetable.manage', 'Manage timetable'),
('assignments.manage', 'Manage assignments'),
('exams.manage', 'Manage exams and results'),
('fees.manage', 'Manage fees'),
('reports.view', 'View reports'),
('settings.manage', 'Manage settings'),
('audit.view', 'View audit logs');

INSERT IGNORE INTO roles (name, slug, description, is_system) VALUES
('Super Administrator', 'super_admin', 'Full platform access', 1),
('Administrator', 'admin', 'Institution administrator', 1),
('Teacher', 'teacher', 'Teaching staff access', 1),
('Student', 'student', 'Student portal access', 1),
('Parent', 'parent', 'Parent portal access', 1),
('Accountant', 'accountant', 'Finance and fee access', 1),
('Librarian', 'librarian', 'Library access', 1);

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT roles.id, permissions.id
FROM roles
CROSS JOIN permissions
WHERE roles.slug IN ('super_admin', 'admin');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT roles.id, permissions.id
FROM roles
JOIN permissions ON permissions.name IN (
    'dashboard.view',
    'students.view',
    'courses.view',
    'attendance.manage',
    'assignments.manage',
    'exams.manage',
    'reports.view'
)
WHERE roles.slug = 'teacher';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT roles.id, permissions.id
FROM roles
JOIN permissions ON permissions.name IN ('dashboard.view', 'courses.view')
WHERE roles.slug = 'student';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT roles.id, permissions.id
FROM roles
JOIN permissions ON permissions.name IN ('dashboard.view', 'students.view', 'fees.manage', 'reports.view')
WHERE roles.slug = 'parent';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT roles.id, permissions.id
FROM roles
JOIN permissions ON permissions.name IN ('dashboard.view', 'students.view', 'fees.manage', 'reports.view')
WHERE roles.slug = 'accountant';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT roles.id, permissions.id
FROM roles
JOIN permissions ON permissions.name IN ('dashboard.view', 'students.view', 'reports.view')
WHERE roles.slug = 'librarian';

