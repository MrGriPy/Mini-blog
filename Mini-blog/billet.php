<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du Billet</title>
    <style>
        .commentaires-container {
            margin-top: 20px;
        }
        .commentaire {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .comments {
            display: none;
        }
        .form-comment {
            display: none;
            margin-top: 20px;
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

    <?php
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id_billet = $_GET['id'];
            $stmt = $pdo->prepare("SELECT * FROM billets WHERE id = :id_billet");
            $stmt->bindParam(':id_billet', $id_billet);
            $stmt->execute();
            $billet = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($billet) {
                echo '<h1>' . htmlspecialchars($billet['titre']) . '</h1>';
                echo '<p>' . nl2br(htmlspecialchars($billet['contenu'])) . '</p>';
                echo '<p>Date de publication : ' . $billet['date_post'] . '</p>';
                echo '<button class="toggle-comments" onclick="toggleComments(' . $billet['id'] . ')">Voir les commentaires</button>';
                echo '<div id="comments-' . $billet['id'] . '" class="comments">';
                echo '<h2>Commentaires</h2>';

                $stmt_comments = $pdo->prepare("SELECT c.*, u.login FROM commentaires c JOIN utilisateurs u ON c.id_utilisateur = u.id WHERE c.id_billet = :id_billet ORDER BY c.date_post DESC");
                $stmt_comments->bindParam(':id_billet', $id_billet);
                $stmt_comments->execute();

                if ($stmt_comments->rowCount() > 0) {
                    while ($commentaire = $stmt_comments->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="commentaire">';
                        echo '<p>' . nl2br(htmlspecialchars($commentaire['contenu'])) . '</p>';
                        echo '<p>- <strong>' . htmlspecialchars($commentaire['login']) . '</strong> le ' . $commentaire['date_post'] . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Aucun commentaire pour ce billet.</p>';
                }
                echo '</div>';
                if (isset($_SESSION['user_id'])) {
                    echo '<div id="form-comment-' . $billet['id'] . '" class="form-comment">';
                    echo '<h3>Ajouter un commentaire</h3>';
                    echo '<form action="traiter_ajouter_commentaire.php" method="POST">';
                    echo '<input type="hidden" name="id_billet" value="' . $billet['id'] . '">';
                    echo '<textarea name="contenu" required placeholder="Votre commentaire..."></textarea>';
                    echo '<button type="submit">Ajouter un commentaire</button>';
                    echo '</form>';
                    echo '</div>';
                } else {
                    echo '<p>Vous devez être connecté pour ajouter un commentaire.</p>';
                }
            } else {
                echo '<p>Billet introuvable.</p>';
            }
        } else {
            echo '<p>Erreur : Aucun identifiant de billet fourni.</p>';
        }
    } catch (PDOException $e) {
        die('Erreur de connexion à la base de données : ' . $e->getMessage());
    }
    ?>
    <br><a href="index.php"><button>Retour</button></a>

    <?php
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo '<p>Votre commentaire a été ajouté avec succès !</p>';
    }
    ?>
    <script>
    function toggleComments(billetId) {
        var commentsDiv = document.getElementById("comments-" + billetId);
        var formCommentDiv = document.getElementById("form-comment-" + billetId);

        if (commentsDiv.style.display === "none" || commentsDiv.style.display === "") {
            commentsDiv.style.display = "block";
            formCommentDiv.style.display = "block";
        } else {
            commentsDiv.style.display = "none";
            formCommentDiv.style.display = "none";
        }
    }
    </script>
</body>
</html>