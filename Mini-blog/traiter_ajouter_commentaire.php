<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = $_POST['contenu'] ?? null;
    $id_billet = $_POST['id_billet'] ?? null;

    if (empty($contenu) || empty($id_billet)) {
        echo "Erreur : Le commentaire et le billet doivent être spécifiés.";
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO commentaires (contenu, date_post, id_utilisateur, id_billet) VALUES (:contenu, NOW(), :id_utilisateur, :id_billet)");
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':id_utilisateur', $_SESSION['user_id']);
        $stmt->bindParam(':id_billet', $id_billet);

        if ($stmt->execute()) {
            echo "Commentaire ajouté avec succès !";
        } else {
            echo "Erreur lors de l'ajout du commentaire.";
        }
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout du commentaire : " . $e->getMessage();
    }
} else {
    echo "Erreur : Requête invalide.";
}

header('Location: billet.php?id=' . $id_billet . '&success=1');
exit();

?>