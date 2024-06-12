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

// Sepete eklenen ürünleri çek
$cart_query = "SELECT s.id, s.name, s.description, s.price, i.filepath AS image_path, c.quantity 
               FROM carts c
               JOIN shoes s ON c.shoe_id = s.id
               LEFT JOIN images i ON s.image_id = i.id
               WHERE c.user_id = ?";
$stmt = $db->prepare($cart_query);
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<link rel="stylesheet" href="navbar.css">

    <meta charset="UTF-8">
    <title>Sepetim</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style_index.css">
</head>
<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">Ayakkabılarım</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="favorilerim.php">Favorilerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sepetim.php">Sepetim</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Profil
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="profil.php">Profili Düzenle</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="giris_kayit.php?action=logout">Çıkış Yap</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <h2>Sepetim</h2>
        <div class="row">
            <?php while ($shoe = $result->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="<?php echo htmlspecialchars($shoe['image_path']); ?>" class="card-img-top" alt="Ayakkabı Resmi">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($shoe['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($shoe['description']); ?></p>
                            <p class="card-text"><strong>Fiyat:</strong> <?php echo htmlspecialchars($shoe['price']); ?> TL</p>
                            <form action="sepetten_cikar.php" method="post">
                                <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                                <button type="submit" class="btn btn-danger">Sepetten Çıkar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrap.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
