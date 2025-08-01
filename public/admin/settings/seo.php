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
<p>Verify your site ownership with search engines:</p>
<ol>
    <li>Add your domain in <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a>.</li>
    <li>Add your domain in <a href="https://www.bing.com/webmasters/" target="_blank" rel="noopener">Bing Webmaster Tools</a>.</li>
    <li>Select HTML tag verification and paste the provided tokens below.</li>
</ol>
<div id="settings-form"></div>
<script type="module" src="./seo.js"></script>
<?php admin_footer(); ?>
