<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Billet</title>
</head>
<body>
    <nav>
        <a href="index.php"><button>Accueil</button></a>
        
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
                echo '<a href="inscription.php"><button>Inscription</button></a>';

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

    <h1>Ajouter un Billet</h1>
    <form action="traiter_ajouter_billet.php" method="POST">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" required>
        <br>
        <label for="contenu">Contenu :</label>
        <textarea rows="25" cols="90" id="contenu" name="contenu" required></textarea>
        <br>
        <input type="submit" value="Ajouter le Billet">
    </form>

    <a href="javascript:history.back()"><button>Retour</button></a>
</body>
</html>