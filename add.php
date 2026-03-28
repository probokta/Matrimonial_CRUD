<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize inputs
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

    // Basic server-side validation
    if (empty($full_name) || empty($birth_date) || empty($contact)) {
        $error = "All fields are required.";
    } else {
        // Handle photo upload
        $photo_path = null;
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
                    $photo_path = $target;
                } else {
                    $error = "Failed to upload photo.";
                }
            }
        }

        if (empty($error)) {
            $sql = "INSERT INTO matrimonial_biodata 
                    (full_name, birth_date, birth_time, birth_place, religion, caste, height, blood_group,
                     education, occupation, father_name, father_occupation, mother_name, sisters, brothers,
                     contact, address, photo_path)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $full_name, $birth_date, $birth_time, $birth_place, $religion, $caste, $height, $blood_group,
                $education, $occupation, $father_name, $father_occupation, $mother_name, $sisters, $brothers,
                $contact, $address, $photo_path
            ]);
            $success = "Biodata added successfully!";
            // Optionally redirect to index
            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Matrimonial Biodata</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="biodata-container">
        <h1>Add New Biodata</h1>
        <?php if ($error): ?>
            <div style="color: red; margin-bottom: 10px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="color: green; margin-bottom: 10px;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form id="biodataForm" method="POST" enctype="multipart/form-data" novalidate>
            <!-- The exact same form structure as your original HTML -->
            <div class="top-section">
                <div class="form-left">
                    <table>
                        <tr><td class="label">Name</td><td><input id="fullName" name="fullName" type="text" placeholder="Mirza Anonno Probokta" required></td></tr>
                        <tr><td class="label">Birthdate</td><td><input id="birthDate" name="birthDate" type="text" placeholder="YYYY-MM-DD" required></td></tr>
                        <tr><td class="label">Birth Time</td><td><input id="birthTime" name="birthTime" type="time" required></td></tr>
                        <tr><td class="label">Birth Place</td><td><input id="birthPlace" name="birthPlace" type="text" placeholder="Pabna" required></td></tr>
                        <tr><td class="label">Religion</td><td><input id="religion" name="religion" type="text" placeholder="Islam" required></td></tr>
                        <tr><td class="label">Caste</td><td><input id="caste" name="caste" type="text" placeholder="Mirza" required></td></tr>
                        <tr><td class="label">Height</td><td><input id="height" name="height" type="text" placeholder="e.g. 5 ft 6 in" required></td></tr>
                        <tr>
                            <td class="label">Blood Group</td>
                            <td>
                                <select id="bloodGroup" name="bloodGroup" required>
                                    <option value="">Select blood group</option>
                                    <option value="A+">A+</option><option value="A-">A-</option>
                                    <option value="B+">B+</option><option value="B-">B-</option>
                                    <option value="O+">O+</option><option value="O-">O-</option>
                                    <option value="AB+">AB+</option><option value="AB-">AB-</option>
                                </select>
                            </td>
                        </tr>
                        <tr><td class="label">Education</td><td><input id="education" name="education" type="text" placeholder="BSC in CSE" required></td></tr>
                        <tr><td class="label">Occupation</td><td><input id="occupation" name="occupation" type="text" placeholder="Junior UI/UX Designer" required></td></tr>
                    </table>
                </div>
                <div class="photo-box">
                    <img id="preview" src="uploads/default.png" alt="Photo Preview" class="photo-preview"><br><br>
                    <input id="photoUpload" name="photoUpload" type="file" accept="image/*">
                    <div class="photo-text">Upload your photo</div>
                </div>
            </div>

            <div class="section-title">FAMILY INFORMATION</div>
            <table>
                <tr><td class="label">Father Name</td><td><input id="fatherName" name="fatherName" type="text" placeholder="Mirza MD Alamgir Hossain" required></td></tr>
                <tr><td class="label">Occupation</td><td><input id="fatherOccupation" name="fatherOccupation" type="text" placeholder="Businessman" required></td></tr>
                <tr><td class="label">Mother Name</td><td><input id="motherName" name="motherName" type="text" placeholder="Hira Sultana" required></td></tr>
                <tr><td class="label">Sisters</td><td><input id="sisters" name="sisters" type="text" placeholder="e.g. 1 (0 Married)" required></td></tr>
                <tr><td class="label">Brothers</td><td><input id="brothers" name="brothers" type="text" placeholder="e.g. 2 (1 Married)" required></td></tr>
                <tr><td class="label">Contact No.</td><td><input id="contact" name="contact" type="tel" placeholder="01900000000" required pattern="^(?:\+?88)?01[3-9]\d{8}$"></td></tr>
                <tr><td class="label">Address</td><td><textarea id="address" name="address" rows="3" placeholder="House 620, Gazipur Sadar, Gazipur" required></textarea></td></tr>
            </table>

            <div class="form-actions">
                <button type="submit">Save Biodata</button>
            </div>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>