<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Blog</title>
</head>
<body>
    <nav>
        <a href="index.php"><button>Accueil</button></a>
        <a href="inscription.php"><button>Inscription</button></a>
        
        <?php
        session_start();
        
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            if (isset($_SESSION['user_id'])) {
                echo '<a href="espace_client.php"><button>Espace Client</button></a>';

                if ((int)$_SESSION['user_id'] === 1) {
                    echo '<a href="ajouter_billet.php"><button>Ajouter un Billet</button></a>';
                    echo '<a href="admin.php"><button>Administration</button></a>';
                }
                echo '<a href="deconnexion.php"><button>Déconnexion</button></a>';
                echo '<span>Connecté en tant que : ' . htmlspecialchars($_SESSION['login']) . '</span>';
                $stmt_user = $pdo->prepare("SELECT photo_profil FROM utilisateurs WHERE id = :id");
                $stmt_user->bindParam(':id', $_SESSION['user_id']);
                $stmt_user->execute();
                $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

                if ($user && !empty($user['photo_profil'])) {
                    echo '<img src="uploads/' . htmlspecialchars($user['photo_profil']) . '" alt="Photo de Profil" style="width:100px;height:100px;border-radius:50%;">';
                }
            } else {
                echo '<a href="connexion.php"><button>Connexion</button></a>';
            }

            if (isset($_SESSION['message'])) {
                echo '<p>' . $_SESSION['message'] . '</p>';
                unset($_SESSION['message']);
            }

        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
        ?>
    </nav>
    <?php
$pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = :login");
    $stmt->bindParam(':login', $login);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Erreur : Un compte avec ce login existe déjà.<br>";
        echo '<a href="inscription.php"><button>Retour</button></a>';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (login, password) VALUES (:login, :password)");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            echo "Inscription réussie ! Vous pouvez vous connecter.";
            echo '<a href="connexion.php"><button>Connexion</button></a>';

        } else {
            echo "Erreur lors de l'inscription.";
        }
    }
}
?>
</body>
</html>