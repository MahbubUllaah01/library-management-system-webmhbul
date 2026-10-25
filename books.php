<?php
require 'db.php'; $title = 'Books';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = $_POST['action'] ?? '';
    try {
        if ($a === 'add' || $a === 'update') {
            $t = trim($_POST['title']); $au = trim($_POST['author']); $isbn = trim($_POST['isbn']);
            $cat = trim($_POST['category']); $copies = max(1, (int)$_POST['copies']);
            if ($t === '' || $au === '' || $isbn === '' || $cat === '') throw new Exception('All fields are required.');
            if ($a === 'add') {
                $pdo->prepare('INSERT INTO books (title,author,isbn,category,copies,available) VALUES (?,?,?,?,?,?)')
                    ->execute([$t,$au,$isbn,$cat,$copies,$copies]);
                $msg = 'Book added.';
            } else {
                $id = (int)$_POST['id'];
                $issued = $pdo->prepare('SELECT COUNT(*) c FROM issues WHERE book_id=? AND return_date IS NULL');
                $issued->execute([$id]); $n = $issued->fetch()['c'];
                if ($copies < $n) throw new Exception("Copies cannot be fewer than the $n currently issued.");
                $pdo->prepare('UPDATE books SET title=?,author=?,isbn=?,category=?,copies=?,available=? WHERE id=?')
                    ->execute([$t,$au,$isbn,$cat,$copies,$copies-$n,$id]);
                $msg = 'Book updated.';
            }
        } elseif ($a === 'delete') {
            $id = (int)$_POST['id'];
            $c = $pdo->prepare('SELECT COUNT(*) c FROM issues WHERE book_id=?'); $c->execute([$id]);
            if ($c->fetch()['c'] > 0) throw new Exception('This book has issue records and cannot be deleted.');
            $pdo->prepare('DELETE FROM books WHERE id=?')->execute([$id]);
            $msg = 'Book deleted.';
        }
    } catch (PDOException $e) {
        $err = $e->getCode() == 23000 ? 'A book with this ISBN already exists.' : 'Database error.';
    } catch (Exception $e) { $err = $e->getMessage(); }
}
$edit = null;
if (isset($_GET['edit'])) { $s = $pdo->prepare('SELECT * FROM books WHERE id=?'); $s->execute([(int)$_GET['edit']]); $edit = $s->fetch(); }
$q = trim($_GET['q'] ?? '');
$s = $pdo->prepare('SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? OR category LIKE ? ORDER BY title');
$like = "%$q%"; $s->execute([$like,$like,$like,$like]); $rows = $s->fetchAll();
include 'header.php';
?>
<h1>Books</h1>
<form method="get" class="search"><input type="search" name="q" value="<?= h($q) ?>" placeholder="Search title, author, ISBN or category"><button>Search</button></form>
<div class="scroll"><table>
<tr><th>Title</th><th>Author</th><th>ISBN</th><th>Category</th><th>Available</th><th></th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= h($r['title']) ?></td><td><?= h($r['author']) ?></td><td><?= h($r['isbn']) ?></td><td><?= h($r['category']) ?></td>
<td><?= $r['available'] ?> / <?= $r['copies'] ?></td>
<td class="act"><a href="books.php?edit=<?= $r['id'] ?>">Edit</a>
<form method="post"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $r['id'] ?>">
<button class="link danger" data-confirm="Delete this book?">Delete</button></form></td></tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="6" class="muted">No books found.</td></tr><?php endif; ?>
</table></div>
<h2><?= $edit ? 'Edit book' : 'Add a book' ?></h2>
<form method="post" class="form" data-validate>
  <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
  <?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['id'] ?>"><?php endif; ?>
  <label>Title<input name="title" required value="<?= h($edit['title'] ?? '') ?>"></label>
  <label>Author<input name="author" required value="<?= h($edit['author'] ?? '') ?>"></label>
  <label>ISBN<input name="isbn" required pattern="[0-9Xx\-]{10,20}" title="10 to 20 digits" value="<?= h($edit['isbn'] ?? '') ?>"></label>
  <label>Category<input name="category" required value="<?= h($edit['category'] ?? '') ?>"></label>
  <label>Copies<input type="number" name="copies" min="1" required value="<?= h($edit['copies'] ?? 1) ?>"></label>
  <button><?= $edit ? 'Save changes' : 'Add book' ?></button>
  <?php if ($edit): ?><a href="books.php" class="cancel">Cancel</a><?php endif; ?>
</form>
<?php include 'footer.php'; ?>
