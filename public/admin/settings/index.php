<?php
$requireRole = 'admin';
require_once '../layout.php';

$title = 'Settings';
$page = 'settings';
$breadcrumbs = [['label' => 'Innstillinger']];
$help = 'Administrer applikasjonsinnstillinger.';
admin_header(compact('title','page','breadcrumbs','help'));
?>
<h1>Settings</h1>
<ul>
  <li><a href="general.php">General</a></li>
  <li><a href="email.php">Email</a></li>
  <li><a href="notifications.php">Notifications</a></li>
  <li><a href="integrations.php">Integrations</a></li>
  <li><a href="seo.php">SEO</a></li>
</ul>
<?php admin_footer(); ?>
