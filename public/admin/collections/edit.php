<?php
require_once '../auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8" />
<title>Rediger utfordring</title>
<link rel="stylesheet" href="/styles/main.css" />
</head>
<body>
<h1>Rediger utfordring</h1>
<form method="post">
<textarea id="challengeText" name="challenge" placeholder="Utfordringstekst"></textarea>
<div id="placeholderButtons">
    <button type="button" data-placeholder="{{player}}">{{player}}</button>
</div>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>" />
<button type="submit">Lagre</button>
</form>
<pre id="preview" style="margin-top:1em;"></pre>
<script>
(function() {
  const textarea = document.getElementById('challengeText');
  const preview = document.getElementById('preview');
  document.querySelectorAll('#placeholderButtons button').forEach(btn => {
    btn.addEventListener('click', () => insertAtCursor(btn.dataset.placeholder));
  });
  textarea.addEventListener('input', updatePreview);

  function insertAtCursor(text) {
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const before = textarea.value.slice(0, start);
    const after = textarea.value.slice(end);
    textarea.value = before + text + after;
    const newPos = start + text.length;
    textarea.selectionStart = textarea.selectionEnd = newPos;
    textarea.focus();
    updatePreview();
  }

  function replacePlaceholders(text) {
    const players = JSON.parse(localStorage.getItem('players') || '[]');
    if (players.length === 0) return text;
    return text.replace(/{{player}}/gi, () => {
      const index = Math.floor(Math.random() * players.length);
      return players[index];
    });
  }

  function updatePreview() {
    if (!preview) return;
    preview.textContent = replacePlaceholders(textarea.value);
  }
})();
</script>
</body>
</html>
