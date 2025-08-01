<?php
$requireRole = 'admin';
require_once '../layout.php';

$title = 'Settings - General';
$page = 'settings';
$breadcrumbs = [
    ['label' => 'Innstillinger', 'url' => '/admin/settings/'],
    ['label' => 'General']
];
$help = 'General site settings.';
admin_header(compact('title', 'page', 'breadcrumbs', 'help'));
?>
<h1>General Settings</h1>
<div id="settings-form"></div>
<script type="module" src="./general.js"></script>
<?php admin_footer(); ?>
