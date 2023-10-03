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

// Récupérer l'avatar de l'utilisateur
$utilisateurId = $_SESSION["utilisateur_id"];
$requeteAvatar = "SELECT avatars FROM utilisateurs WHERE id = ?";
$preparationAvatar = $connexion->prepare($requeteAvatar);

if ($preparationAvatar) {
    $preparationAvatar->bind_param("i", $utilisateurId);
    $preparationAvatar->execute();
    $resultatAvatar = $preparationAvatar->get_result();

    if ($resultatAvatar->num_rows == 1) {
        $utilisateurAvatar = $resultatAvatar->fetch_assoc();
        $nomAvatar = $utilisateurAvatar["avatars"];
    }

    $preparationAvatar->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST["titre"];
    $description = $_POST["description"];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];

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
            
            // Définir le message de succès dans la session
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
<!DOCTYPE html>
<html>
<head>
    <title>Mon Profil</title>
    <link rel="stylesheet" type="text/css" href="profil.css">
    <script>
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;
                // Remplissez les champs cachés avec les valeurs de latitude et de longitude
                document.getElementById("latitude").value = latitude;
                document.getElementById("longitude").value = longitude;
            }, function(error) {
                console.error("Erreur de géolocalisation : " + error.message);
            });
        } else {
            console.log("La géolocalisation n'est pas prise en charge par votre navigateur.");
        }
    </script>
</head>
<body>

<nav class="navbar">
    <div class="navbar-content">
        <div class="logo">Found Me</div>
        <a href="deconnexion.php" class="deconnexion-btn">Deconnexion</a>
    </div>
</nav>
<div class="content">
    <!-- Photo de profil -->
    <div class="profile-photo">
        <img src="<?php echo $nomAvatar; ?>" alt="Photo de profil">
    </div>

    <!-- Formulaire de création d'annonce -->
    <div class="annonce-form">

        <h3>Créer une annonce</h3>

        <form method="post" action="traitement_annonces.php" enctype="multipart/form-data">
            <!-- Ajoutez ici les champs du formulaire -->
            <label for="titre">Titre de l'annonce :</label>
            <input type="text" id="titre" name="titre" required>
            <label for="description">Description :</label>
            <textarea id="description" name="description" required></textarea>
            <label for="image">Télécharger une photo :</label>
            <input type="file" id="image" name="image" accept="image/*" required>
            <!-- Champ de latitude caché -->
            <input type="hidden" id="latitude" name="latitude" value="">
            <!-- Champ de longitude caché -->
            <input type="hidden" id="longitude" name="longitude" value="">
            <!-- Autres champs pour les informations de l'annonce -->
            <button type="submit">Créer l'annonce</button>
        </form>
    </div>

            <!-- Bouton "Annonces" -->
    <a href="annonces.php" class="annonces-btn">Annonces</a>

</div>

<footer class="footer">
</footer>

<script>
   // Fonction pour afficher une alerte lorsque l'utilisateur se connecte
   function afficherAlerte() {
        alert("votre annonce a été créee !");
    }

    // Appelez la fonction pour afficher l'alerte si la session contient une annonce créée
    <?php
    if (isset($_SESSION['annonce_creee'])) {
        echo 'afficherAlerte();';
        // Supprimez la variable de session après l'avoir affichée
        unset($_SESSION['annonce_creee']);
    }
    ?>
</script>

</body>
</html>