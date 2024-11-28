<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Blog</title>
    <style>
        .billets-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .billet {
            flex: 0 1 calc(33% - 10px);
            box-sizing: border-box;
            margin: 5px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: black;
        }

        .billet h2 {
            font-size: 1.2em;
            margin: 0;
        }

        .billet p {
            margin: 10px 0 0 0;
        }

        .billet:hover {
            background-color: #f0f0f0;
        }

        /* Styles pour le lien normal */
        .billet a {
            text-decoration: none;
            color: inherit;
        }
    </style>
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

    <h1>Bienvenue sur Mon Blog</h1>

    <div class="billets-container">
        <?php
        $stmt = $pdo->query("SELECT * FROM billets ORDER BY date_post DESC LIMIT 3");
        while ($billet = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $titre = htmlspecialchars($billet['titre']);
            $contenu = htmlspecialchars($billet['contenu']);
            $id_billet = $billet['id'];
            $extrait = strlen($contenu) > 50 ? substr($contenu, 0, 50) . '...' : $contenu;

            echo '<div class="billet">';
            echo '<a href="billet.php?id=' . $id_billet . '">';
            echo '<h2>' . $titre . '</h2>';
            echo '<p>' . $extrait . '</p>';
            echo '<p>Date de publication : ' . $billet['date_post'] . '</p>';
            echo '</a>';
            echo '</div>';
        }
        ?>
    </div>
    <br>
    <a href="archives.php"><button>Voir tous les billets (Archives)</button></a>
    
</body>
</html>