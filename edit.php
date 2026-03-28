<?php
require 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch existing data
$stmt = $pdo->prepare("SELECT * FROM matrimonial_biodata WHERE id = ?");
$stmt->execute([$id]);
$biodata = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$biodata) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name          = trim($_POST['fullName']);
    $birth_date         = $_POST['birthDate'];
    $birth_time         = $_POST['birthTime'];
    $birth_place        = trim($_POST['birthPlace']);
    $religion           = trim($_POST['religion']);
    $caste              = trim($_POST['caste']);
    $height             = trim($_POST['height']);
    $blood_group        = $_POST['bloodGroup'];
    $education          = trim($_POST['education']);
    $occupation         = trim($_POST['occupation']);
    $father_name        = trim($_POST['fatherName']);
    $father_occupation  = trim($_POST['fatherOccupation']);
    $mother_name        = trim($_POST['motherName']);
    $sisters            = trim($_POST['sisters']);
    $brothers           = trim($_POST['brothers']);
    $contact            = trim($_POST['contact']);
    $address            = trim($_POST['address']);

    $photo_path = $biodata['photo_path']; // keep old photo by default

    // Handle new photo upload
    if (isset($_FILES['photoUpload']) && $_FILES['photoUpload']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $ext = pathinfo($_FILES['photoUpload']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $error = "Only JPG, PNG, GIF files are allowed.";
        } else {
            $filename = uniqid() . '.' . $ext;
            $target = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['photoUpload']['tmp_name'], $target)) {
                // Delete old photo if exists
                if ($biodata['photo_path'] && file_exists($biodata['photo_path'])) {
                    unlink($biodata['photo_path']);
                }
                $photo_path = $target;
            } else {
                $error = "Failed to upload photo.";
            }
        }
    }

    if (empty($error)) {
        $sql = "UPDATE matrimonial_biodata SET
                full_name = ?, birth_date = ?, birth_time = ?, birth_place = ?, religion = ?, caste = ?,
                height = ?, blood_group = ?, education = ?, occupation = ?, father_name = ?,
                father_occupation = ?, mother_name = ?, sisters = ?, brothers = ?, contact = ?,
                address = ?, photo_path = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $full_name, $birth_date, $birth_time, $birth_place, $religion, $caste,
            $height, $blood_group, $education, $occupation, $father_name,
            $father_occupation, $mother_name, $sisters, $brothers, $contact,
            $address, $photo_path, $id
        ]);
        $success = "Biodata updated successfully!";
        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM matrimonial_biodata WHERE id = ?");
        $stmt->execute([$id]);
        $biodata = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Matrimonial Biodata</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="biodata-container">
        <h1>Edit Biodata</h1>
        <?php if ($error): ?>
            <div style="color: red; margin-bottom: 10px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="color: green; margin-bottom: 10px;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form id="biodataForm" method="POST" enctype="multipart/form-data" novalidate>
            <div class="top-section">
                <div class="form-left">
                    <table>
                        <tr><td class="label">Name</td><td><input id="fullName" name="fullName" type="text" value="<?= htmlspecialchars($biodata['full_name']) ?>" required></td></tr>
                        <tr><td class="label">Birthdate</td><td><input id="birthDate" name="birthDate" type="text" value="<?= htmlspecialchars($biodata['birth_date']) ?>" required></td></tr>
                        <tr><td class="label">Birth Time</td><td><input id="birthTime" name="birthTime" type="time" value="<?= htmlspecialchars($biodata['birth_time']) ?>" required></td></tr>
                        <tr><td class="label">Birth Place</td><td><input id="birthPlace" name="birthPlace" type="text" value="<?= htmlspecialchars($biodata['birth_place']) ?>" required></td></tr>
                        <tr><td class="label">Religion</td><td><input id="religion" name="religion" type="text" value="<?= htmlspecialchars($biodata['religion']) ?>" required></td></tr>
                        <tr><td class="label">Caste</td><td><input id="caste" name="caste" type="text" value="<?= htmlspecialchars($biodata['caste']) ?>" required></td></tr>
                        <tr><td class="label">Height</td><td><input id="height" name="height" type="text" value="<?= htmlspecialchars($biodata['height']) ?>" required></td></tr>
                        <tr>
                            <td class="label">Blood Group</td>
                            <td>
                                <select id="bloodGroup" name="bloodGroup" required>
                                    <option value="">Select blood group</option>
                                    <?php
                                    $groups = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];
                                    foreach ($groups as $bg):
                                        $selected = ($bg == $biodata['blood_group']) ? 'selected' : '';
                                        echo "<option value='$bg' $selected>$bg</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr><td class="label">Education</td><td><input id="education" name="education" type="text" value="<?= htmlspecialchars($biodata['education']) ?>" required></td></tr>
                        <tr><td class="label">Occupation</td><td><input id="occupation" name="occupation" type="text" value="<?= htmlspecialchars($biodata['occupation']) ?>" required></td></tr>
                    </table>
                </div>
                <div class="photo-box">
                    <img id="preview" src="<?= $biodata['photo_path'] ? htmlspecialchars($biodata['photo_path']) : 'uploads/default.png' ?>" alt="Photo Preview" class="photo-preview"><br><br>
                    <input id="photoUpload" name="photoUpload" type="file" accept="image/*">
                    <div class="photo-text">Upload new photo (leave empty to keep current)</div>
                </div>
            </div>

            <div class="section-title">FAMILY INFORMATION</div>
            <table>
                <tr><td class="label">Father Name</td><td><input id="fatherName" name="fatherName" type="text" value="<?= htmlspecialchars($biodata['father_name']) ?>" required></td></tr>
                <tr><td class="label">Occupation</td><td><input id="fatherOccupation" name="fatherOccupation" type="text" value="<?= htmlspecialchars($biodata['father_occupation']) ?>" required></td></tr>
                <tr><td class="label">Mother Name</td><td><input id="motherName" name="motherName" type="text" value="<?= htmlspecialchars($biodata['mother_name']) ?>" required></td></tr>
                <tr><td class="label">Sisters</td><td><input id="sisters" name="sisters" type="text" value="<?= htmlspecialchars($biodata['sisters']) ?>" required></td></tr>
                <tr><td class="label">Brothers</td><td><input id="brothers" name="brothers" type="text" value="<?= htmlspecialchars($biodata['brothers']) ?>" required></td></tr>
                <tr><td class="label">Contact No.</td><td><input id="contact" name="contact" type="tel" value="<?= htmlspecialchars($biodata['contact']) ?>" required pattern="^(?:\+?88)?01[3-9]\d{8}$"></td></tr>
                <tr><td class="label">Address</td><td><textarea id="address" name="address" rows="3" required><?= htmlspecialchars($biodata['address']) ?></textarea></td></tr>
            </table>

            <div class="form-actions">
                <button type="submit">Update Biodata</button>
                <a href="index.php" style="margin-left: 10px;">Cancel</a>
            </div>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>