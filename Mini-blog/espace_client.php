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

$stmt_user = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt_user->bindParam(':id', $_SESSION['user_id']);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['new_login']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $errors = [];

    if (empty($login)) {
        $errors[] = "Le login ne peut pas être vide.";
    }
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        $query = "UPDATE utilisateurs SET login = :login";
        if (!empty($new_password)) {
            $query .= ", password = :motdepasse";
        }
        $query .= " WHERE id = :id";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        
        if (!empty($new_password)) {
            $motdepasse_hache = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->bindParam(':motdepasse', $motdepasse_hache);
        }

        if ($stmt->execute()) {
            $_SESSION['message'] = "Informations mises à jour avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors de la mise à jour.";
        }

        header('Location: espace_client.php');
        exit();
    } else {
        $_SESSION['errors'] = $errors;
    }
}

$messages = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
unset($_SESSION['message'], $_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Client - Mon Mini Blog</title>
</head>
<body>
    <header>
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
    </header>
    <main>
        <h1>Bienvenue, <?php echo htmlspecialchars($user['login']); ?></h1>

        <?php if (!empty($messages)): ?>
            <p><?php echo htmlspecialchars($messages); ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
         <section>
            <h2>Votre Photo de Profil</h2>
            <?php if ($user['photo_profil']): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['photo_profil']); ?>" alt="Photo de Profil" style="width:100px;height:100px;border-radius:50%;">
                <form action="supprimer_photo_profil.php" method="POST">
                    <button type="submit" name="supprimer_photo">Supprimer la Photo</button>
                </form>
            <?php else: ?>
                <p>Aucune photo de profil chargée.</p>
            <?php endif; ?>
             <?php if ($photo_supprimee): ?>
            <p>La photo de profil a été supprimée avec succès.</p>
        <?php endif; ?>

        <section>
            <form action="traiter_photo_profil.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="photo_profil" accept="image/*" required>
                <button type="submit">Charger la Photo</button>
            </form>
        </section>
        </section>

        <section>
            <h2>Modifier vos Informations</h2>
            <form action="espace_client.php" method="POST">
                <label for="new_login">Modifier votre login :</label>
                <input type="text" id="new_login" name="new_login" value="<?php echo htmlspecialchars($user['login']); ?>" required>

                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe">

                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe">
                
                <button type="submit">Mettre à jour les Informations</button>
            </form>
        </section>
    </main>
</body>
</html>