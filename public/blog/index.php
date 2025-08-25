<?php
require_once __DIR__ . '/../api/db.php';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare('SELECT slug, title, type, featured_image, created_at FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

$totalStmt = $pdo->query('SELECT COUNT(*) FROM posts');
$totalPosts = (int) $totalStmt->fetchColumn();
$totalPages = (int) ceil($totalPosts / $limit);

if (isset($_GET['ajax'])) {
    foreach ($posts as $post) {
        ?>
        <li>
          <?php if (!empty($post['featured_image'])): ?>
            <img loading="lazy" src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="" />
          <?php endif; ?>
          <a href="/blog/post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
            <?php echo htmlspecialchars($post['title']); ?>
          </a>
          <small><?php echo htmlspecialchars($post['type']); ?> | <?php echo htmlspecialchars($post['created_at']); ?></small>
        </li>
        <?php
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>GameNight Blogg</title>
<link rel="stylesheet" href="/styles/main.css" />
<?php include __DIR__ . '/../meta_tags.php'; ?>
<?php include __DIR__ . '/../seo_verification.php'; ?>
</head>
<body>
<h1>Blogg</h1>
<ul class="articles">
<?php foreach ($posts as $post): ?>
<li>
  <?php if (!empty($post['featured_image'])): ?>
    <img loading="lazy" src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="" />
  <?php endif; ?>
  <a href="/blog/post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
    <?php echo htmlspecialchars($post['title']); ?>
  </a>
  <small><?php echo htmlspecialchars($post['type']); ?> | <?php echo htmlspecialchars($post['created_at']); ?></small>
</li>
<?php endforeach; ?>
</ul>
<nav class="pagination">
  <button id="prev" <?php if ($page <= 1) echo 'disabled'; ?>>Previous</button>
  <span id="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
  <button id="next" <?php if ($page >= $totalPages) echo 'disabled'; ?>>Next</button>
</nav>
<script>
const totalPages = <?php echo $totalPages; ?>;
let currentPage = <?php echo $page; ?>;
const prevBtn = document.getElementById('prev');
const nextBtn = document.getElementById('next');

function loadPage(page) {
  fetch(`/blog/index.php?page=${page}&ajax=1`)
    .then(res => res.text())
    .then(html => {
      document.querySelector('.articles').innerHTML = html;
      currentPage = page;
      prevBtn.disabled = currentPage <= 1;
      nextBtn.disabled = currentPage >= totalPages;
      document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
    });
}

prevBtn.addEventListener('click', () => {
  if (currentPage > 1) {
    loadPage(currentPage - 1);
  }
});

nextBtn.addEventListener('click', () => {
  if (currentPage < totalPages) {
    loadPage(currentPage + 1);
  }
});
</script>
</body>
</html>
