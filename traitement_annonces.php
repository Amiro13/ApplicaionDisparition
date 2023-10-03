<?php
session_start();

// Assurez-vous que l'utilisateur est connecté
if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: connexion.php");
    exit();
}

// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root"; // Remplacez par le nom d'utilisateur MySQL correct
$motDePasseDB = ""; // Remplacez par le mot de passe MySQL correct
$baseDeDonnees = "disparition"; // Remplacez par le nom de la base de données correcte

$connexion = new mysqli($serveur, $utilisateur, $motDePasseDB, $baseDeDonnees);

if ($connexion->connect_error) {
    die("La connexion à la base de données a échoué : " . $connexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST["titre"];
    $description = $_POST["description"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];

    var_dump($_POST);

    // Validation et traitement des autres champs (ajoutez vos validations ici)

    // Gestion de l'image téléchargée
    $dossierImages = "images/"; // Dossier où stocker les images
    $nomFichier = basename($_FILES["image"]["name"]);
    $cheminFichier = $dossierImages . $nomFichier;

    // Vérifier si le fichier image est valide
    $typeFichier = strtolower(pathinfo($cheminFichier, PATHINFO_EXTENSION));
    $autoriseTypes = array("jpg", "jpeg", "png", "gif");

    if (in_array($typeFichier, $autoriseTypes)) {
        // Déplacer l'image vers le dossier
        move_uploaded_file($_FILES["image"]["tmp_name"], $cheminFichier);

        // Enregistrement de l'annonce dans la base de données
        $utilisateurId = $_SESSION["utilisateur_id"]; // Vous devez récupérer l'ID de l'utilisateur connecté

        $requete = "INSERT INTO annonces (id_utilisateurs, description, titre, photos, date, latitude, longitude) VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $preparation = $connexion->prepare($requete);
        
        if ($preparation) {
            $preparation->bind_param("isssdd", $utilisateurId, $description, $titre, $cheminFichier, $latitude, $longitude);
            $preparation->execute();
            $preparation->close();

            // Après avoir inséré l'annonce avec succès
        $_SESSION['annonce_creee'] = "L'annonce a été créée avec succès";
        } else {
            echo "Erreur de préparation de la requête : " . $connexion->error;
        }

        // Rediriger l'utilisateur vers son profil ou une autre page
        header("Location: profil.php");
        exit();
    } else {
        echo "Type de fichier non autorisé.";
    }
}

$connexion->close();
?>
