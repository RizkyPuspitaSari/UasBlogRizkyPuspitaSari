<?php
include 'db.php';

// Initialize variables
$id = $title = $content = $photoPath = $categoryId = '';
$action = 'Create'; // default action

// Handle form submission for Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $categoryId = $_POST['category_id'] ?? '';

    // Handle file upload for photo
    if (isset($_FILES['photo'])) {
        $photo = $_FILES['photo'];
        if ($photo['error'] == 0) {
            $photoPath = time() . '_' . basename($photo['name']);
            move_uploaded_file($photo['tmp_name'], 'uploads/' . $photoPath);
        }
    }

    if (empty($id)) {
        // Create new article
        $stmt = $conn->prepare("INSERT INTO articles (title, content, photo_path, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $content, $photoPath, $categoryId);
    } else {
        // Update existing article
        $stmt = $conn->prepare("UPDATE articles SET title=?, content=?, photo_path=?, category_id=? WHERE id=?");
        $stmt->bind_param("sssii", $title, $content, $photoPath, $categoryId, $id);
    }

    if ($stmt->execute()) {
        header('Location: article_crud.php'); // Redirect after successful operation
        exit;
    }
}

// Handle Delete action
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $deleteStmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $deleteStmt->bind_param("i", $deleteId);
    $deleteStmt->execute();
    header('Location: article_crud.php'); // Redirect after delete
    exit;
}

// Handle Edit action
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM articles WHERE id = $editId");
    $article = $result->fetch_assoc();
    
    // Fill form fields with existing article data
    if ($article) {
        $id = $article['id'];
        $title = $article['title'];
        $content = $article['content'];
        $photoPath = $article['photo_path'];
        $categoryId = $article['category_id'];
        $action = 'Update';
    }
}

// Fetch categories for the dropdown
$categoriesResult = $conn->query("SELECT * FROM categories");
$categories = $categoriesResult->fetch_all(MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Article CRUD</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"> <!-- Add Bootstrap CSS -->
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= $action ?> Article</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($title) ?>">
        </div>

        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" class="form-control" required><?= htmlspecialchars($content) ?></textarea>
        </div>

        <div class="form-group">
            <label for="photo">Photo:</label>
            <input type="file" id="photo" name="photo" class="form-control">
            <?php if ($photoPath): ?>
                <img src="uploads/<?= htmlspecialchars($photoPath) ?>" alt="Article Photo" style="margin-top: 10px;">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= ($category['id'] == $categoryId) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><?= $action ?> Article</button>
    </form>

    <h3 class="mt-4">Articles List</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Content</th>
                <th>Photo</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $articles = $conn->query("SELECT a.*, c.name AS category_name FROM articles a JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC");
            while ($row = $articles->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars(substr($row['content'], 0, 50)) ?>...</td>
                    <td><img src="uploads/<?= htmlspecialchars($row['photo_path']) ?>" alt="Article Photo"></td>
                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                    <td>
                        <a href="?edit_id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this article?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Include Bootstrap JS and jQuery -->
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
