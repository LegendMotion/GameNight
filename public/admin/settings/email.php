<?php
$requireRole = 'admin';
require_once '../layout.php';

$title = 'Settings - Email';
$page = 'settings';
$breadcrumbs = [
    ['label' => 'Innstillinger', 'url' => '/admin/settings/'],
    ['label' => 'Email']
];
$help = 'Email server settings.';
admin_header(compact('title', 'page', 'breadcrumbs', 'help'));
?>
<h1>Email Settings</h1>
<div id="settings-form"></div>
<script type="module" src="./email.js"></script>
<?php admin_footer(); ?>
