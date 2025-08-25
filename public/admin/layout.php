<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/i18n.php';

function user_can(array $roles): bool {
    return in_array($_SESSION['role'] ?? '', $roles, true);
}

function admin_header(array $options = []): void {
    $title = $options['title'] ?? t('admin');
    $page = $options['page'] ?? '';
    $breadcrumbs = $options['breadcrumbs'] ?? [];
    $help = $options['help'] ?? '';
    $role = $_SESSION['role'] ?? '';
    ?>
<!DOCTYPE html>
<html lang="<?php echo get_locale(); ?>">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
<link rel="stylesheet" href="/styles/admin.css" />
<script>(function(){const t=localStorage.getItem('theme')|| (window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');document.documentElement.setAttribute('data-theme',t);})();</script>
<?php include __DIR__ . '/../meta_tags.php'; ?>
</head>
<body>
<header class="admin-header">
  <div class="logo"><a href="/admin/articles/"><?php echo t('admin_title'); ?></a></div>
  <button id="theme-toggle" class="theme-toggle" aria-label="<?php echo t('toggle_theme'); ?>">🌓</button>
</header>
<div class="admin-wrapper">
<aside class="admin-sidebar">
  <ul>
    <?php if (user_can(['admin','editor'])): ?>
    <li<?php if ($page === 'articles') echo ' class="active"'; ?>><a href="/admin/articles/"><?php echo t('articles'); ?></a></li>
    <li<?php if ($page === 'collections') echo ' class="active"'; ?>><a href="/admin/collections/"><?php echo t('collections'); ?></a></li>
    <li<?php if ($page === 'games') echo ' class="active"'; ?>><a href="/admin/games/"><?php echo t('games'); ?></a></li>
    <?php endif; ?>
    <?php if (user_can(['admin'])): ?>
    <li<?php if ($page === 'users') echo ' class="active"'; ?>><a href="/admin/users/"><?php echo t('users'); ?></a></li>
    <li<?php if ($page === 'settings') echo ' class="active"'; ?>><a href="/admin/settings/"><?php echo t('settings'); ?></a></li>
    <li<?php if ($page === 'audit_logs') echo ' class="active"'; ?>><a href="/admin/audit_logs/"><?php echo t('audit_logs'); ?></a></li>
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
<script type="module" src="/admin/theme.js"></script>
</body>
</html>
<?php
}
