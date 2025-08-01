<?php
$requireRole = 'admin';
require_once '../layout.php';

$title = 'Settings - Integrations';
$page = 'settings';
$breadcrumbs = [
    ['label' => 'Innstillinger', 'url' => '/admin/settings/'],
    ['label' => 'Integrations']
];
$help = 'Configure integrations.';
admin_header(compact('title', 'page', 'breadcrumbs', 'help'));
?>
<h1>Integration Settings</h1>
<div id="settings-form"></div>
<script type="module" src="./integrations.js"></script>
<?php admin_footer(); ?>
