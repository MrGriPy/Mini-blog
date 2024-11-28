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

if (isset($_FILES['photo_profil'])) {
    $file = $_FILES['photo_profil'];
    $errors = [];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Une erreur est survenue lors du téléchargement.';
    }

    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = 'Le fichier doit être une image de type JPEG, PNG ou GIF.';
    }

    if ($file['size'] > $max_size) {
        $errors[] = 'La taille du fichier ne doit pas dépasser 2 Mo.';
    }

    if (empty($errors)) {
        $file_name = uniqid() . '-' . basename($file['name']);
        $upload_dir = 'uploads/';

        if (move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
            $stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = :photo_profil WHERE id = :id");
            $stmt->bindParam(':photo_profil', $file_name);
            $stmt->bindParam(':id', $_SESSION['user_id']);

            if ($stmt->execute()) {
                header('Location: espace_client.php?success=photo_upload');
                exit();
            } else {
                $errors[] = 'Erreur lors de la mise à jour de la photo de profil dans la base de données.';
            }
        } else {
            $errors[] = 'Erreur lors du déplacement du fichier.';
        }
    }
} else {
    $errors[] = 'Aucun fichier sélectionné.';
}

if (!empty($errors)) {
    header('Location: espace_client.php?error=' . urlencode(implode(', ', $errors)));
    exit();
}
?>