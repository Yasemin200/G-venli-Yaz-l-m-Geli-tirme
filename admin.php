<?php
session_start();
require_once 'db.php';

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

// Kullanıcı bilgilerini alın
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$username = $user['username'];
$role = $user['role'];

// Admin kontrolü
if ($role !== 'admin') {
    // Yetkisi olmayan kullanıcıyı ana sayfaya yönlendir
    header("Location: index.php");
    exit();
}

// Ürünleri veritabanından çek
$shoes_query = "SELECT id, name, description, price, image FROM shoes";
$shoes_result = $db->query($shoes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style_index.css">
    <link rel="stylesheet" href="navbar.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <ul class="nav-type">
                <li><a href="index.php" class="active">Ana Sayfa</a></li>
                <li><a href="favorilerim.php" class="active1">Favorilerim</a></li>
                <li><a href="sepetim.php" class="active2">Sepetim</a></li>
                <li>
                    <form class="search-form" action="search.php" method="get">
                        <input type="text" name="query" placeholder="Ayakkabı Ara">
                        <button type="submit"><i class="fa fa-search"></i></button>
                    </form>
                </li>
                <li class="nav-item dropdown ml-auto">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo htmlspecialchars($username); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="profile.php">Profilimi Düzenle</a>
                        <a class="dropdown-item" href="logout.php">Çıkış Yap</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <div class="container mt-5">
        <h1>Admin Paneli - Ürünler</h1>
        <?php while ($shoe = $shoes_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="shoe-card">
                <img src="<?php echo htmlspecialchars($shoe['image']); ?>" alt="Ayakkabı Resmi">
                <div>
                    <h3><a href="shoe.php?id=<?php echo $shoe['id']; ?>"><?php echo htmlspecialchars($shoe['name']); ?></a></h3>
                    <p><?php echo htmlspecialchars($shoe['description']); ?></p>
                    <p><strong>Fiyat:</strong> <?php echo htmlspecialchars($shoe['price']); ?> TL</p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="tr">
<head>
<link rel="stylesheet" href="navbar.css">

    <meta charset="UTF-8">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #3b4465;
            color: #fff;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            overflow-y: auto; /* Sayfanın dikey kaydırılabilir olması için */
        }
        .navbar {
            background-color: #343a40;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .container {
            margin-top: 100px;
            background-color: #44475a;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
            color: #fff;
            width: 80%;
            max-width: 1000px;
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
        .comment, .shoe, .user {
            background-color: #6272a4;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .comment p, .shoe p, .user p {
            margin: 0;
        }
        .comment form, .shoe form, .user form {
            display: inline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Ayakkabılarım</a></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <h2 class="text-center">Admin Paneli</h2>

        <h3>Ürün Ekle</h3>
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
                <input type="file" class="form-control-file" id="image" name="image">
            </div>
            <button type="submit" class="btn btn-primary">Ürün Ekle</button>
        </form>

        <h3>Ürünler</h3>
        <?php while ($shoe = $shoes_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="shoe">
                <p><strong><?php echo htmlspecialchars($shoe['name']); ?></strong> - <em><?php echo htmlspecialchars($shoe['price']); ?> TL</em></p>
                <p><?php echo htmlspecialchars($shoe['description']); ?></p>
                <form action="delete_product.php" method="post" style="display: inline;">
                    <input type="hidden" name="delete_shoe_id" value="<?php echo $shoe['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                </form>
                <form action="update_product.php" method="post" style="display: inline;">
                    <input type="hidden" name="update_shoe_id" value="<?php echo $shoe['id']; ?>">
                    <input type="text" name="name" value="<?php echo htmlspecialchars($shoe['name']); ?>" required>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($shoe['description']); ?>" required>
                    <input type="text" name="price" value="<?php echo htmlspecialchars($shoe['price']); ?>" required>
                    <button type="submit" class="btn btn-warning btn-sm">Güncelle</button>
                </form>
            </div>
        <?php endwhile; ?>

        <h3>Yorumlar</h3>
        <?php while ($comment = $comments_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="comment">
                <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong> - <em><?php echo htmlspecialchars($comment['shoe_name']); ?></em></p>
                <form action="delete_comment.php" method="post" style="display: inline;">
                    <input type="hidden" name="delete_comment_id" value="<?php echo $comment['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                </form>
            </div>
        <?php endwhile; ?>

        <h3>Kullanıcılar</h3>
        <?php while ($user = $users_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="user">
                <p><strong><?php echo htmlspecialchars($user['username']); ?></strong> - <em><?php echo htmlspecialchars($user['role']); ?></em></p>
                <form action="delete_user.php" method="post" style="display: inline;">
                    <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                </form>
                <form action="update_user.php" method="post" style="display: inline;">
                    <input type="hidden" name="update_user_id" value="<?php echo $user['id']; ?>">
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <select name="role">
                        <option value="user" <?php if ($user['role'] == 'user') echo 'selected'; ?>>Kullanıcı</option>
                        <option value="editor" <?php if ($user['role'] == 'editor') echo 'selected'; ?>>Editör</option>
                        <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                    </select>
                    <button type="submit" class="btn btn-warning btn-sm">Güncelle</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
