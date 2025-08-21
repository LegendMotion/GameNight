<?php
require_once __DIR__ . '/auth.php';

function user_can(array $roles): bool {
    return in_array($_SESSION['role'] ?? '', $roles, true);
}

function admin_header(array $options = []): void {
    $title = $options['title'] ?? 'Admin';
    $page = $options['page'] ?? '';
    $breadcrumbs = $options['breadcrumbs'] ?? [];
    $help = $options['help'] ?? '';
    $role = $_SESSION['role'] ?? '';
    ?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
<link rel="stylesheet" href="/styles/admin.css" />
</head>
<body>
<header class="admin-header">
  <div class="logo"><a href="/admin/articles/">GameNight Admin</a></div>
</header>
<div class="admin-wrapper">
<aside class="admin-sidebar">
  <ul>
    <?php if (user_can(['admin','editor'])): ?>
    <li<?php if ($page === 'articles') echo ' class="active"'; ?>><a href="/admin/articles/">Artikler</a></li>
    <li<?php if ($page === 'collections') echo ' class="active"'; ?>><a href="/admin/collections/">Samlinger</a></li>
    <li<?php if ($page === 'games') echo ' class="active"'; ?>><a href="/admin/games/">Spill</a></li>
    <?php endif; ?>
    <?php if (user_can(['admin'])): ?>
    <li<?php if ($page === 'users') echo ' class="active"'; ?>><a href="/admin/users/">Brukere</a></li>
    <li<?php if ($page === 'settings') echo ' class="active"'; ?>><a href="/admin/settings/">Innstillinger</a></li>
    <li<?php if ($page === 'audit_logs') echo ' class="active"'; ?>><a href="/admin/audit_logs/">Audit Logs</a></li>
    <?php endif; ?>
  </ul>
</aside>
<main class="admin-main">
<?php if (!empty($breadcrumbs)): ?>
<nav class="breadcrumbs">
  <?php foreach ($breadcrumbs as $i => $crumb): ?>
    <?php if ($i > 0): ?> / <?php endif; ?>
    <?php if (!empty($crumb['url']) && $i < count($breadcrumbs) - 1): ?>
      <a href="<?php echo $crumb['url']; ?>"><?php echo htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8'); ?></a>
    <?php else: ?>
      <span><?php echo htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8'); ?></span>
    <?php endif; ?>
  <?php endforeach; ?>
</nav>
<?php endif; ?>
<?php if ($help): ?>
<section class="help"><?php echo htmlspecialchars($help, ENT_QUOTES, 'UTF-8'); ?></section>
<?php endif; ?>
<?php
}

function admin_footer(): void {
    ?>
</main>
</div>
</body>
</html>
<?php
}
