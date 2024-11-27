<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
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
    $titre = $_POST['titre'] ?? null;
    $contenu = $_POST['contenu'] ?? null;

    if (empty($titre) || empty($contenu)) {
        echo "Erreur : Le titre et le contenu ne peuvent pas être vides.";
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO billets (titre, contenu, id_utilisateur) VALUES (:titre, :contenu, :id_utilisateur)");
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':id_utilisateur', $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            echo "Billet ajouté avec succès !";
            echo '<a href="index.php">Retour à l\'accueil</a>';
        } else {
            echo "Erreur lors de l'ajout du billet.";
        }
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout du billet : " . $e->getMessage();
    }
} else {
    echo "Erreur : Requête invalide.";
}
?>