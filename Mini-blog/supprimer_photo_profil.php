<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if (isset($_POST['supprimer_photo'])) {
    $stmt_user = $pdo->prepare("SELECT photo_profil FROM utilisateurs WHERE id = :id");
    $stmt_user->bindParam(':id', $_SESSION['user_id']);
    $stmt_user->execute();
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if ($user['photo_profil']) {
        $photo_path = 'uploads/' . $user['photo_profil'];

        if (file_exists($photo_path)) {
            unlink($photo_path);
        }

        $stmt_update = $pdo->prepare("UPDATE utilisateurs SET photo_profil = NULL WHERE id = :id");
        $stmt_update->bindParam(':id', $_SESSION['user_id']);
        $stmt_update->execute();
        $_SESSION['photo_supprimee'] = true;
        header('Location: espace_client.php');
        exit();
    }
}
?>