<?php
require 'db.php'; $title = 'Dashboard';
$books = $pdo->query('SELECT COUNT(*) c FROM books')->fetch()['c'];
$members = $pdo->query('SELECT COUNT(*) c FROM members')->fetch()['c'];
$out = $pdo->query('SELECT COUNT(*) c FROM issues WHERE return_date IS NULL')->fetch()['c'];
$overdue = $pdo->query("SELECT i.id, b.title, m.name, i.due_date FROM issues i
  JOIN books b ON b.id=i.book_id JOIN members m ON m.id=i.member_id
  WHERE i.return_date IS NULL AND i.due_date < CURDATE() ORDER BY i.due_date")->fetchAll();
include 'header.php';
?>
<h1>Dashboard</h1>
<div class="stats">
  <div><b><?= $books ?></b> book titles</div>
  <div><b><?= $members ?></b> members</div>
  <div><b><?= $out ?></b> books currently issued</div>
  <div><b><?= count($overdue) ?></b> overdue</div>
</div>
<h2>Overdue books</h2>
<?php if (!$overdue): ?><p class="muted">No overdue books.</p><?php else: ?>
<div class="scroll"><table>
<tr><th>Book</th><th>Member</th><th>Due date</th></tr>
<?php foreach ($overdue as $o): ?>
<tr><td><?= h($o['title']) ?></td><td><?= h($o['name']) ?></td><td><?= h($o['due_date']) ?></td></tr>
<?php endforeach; ?></table></div>
<?php endif; include 'footer.php'; ?>
