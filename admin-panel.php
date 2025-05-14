<php
// Script pour accéder à l'interface d'administration des messages
session_start();

// Configuration de la base de données
$servername = "localhost";
$username = "jpskz"; // À remplacer par votre nom d'utilisateur MySQL
$password = "753698"; // À remplacer par votre mot de passe MySQL
$dbname = "cv_web";

// Vérifier si l'utilisateur est connecté
$loggedIn = false;
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $loggedIn = true;
}

// Traiter la tentative de connexion
if (isset($_POST['login'])) {
    $admin_username = "jpskz"; // Changez ceci avec un nom d'utilisateur sécurisé
    $admin_password = "789635"; // Changez ceci avec un mot de passe sécurisé
    
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $loggedIn = true;
    } else {
        $error_message = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}

// Traiter la déconnexion
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Marquer un message comme lu
if ($loggedIn && isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("La connexion a échoué : " . $conn->connect_error);
    }
    
    $id = (int)$_GET['mark_read'];
    $stmt = $conn->prepare("UPDATE messages SET lu = TRUE WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    
    header("Location: admin.php");
    exit();
}

// Supprimer un message
if ($loggedIn && isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("La connexion a échoué : " . $conn->connect_error);
    }
    
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    
    header("Location: admin.php");
    exit();
}

// Récupérer les messages si connecté
$messages = [];
if ($loggedIn) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("La connexion a échoué : " . $conn->connect_error);
    }
    
    $result = $conn->query("SELECT * FROM messages ORDER BY date_envoi DESC");
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    
    $conn->close();
}
>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - CV Web</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            background-color: rgb(0, 0, 0, 0.93);
            color: azure;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .login-form {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .message {
            background-color: white;
            border-left: 5px solid #007bff;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .message.unread {
            border-left-color: #dc3545;
            background-color: #f8f9fa;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .message-actions {
            display: flex;
            gap: 10px;
        }
        .message-content {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
        .btn-back {
            background: none;
            border: none;
            color: azure;
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$loggedIn): ?>
            <!-- Formulaire de connexion -->
            <div class="login-form">
                <h2 class="text-center mb-4">Connexion Admin</h2>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Se connecter</button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="index.php">Retour au CV</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Panneau d'administration -->
            <div class="header">
                <h1>Gestion des messages</h1>
                <a href="?logout=1" class="btn-back">Déconnexion</a>
            </div>
            
            <?php if (count($messages) === 0): ?>
                <div class="alert alert-info">Aucun message pour le moment.</div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message <?php echo $msg['lu'] ? '' : 'unread'; ?>">
                        <div class="message-header">
                            <div>
                                <strong>De :</strong> <?php echo htmlspecialchars($msg['nom']); ?> (<?php echo htmlspecialchars($msg['email']); ?>)
                                <br>
                                <strong>Sujet :</strong> <?php echo htmlspecialchars($msg['sujet']); ?>
                                <br>
                                <small class="text-muted">Reçu le <?php echo date('d/m/Y à H:i', strtotime($msg['date_envoi'])); ?></small>
                                <?php if (!$msg['lu']): ?>
                                    <span class="badge bg-danger ms-2">Non lu</span>
                                <?php endif; ?>
                            </div>
                            <div class="message-actions">
                                <?php if (!$msg['lu']): ?>
                                    <a href="?mark_read=<?php echo $msg['id']; ?>" class="btn btn-sm btn-success">Marquer comme lu</a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');">Supprimer</a>
                            </div>
                        </div>
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">Retour au CV</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
