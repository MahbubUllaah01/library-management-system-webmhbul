<?php $page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($title ?? 'Library') ?> | Library Management System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="top">
  <div class="brand">Library Management System</div>
  <nav>
    <a href="index.php" class="<?= $page=='index.php'?'on':'' ?>">Dashboard</a>
    <a href="books.php" class="<?= $page=='books.php'?'on':'' ?>">Books</a>
    <a href="members.php" class="<?= $page=='members.php'?'on':'' ?>">Members</a>
    <a href="issue.php" class="<?= $page=='issue.php'?'on':'' ?>">Issue / Return</a>
  </nav>
</header>
<main>
<?php if ($msg): ?><p class="note ok"><?= h($msg) ?></p><?php endif; ?>
<?php if ($err): ?><p class="note bad"><?= h($err) ?></p><?php endif; ?>
