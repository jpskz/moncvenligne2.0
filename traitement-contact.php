<php
// Configuration de la base de données
$servername = "localhost"; // Généralement localhost pour les hébergements LAMP/WAMP
$username = "jpskz"; // À remplacer par votre nom d'utilisateur MySQL
$password = "753698"; // À remplacer par votre mot de passe MySQL
$dbname = "cv_web"; // Nom de votre base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Définir l'encodage des caractères
$conn->set_charset("utf8");

// Variables pour les messages d'erreur et de succès
$message_erreur = "";
$message_succes = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $sujet = htmlspecialchars(trim($_POST['sujet']));
    $message = htmlspecialchars(trim($_POST['message']));
    $date_envoi = date("Y-m-d H:i:s");
    
    // Validation des données
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $message_erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_erreur = "L'adresse email n'est pas valide.";
    } else {
        // Préparer et exécuter la requête SQL
        $stmt = $conn->prepare("INSERT INTO messages (nom, email, sujet, message, date_envoi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nom, $email, $sujet, $message, $date_envoi);
        
        if ($stmt->execute()) {
            $message_succes = "Votre message a été envoyé avec succès. Je vous contacterai bientôt.";
            
            // Envoyer une notification par email (optionnel)
            $destinataire = "jeanpaulsolkiewicz@gmail.com"; // Votre email
            $sujet_email = "Nouveau message depuis votre CV Web";
            $contenu_email = "Vous avez reçu un nouveau message :\n\n";
            $contenu_email .= "Nom : " . $nom . "\n";
            $contenu_email .= "Email : " . $email . "\n";
            $contenu_email .= "Sujet : " . $sujet . "\n";
            $contenu_email .= "Message : " . $message . "\n";
            
            $headers = "From: " . $email . "\r\n";
            
            mail($destinataire, $sujet_email, $contenu_email, $headers);
            
            // Réinitialiser les champs du formulaire
            $nom = $email = $sujet = $message = "";
        } else {
            $message_erreur = "Une erreur est survenue : " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Fermer la connexion
$conn->close();

// Redirection vers la page avec un message de statut
if (!empty($message_succes) || !empty($message_erreur)) {
    $message_type = !empty($message_succes) ? "success" : "error";
    $message_content = !empty($message_succes) ? $message_succes : $message_erreur;
    header("Location: index.php?message_type=" . $message_type . "&message=" . urlencode($message_content) . "#contact");
    exit();
}
>
