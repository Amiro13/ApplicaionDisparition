<?php
session_start();

// Vérifiez si l'utilisateur est déjà connecté (a une session active)
if (isset($_SESSION["utilisateur_id"])) {
    // Détruire la session
    session_destroy();

    // Rediriger l'utilisateur vers la page de connexion (ou une autre page de votre choix)
    header("Location: connexion.php");
    exit();
} else {
    // Si l'utilisateur n'a pas de session active, vous pouvez rediriger vers une page d'accueil ou une autre page appropriée.
    header("Location: index.php"); // Remplacez "index.php" par la page souhaitée.
    exit();
}
?>
