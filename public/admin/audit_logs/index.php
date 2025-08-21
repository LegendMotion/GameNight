<?php
require_once '../layout.php';
require_once __DIR__ . '/../../api/db.php';

$title = 'Audit Logs';
$page = 'audit_logs';
$breadcrumbs = [ ['label' => 'Audit Logs'] ];
admin_header(compact('title','page','breadcrumbs'));

$user = $_GET['user'] ?? '';
$action = $_GET['action'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$sql = 'SELECT a.*, u.email FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id WHERE 1=1';
$params = [];
if ($user !== '') { $sql .= ' AND u.email LIKE ?'; $params[] = "%$user%"; }
if ($action !== '') { $sql .= ' AND a.action = ?'; $params[] = $action; }
if ($from !== '') { $sql .= ' AND a.created_at >= ?'; $params[] = $from; }
if ($to !== '') { $sql .= ' AND a.created_at <= ?'; $params[] = $to; }
$sql .= ' ORDER BY a.created_at DESC LIMIT 100';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>
<h1>Audit Logs</h1>
<form method="get" style="margin-bottom:1em;">
  <input type="text" name="user" placeholder="User" value="<?php echo htmlspecialchars($user, ENT_QUOTES, 'UTF-8'); ?>" />
  <input type="text" name="action" placeholder="Action" value="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>" />
  <input type="date" name="from" value="<?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?>" />
  <input type="date" name="to" value="<?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?>" />
  <button type="submit">Filter</button>
</form>
<table>
  <tr><th>Time</th><th>User</th><th>Action</th><th>Target</th><th>Metadata</th></tr>
  <?php foreach ($logs as $log): ?>
  <tr>
    <td><?php echo htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><?php echo htmlspecialchars($log['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
    <td><?php echo htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><?php echo htmlspecialchars($log['target'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><pre style="white-space:pre-wrap;word-break:break-word;"><?php echo htmlspecialchars($log['metadata'], ENT_QUOTES, 'UTF-8'); ?></pre></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php admin_footer(); ?>
