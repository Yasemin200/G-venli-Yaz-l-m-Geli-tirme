<?php
require_once 'db.php';
session_start();

// Ayakkabı ID'sini al
$shoe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($shoe_id > 0) {
    $stmt = $db->prepare("SELECT name, description, price, image FROM shoes WHERE id = ?");
    $stmt->bindValue(1, $shoe_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $shoe = $result->fetchArray(SQLITE3_ASSOC);
} else {
    echo "Geçersiz ayakkabı ID'si.";
    exit();
}

// Yorumları çek
$comments_stmt = $db->prepare("SELECT c.comment, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.shoe_id = ? ORDER BY c.created_at DESC");
$comments_stmt->bindValue(1, $shoe_id, SQLITE3_INTEGER);
$comments_result = $comments_stmt->execute();

// Yorum ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($user_id > 0 && !empty($comment)) {
        $insert_comment_stmt = $db->prepare("INSERT INTO comments (user_id, shoe_id, comment) VALUES (?, ?, ?)");
        $insert_comment_stmt->bindValue(1, $user_id, SQLITE3_INTEGER);
        $insert_comment_stmt->bindValue(2, $shoe_id, SQLITE3_INTEGER);
        $insert_comment_stmt->bindValue(3, $comment, SQLITE3_TEXT);
        $insert_comment_stmt->execute();
        header("Location: shoe.php?id=" . $shoe_id);
        exit();
    } else {
        echo "Yorum eklenemedi. Lütfen giriş yapın ve yorum alanını boş bırakmayın.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($shoe['name']); ?></title>
    <link rel="stylesheet" href="style_index.css">
</head>
<body>
    <div class="navbar">
        <div class="site-name"><a href="index.php">Ayakkabılar</a></div>
        <div class="user-info">
            <span><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
            <a href="index.php?action=logout" class="logout-button">Çıkış Yap</a>
        </div>
    </div>
    <div class="container">
        <div class="shoe-detail">
            <h1><?php echo htmlspecialchars($shoe['name']); ?></h1>
            <img src="images/<?php echo htmlspecialchars($shoe['image']); ?>" alt="Ayakkabı Resmi">
            <p><strong>Açıklama:</strong> <?php echo nl2br(htmlspecialchars($shoe['description'])); ?></p>
            <p><strong>Fiyat:</strong> <?php echo htmlspecialchars($shoe['price']); ?> TL</p>
        </div>

        <div class="comments-section">
            <h2>Yorumlar</h2>
            <?php while ($comment = $comments_result->fetchArray(SQLITE3_ASSOC)): ?>
                <div class="comment">
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
                </div>
            <?php endwhile; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="shoe.php?id=<?php echo $shoe_id; ?>" method="post">
                    <div class="form-group">
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Yorumunuzu yazın..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Yorumu Gönder</button>
                </form>
            <?php else: ?>
                <p>Yorum yapmak için <a href="giris_kayit.php">giriş yapın</a>.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
