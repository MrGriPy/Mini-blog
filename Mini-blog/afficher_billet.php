<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['id'])) {
    $id_billet = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM billets WHERE id = :id");
    $stmt->bindParam(':id', $id_billet);
    $stmt->execute();
    $billet = $stmt->fetch();

    if ($billet) {
        echo "<h2>" . htmlspecialchars($billet['titre']) . "</h2>";
        echo "<p>" . htmlspecialchars($billet['contenu']) . "</p>";
        echo "<small>Posté le " . $billet['date_post'] . " par l'utilisateur ID " . $billet['auteur_id'] . "</small>";

        if (isset($_SESSION['user_id'])) {
            echo '<form action="traiter_ajouter_commentaire.php" method="POST">';
            echo '<input type="hidden" name="id_billet" value="' . $id_billet . '">';
            echo '<textarea name="commentaire" rows="4" placeholder="Votre commentaire" required></textarea><br>';
            echo '<input type="submit" value="Ajouter le commentaire">';
            echo '</form>';
        }

        $stmt = $pdo->prepare("SELECT * FROM commentaires WHERE id_billet = :id_billet ORDER BY date_comment DESC");
        $stmt->bindParam(':id_billet', $id_billet);
        $stmt->execute();
        while ($commentaire = $stmt->fetch()) {
            echo "<div>";
            echo "<p>" . htmlspecialchars($commentaire['commentaire']) . "</p>";
            echo "<small>Posté par l'utilisateur ID " . $commentaire['auteur_id'] . " le " . $commentaire['date_comment'] . "</small>";
            echo "</div><hr>";
        }
    } 
}
?>