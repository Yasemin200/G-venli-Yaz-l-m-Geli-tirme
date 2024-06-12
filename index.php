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

// Ürünleri veritabanından çek
$shoes_query = "SELECT id, name, description, price, image FROM shoes";
$shoes_result = $db->query($shoes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa</title>
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
        <h1>Ürünler</h1>
        <?php while ($shoe = $shoes_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="shoe-card">
                <img src="<?php echo htmlspecialchars($shoe['image']); ?>" alt="Ayakkabı Resmi">
                <div>
                    <h3><a href="shoe.php?id=<?php echo $shoe['id']; ?>"><?php echo htmlspecialchars($shoe['name']); ?></a></h3>
                    <p><?php echo htmlspecialchars($shoe['description']); ?></p>
                    <p><strong>Fiyat:</strong> <?php echo htmlspecialchars($shoe['price']); ?> TL</p>
                    <form action="sepete_ekle.php" method="post">
                        <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                        <button type="submit" class="btn btn-primary">Sepete Ekle</button>
                    </form>
                    <form action="favorilere_ekle.php" method="post">
                        <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                        <button type="submit" class="btn btn-secondary">Favorilere Ekle</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php
        // Reflected XSS zafiyeti
        if (isset($_GET['message'])) {
            echo "<script>alert('" . htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8') . "');</script>";
        }
    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
