<?php
include 'db.php'; // Include database connection file

// Check if an action is set (default to 'view')
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Initialize article variable
$article = null;

// Handle viewing an article and increment the view count
if ($action === 'view' && $articleId > 0) {
    // Increment the view count securely
    $conn->query("UPDATE articles SET view_count = view_count + 1 WHERE id = $articleId");

    // Fetch the article details
    $articleQuery = "SELECT * FROM articles WHERE id = $articleId";
    $article = $conn->query($articleQuery)->fetch_assoc();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Article Management</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
</head>
<body>
    <div class="container">
        <?php if ($action === 'view' && $article): ?>
            <h1><?= htmlspecialchars($article['title']); ?></h1>
            <?php if (!empty($article['photo_path'])): ?>
                <img src="uploads/<?= htmlspecialchars($article['photo_path']); ?>" 
                     alt="<?= htmlspecialchars($article['title']); ?>" 
                     style="max-width: 100%; height: auto;">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($article['content'])); ?></p>
            <span>Read count: <?= $article['view_count']; ?></span>
        <?php else: ?>
            <h1>No article found</h1>
        <?php endif; ?>
    </div>
</body>
</html>
