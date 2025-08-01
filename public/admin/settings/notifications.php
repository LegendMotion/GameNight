<?php
$requireRole = 'admin';
require_once '../layout.php';

$title = 'Settings - Notifications';
$page = 'settings';
$breadcrumbs = [
    ['label' => 'Innstillinger', 'url' => '/admin/settings/'],
    ['label' => 'Notifications']
];
$help = 'Notification preferences.';
admin_header(compact('title', 'page', 'breadcrumbs', 'help'));
?>
<h1>Notification Settings</h1>
<div id="settings-form"></div>
<script type="module" src="./notifications.js"></script>
<?php admin_footer(); ?>
