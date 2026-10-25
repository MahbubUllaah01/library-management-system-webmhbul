<?php
require 'db.php'; $title = 'Members';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (($_POST['action'] ?? '') === 'add') {
            $n = trim($_POST['name']); $e = trim($_POST['email']); $p = trim($_POST['phone']);
            if ($n === '' || !filter_var($e, FILTER_VALIDATE_EMAIL) || $p === '') throw new Exception('Enter a valid name, email and phone.');
            $pdo->prepare('INSERT INTO members (name,email,phone) VALUES (?,?,?)')->execute([$n,$e,$p]);
            $msg = 'Member added.';
        } elseif (($_POST['action'] ?? '') === 'delete') {
            $id = (int)$_POST['id'];
            $c = $pdo->prepare('SELECT COUNT(*) c FROM issues WHERE member_id=?'); $c->execute([$id]);
            if ($c->fetch()['c'] > 0) throw new Exception('This member has issue records and cannot be deleted.');
            $pdo->prepare('DELETE FROM members WHERE id=?')->execute([$id]);
            $msg = 'Member deleted.';
        }
    } catch (PDOException $e) {
        $err = $e->getCode() == 23000 ? 'A member with this email already exists.' : 'Database error.';
    } catch (Exception $e) { $err = $e->getMessage(); }
}
$rows = $pdo->query('SELECT * FROM members ORDER BY name')->fetchAll();
include 'header.php';
?>
<h1>Members</h1>
<div class="scroll"><table>
<tr><th>Name</th><th>Email</th><th>Phone</th><th></th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= h($r['name']) ?></td><td><?= h($r['email']) ?></td><td><?= h($r['phone']) ?></td>
<td class="act"><form method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
<button class="link danger" data-confirm="Delete this member?">Delete</button></form></td></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="4" class="muted">No members yet.</td></tr><?php endif; ?>
</table></div>
<h2>Add a member</h2>
<form method="post" class="form" data-validate>
  <input type="hidden" name="action" value="add">
  <label>Name<input name="name" required></label>
  <label>Email<input type="email" name="email" required></label>
  <label>Phone<input name="phone" required pattern="[0-9+\-\s]{7,20}" title="Digits only"></label>
  <button>Add member</button>
</form>
<?php include 'footer.php'; ?>
