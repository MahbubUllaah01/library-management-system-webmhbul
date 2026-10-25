<?php
require 'db.php'; $title = 'Issue / Return';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        if (($_POST['action'] ?? '') === 'issue') {
            $b = (int)$_POST['book_id']; $m = (int)$_POST['member_id']; $days = max(1, min(60, (int)$_POST['days']));
            $s = $pdo->prepare('SELECT available FROM books WHERE id=? FOR UPDATE'); $s->execute([$b]); $bk = $s->fetch();
            if (!$bk || $bk['available'] < 1) throw new Exception('No copies of this book are available.');
            $pdo->prepare('INSERT INTO issues (book_id,member_id,issue_date,due_date) VALUES (?,?,CURDATE(),DATE_ADD(CURDATE(), INTERVAL ? DAY))')->execute([$b,$m,$days]);
            $pdo->prepare('UPDATE books SET available = available - 1 WHERE id=?')->execute([$b]);
            $msg = 'Book issued.';
        } elseif (($_POST['action'] ?? '') === 'return') {
            $id = (int)$_POST['id'];
            $s = $pdo->prepare('SELECT book_id FROM issues WHERE id=? AND return_date IS NULL FOR UPDATE'); $s->execute([$id]); $is = $s->fetch();
            if (!$is) throw new Exception('This issue is already returned.');
            $pdo->prepare('UPDATE issues SET return_date=CURDATE() WHERE id=?')->execute([$id]);
            $pdo->prepare('UPDATE books SET available = available + 1 WHERE id=?')->execute([$is['book_id']]);
            $msg = 'Book returned.';
        }
        $pdo->commit();
    } catch (Exception $e) { if ($pdo->inTransaction()) $pdo->rollBack(); $err = $e instanceof PDOException ? 'Database error.' : $e->getMessage(); }
}
$books = $pdo->query('SELECT id,title,available FROM books WHERE available>0 ORDER BY title')->fetchAll();
$members = $pdo->query('SELECT id,name FROM members ORDER BY name')->fetchAll();
$active = $pdo->query("SELECT i.id,b.title,m.name,i.issue_date,i.due_date,(i.due_date < CURDATE()) AS late FROM issues i
  JOIN books b ON b.id=i.book_id JOIN members m ON m.id=i.member_id WHERE i.return_date IS NULL ORDER BY i.due_date")->fetchAll();
include 'header.php';
?>
<h1>Issue / Return</h1>
<h2>Issue a book</h2>
<form method="post" class="form" data-validate>
  <input type="hidden" name="action" value="issue">
  <label>Book<select name="book_id" required><option value="">Select a book</option>
    <?php foreach ($books as $b): ?><option value="<?= $b['id'] ?>"><?= h($b['title']) ?> (<?= $b['available'] ?> available)</option><?php endforeach; ?></select></label>
  <label>Member<select name="member_id" required><option value="">Select a member</option>
    <?php foreach ($members as $m): ?><option value="<?= $m['id'] ?>"><?= h($m['name']) ?></option><?php endforeach; ?></select></label>
  <label>Days<input type="number" name="days" min="1" max="60" value="14" required></label>
  <button>Issue book</button>
</form>
<h2>Currently issued</h2>
<div class="scroll"><table>
<tr><th>Book</th><th>Member</th><th>Issued</th><th>Due</th><th></th></tr>
<?php foreach ($active as $a): ?>
<tr class="<?= $a['late'] ? 'late' : '' ?>"><td><?= h($a['title']) ?></td><td><?= h($a['name']) ?></td><td><?= h($a['issue_date']) ?></td><td><?= h($a['due_date']) ?><?= $a['late'] ? ' (overdue)' : '' ?></td>
<td class="act"><form method="post"><input type="hidden" name="action" value="return"><input type="hidden" name="id" value="<?= $a['id'] ?>"><button class="link">Return</button></form></td></tr>
<?php endforeach; if (!$active): ?><tr><td colspan="5" class="muted">No books are currently issued.</td></tr><?php endif; ?>
</table></div>
<?php include 'footer.php'; ?>
