<?php
$requireRole = 'admin';
require_once '../layout.php';

$title = 'Settings - SEO';
$page = 'settings';
$breadcrumbs = [
    ['label' => 'Innstillinger', 'url' => '/admin/settings/'],
    ['label' => 'SEO']
];
$help = 'Search engine optimization settings.';
admin_header(compact('title', 'page', 'breadcrumbs', 'help'));
?>
<h1>SEO Settings</h1>
<div id="settings-form"></div>
<script type="module" src="./seo.js"></script>
<?php admin_footer(); ?>
