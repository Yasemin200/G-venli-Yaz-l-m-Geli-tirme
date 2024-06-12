<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: giris_kayit.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT username, role FROM users WHERE id = ?");
$stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$username = $user['username'];
$role = $user['role'];

$query = $_GET['query'] ?? '';

// Güvensiz SQL sorgusu (SQL Injection zafiyeti içerir)
$shoes_query = "SELECT id, name, description, price, image FROM shoes WHERE name LIKE '%$query%' OR description LIKE '%$query%'";

$shoes_result = $db->query($shoes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Arama Sonuçları</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Ayakkabılar</a></div>
        <form class="search-form" action="search.php" method="get">
            <input type="text" name="query" placeholder="Ayakkabı Ara">
            <button type="submit">Ara</button>
        </form>
        <div class="user-info">
            <span><?php echo htmlspecialchars($username); ?></span>
            <a href="logout.php" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <h1>Arama Sonuçları</h1>
        <?php while ($shoe = $shoes_result->fetchArray(SQLITE3_ASSOC)): ?>
            <div class="shoe-card">
                <img src="<?php echo htmlspecialchars($shoe['image']); ?>" alt="Ayakkabı Resmi">
                <div>
                    <h3><a href="shoe.php?id=<?php echo $shoe['id']; ?>" style="color: #50fa7b;"><?php echo htmlspecialchars($shoe['name']); ?></a></h3>
                    <p><?php echo htmlspecialchars($shoe['description']); ?></p>
                    <p><strong>Fiyat:</strong> <?php echo htmlspecialchars($shoe['price']); ?> TL</p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
