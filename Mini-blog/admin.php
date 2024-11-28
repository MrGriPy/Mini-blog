<?php
session_start();

if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_id'] !== 1) {
    header('Location: index.php');
    exit();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=tvidal_mini_blog;charset=utf8', 'tvidal', 'dbmotdepasse2024');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['modifier_utilisateur'])) {
            $login = trim($_POST['login']);
            $motdepasse = trim($_POST['motdepasse']);
            $id_utilisateur = $_POST['id_utilisateur'];

            if (empty($login)) {
                $_SESSION['message'] = "Le login ne peut pas être vide.";
            } else {
                $query = "UPDATE utilisateurs SET login = :login";
                if (!empty($motdepasse)) {
                    $query .= ", password = :motdepasse";
                }
                $query .= " WHERE id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':id', $id_utilisateur);
                if (!empty($motdepasse)) {
                    $motdepasse_hache = password_hash($motdepasse, PASSWORD_DEFAULT);
                    $stmt->bindParam(':motdepasse', $motdepasse_hache);
                }
                $stmt->execute();
                $_SESSION['message'] = "Utilisateur modifié avec succès.";
            }
        }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_utilisateur'])) {
        $id_utilisateur = $_POST['id_utilisateur'];

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM billets WHERE id_utilisateur = :id");
            $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            $billets_count = $stmt->fetchColumn();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM commentaires WHERE id_utilisateur = :id");
            $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            $commentaires_count = $stmt->fetchColumn();

            if ($billets_count > 0 || $commentaires_count > 0) {
                throw new Exception("Bah... Non ?");
            }

            $stmt = $pdo->prepare("DELETE FROM commentaires WHERE id_utilisateur = :id");
            $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = :id");
            $stmt->bindParam(':id', $id_utilisateur, PDO::PARAM_INT);
            $stmt->execute();
            $pdo->commit();
            $_SESSION['message'] = "L'utilisateur a été supprimé avec succès.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['message'] = $e->getMessage();
        }
    }
}

        if (isset($_POST['supprimer_photo'])) {
            $id_utilisateur = $_POST['id_utilisateur'];
            $stmt = $pdo->prepare("SELECT photo_profil FROM utilisateurs WHERE id = :id");
            $stmt->bindParam(':id', $id_utilisateur);
            $stmt->execute();
            $photo_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($photo_user && !empty($photo_user['photo_profil'])) {
                unlink('uploads/' . $photo_user['photo_profil']);
            }

            $stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = NULL WHERE id = :id");
            $stmt->bindParam(':id', $id_utilisateur);
            $stmt->execute();
            $_SESSION['message'] = "Photo de profil supprimée avec succès.";
        }

        if (isset($_FILES['photo_profil'])) {
            $id_utilisateur = $_POST['id_utilisateur'];
            $file = $_FILES['photo_profil'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $extensions_valides = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                
                if (in_array($extension, $extensions_valides)) {
                    $nom_fichier = uniqid() . '.' . $extension;
                    move_uploaded_file($file['tmp_name'], 'uploads/' . $nom_fichier);
                    $stmt = $pdo->prepare("UPDATE utilisateurs SET photo_profil = :photo_profil WHERE id = :id");
                    $stmt->bindParam(':photo_profil', $nom_fichier);
                    $stmt->bindParam(':id', $id_utilisateur);
                    $stmt->execute();
                    $_SESSION['message'] = "Photo de profil mise à jour avec succès.";
                } else {
                    $_SESSION['message'] = "Format de fichier non valide. Seuls les JPG, PNG et GIF sont autorisés.";
                }
            }
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de l'opération : " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modifier_billet'])) {
        $id_billet = $_POST['id_billet'];
        $titre = $_POST['titre'];
        $contenu = $_POST['contenu'];
        $stmt = $pdo->prepare("UPDATE billets SET titre = :titre, contenu = :contenu WHERE id = :id");
        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_billet, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['message'] = "Le billet a été modifié avec succès.";
    }

    if (isset($_POST['delete_billet'])) {
        $id_billet = $_POST['id_billet'];
        $stmt = $pdo->prepare("DELETE FROM billets WHERE id = :id");
        $stmt->bindParam(':id', $id_billet, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['message'] = "Le billet a été supprimé avec succès.";
    }

    if (isset($_POST['modifier_commentaire'])) {
        $id_commentaire = $_POST['id_commentaire'];
        $contenu = $_POST['contenu'];
        $stmt = $pdo->prepare("UPDATE commentaires SET contenu = :contenu WHERE id = :id");
        $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id_commentaire, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['message'] = "Le commentaire a été modifié avec succès.";
    }

    if (isset($_POST['delete_commentaire'])) {
        $id_commentaire = $_POST['id_commentaire'];
        $stmt = $pdo->prepare("DELETE FROM commentaires WHERE id = :id");
        $stmt->bindParam(':id', $id_commentaire, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['message'] = "Le commentaire a été supprimé avec succès.";
    }
}

$stmt_billets = $pdo->query("SELECT * FROM billets ORDER BY date_post DESC");
$billets = $stmt_billets->fetchAll(PDO::FETCH_ASSOC);
$stmt_utilisateurs = $pdo->query("SELECT * FROM utilisateurs");
$utilisateurs = $stmt_utilisateurs->fetchAll(PDO::FETCH_ASSOC);
$stmt_commentaires = $pdo->query("SELECT c.*, u.login AS utilisateur_login FROM commentaires c JOIN utilisateurs u ON c.id_utilisateur = u.id ORDER BY c.date_post DESC");
$commentaires = $stmt_commentaires->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration du Blog</title>
    <style>
        .container {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
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

    <h1>Administration du Blog</h1>
    
    <h2>Gestion des Utilisateurs</h2>
    <?php foreach ($utilisateurs as $utilisateur): ?>
        <div class="container">
        <h2><?php echo htmlspecialchars($utilisateur['login']); ?></h3>
        
        <form action="admin.php" method="POST">
            <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id']; ?>">
            <input type="text" name="login" value="<?php echo htmlspecialchars($utilisateur['login']); ?>" required>
            <input type="password" name="motdepasse" placeholder="Nouveau mot de passe (laisser vide pour ne pas changer)">
            <button type="submit" name="modifier_utilisateur">Modifier l'Utilisateur</button>
            <button type="submit" name="delete_utilisateur">Supprimer l'Utilisateur</button>
        </form>
        
        <section>
            <h3>Photo de Profil</h2>
            <?php
            $stmt_photo = $pdo->prepare("SELECT photo_profil FROM utilisateurs WHERE id = :id");
            $stmt_photo->bindParam(':id', $utilisateur['id']);
            $stmt_photo->execute();
            $photo_user = $stmt_photo->fetch(PDO::FETCH_ASSOC);

            if ($photo_user && !empty($photo_user['photo_profil'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($photo_user['photo_profil']); ?>" alt="Photo de Profil" style="width:100px;height:100px;border-radius:50%;">
                <form action="admin.php" method="POST">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id']; ?>">
                    <button type="submit" name="supprimer_photo">Supprimer la Photo</button>
                </form>
            <?php else: ?>
                <p>Aucune photo de profil chargée.</p>
            <?php endif; ?>

            <section>
                <form action="admin.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_utilisateur" value="<?php echo $utilisateur['id']; ?>">
                    <input type="file" name="photo_profil" accept="image/*" required>
                    <button type="submit">Charger une Nouvelle Photo</button>
                </form>
            </section>
        </section>
        </div>
    <?php endforeach; ?>
    
    <h2>Gestion des Billets et des Commentaires Associés</h2>
    <br>
    <?php foreach ($billets as $billet): ?>
    <div class="container">
        <h3><?php echo htmlspecialchars($billet['titre']); ?></h3>
        <p><?php echo nl2br(htmlspecialchars($billet['contenu'])); ?></p>
        <p>Date de publication : <?php echo $billet['date_post']; ?></p>
        <form action="admin.php" method="POST">
            <input type="hidden" name="id_billet" value="<?php echo $billet['id']; ?>">
            <input type="text" name="titre" value="<?php echo htmlspecialchars($billet['titre']); ?>" required>
            <textarea rows="10" cols="30" name="contenu" required><?php echo htmlspecialchars($billet['contenu']); ?></textarea>
            <button type="submit" name="modifier_billet">Modifier le Billet</button>
            <button type="submit" name="delete_billet">Supprimer le Billet</button>
        </form>

        <h4>Commentaires associés</h4>
        
        <?php
        $stmt_commentaires = $pdo->prepare("SELECT * FROM commentaires WHERE id_billet = :id_billet ORDER BY date_post DESC");
        $stmt_commentaires->bindParam(':id_billet', $billet['id'], PDO::PARAM_INT);
        $stmt_commentaires->execute();
        $commentaires = $stmt_commentaires->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (count($commentaires) > 0): ?>
            <?php foreach ($commentaires as $commentaire): ?>
                <h5><?php echo htmlspecialchars($commentaire['utilisateur_login']); ?></h5>
                <p><?php echo nl2br(htmlspecialchars($commentaire['contenu'])); ?></p>
                <p>Date de publication : <?php echo $commentaire['date_post']; ?></p>
                <form action="admin.php" method="POST">
                    <input type="hidden" name="id_commentaire" value="<?php echo $commentaire['id']; ?>">
                    <textarea name="contenu"><?php echo htmlspecialchars($commentaire['contenu']); ?></textarea>
                    <button type="submit" name="modifier_commentaire">Modifier le commentaire</button>
                    <button type="submit" name="delete_commentaire">Supprimer le commentaire</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun commentaire pour ce billet.</p>
        <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>