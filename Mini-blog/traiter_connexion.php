<?php
$pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login'] = $user['login'];
        echo "<h2>Connexion réussie !</h2>";
        echo "<p>Bienvenue, " . htmlspecialchars($user['login']) . " !</p>";
        echo '<a href="index.php"><button>Retour à l\'accueil</button></a>';
    } else {
        echo "Erreur : Identifiant ou mot de passe incorrect.";
        echo '<a href="connexion.php"><button>Retour</button></a>';
    }
}
?>