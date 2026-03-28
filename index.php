<?php
require 'config.php';

// Delete logic (optional, we use a separate delete.php)
$stmt = $pdo->query("SELECT * FROM matrimonial_biodata ORDER BY id DESC");
$biodatas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Matrimonial Biodata - List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #6b5b2d;
            color: white;
        }
        .action-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 4px 8px;
            background: #6b5b2d;
            color: white;
            border-radius: 4px;
        }
        .action-buttons a.delete {
            background: #c0392b;
        }
        .add-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: #2c3e50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .photo-thumb {
            width: 50px;
            height: 60px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="biodata-container" style="width: 95%; max-width: 1200px;">
        <h1>Matrimonial Biodata List</h1>
        <a href="add.php" class="add-btn">+ Add New Biodata</a>

        <?php if (count($biodatas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Full Name</th>
                        <th>Birth Date</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($biodatas as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['id']) ?></td>
                        <td>
                            <?php if ($b['photo_path'] && file_exists($b['photo_path'])): ?>
                                <img src="<?= htmlspecialchars($b['photo_path']) ?>" class="photo-thumb">
                            <?php else: ?>
                                <img src="uploads/default.png" class="photo-thumb" alt="No photo">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($b['full_name']) ?></td>
                        <td><?= htmlspecialchars($b['birth_date']) ?></td>
                        <td><?= htmlspecialchars($b['contact']) ?></td>
                        <td class="action-buttons">
                            <a href="edit.php?id=<?= $b['id'] ?>">Edit</a>
                            <a href="delete.php?id=<?= $b['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this biodata?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No biodata found. <a href="add.php">Add your first biodata</a></p>
        <?php endif; ?>
    </div>
</body>
</html>