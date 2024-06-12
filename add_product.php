<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Handle the product addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];

    // Move the uploaded file to the images directory
    move_uploaded_file($_FILES['image']['tmp_name'], "images/$image");

    // Command injection zafiyeti
    if (isset($_POST['filename'])) {
        $filename = $_POST['filename'];
        $command = "cat " . $filename;
        $output = shell_exec($command);
        echo "<pre>$output</pre>";
    }

    $stmt = $db->prepare("INSERT INTO shoes (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bindValue(1, $name, SQLITE3_TEXT);
    $stmt->bindValue(2, $description, SQLITE3_TEXT);
    $stmt->bindValue(3, $price, SQLITE3_TEXT);
    $stmt->bindValue(4, $image, SQLITE3_TEXT);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}
?>

<<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            margin-top: 100px;
            background-color: #44475a;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            color: #fff;
        }
        h2 {
            color: #50fa7b;
        }
        .btn-back {
            margin-bottom: 20px;
        }
        label {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Ayakkabılarım</a></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="index.php?action=logout" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <h2 class="text-center">Ürün Ekle</h2>
        <a href="admin.php" class="btn btn-secondary btn-back">Geri Dön</a>
        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">İsim</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Açıklama</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Fiyat</label>
                <input type="text" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="image">Resim</label>
                <input type="file" class="form-control-file" id="image" name="image" required>
            </div>
            <!-- Command injection zafiyeti için input alanı -->
            <div class="form-group">
                <label for="filename">Dosya Adı (Command Injection için)</label>
                <input type="text" class="form-control" id="filename" name="filename">
            </div>
            <button type="submit" class="btn btn-primary">Ürün Ekle</button>
        </form>
        <?php
        // Command injection zafiyetinin çıktısı
        if (isset($output)) {
            echo "<pre>$output</pre>";
        }
        ?>
    </div>
</body>
</html>
