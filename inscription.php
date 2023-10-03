<!DOCTYPE html>
<html>

<head>
    <title>Inscription</title>
    <link rel="stylesheet" href="inscription.css">
</head>

<body>

    <h2>Inscription</h2>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data" class="inscription-form">
        <!-- Première colonne -->
        <div class="col1">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Adresse e-mail :</label>
            <input type="email" id="email" name="email" required>

            <label for="dateNaissance">Date de naissance :</label>
            <input type="date" id="dateNaissance" name="dateNaissance" required>

            <label for="telephone">Téléphone :</label>
            <input type="tel" id="telephone" name="telephone" required>
        </div>

        <!-- Deuxième colonne -->
        <div class="col2">
            <label for="motDePasse">Mot de passe :</label>
            <input type="password" id="motDePasse" name="motDePasse" required>

            <label for="avatar">Télécharger votre propre avatar :</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">

            <label for="adresse">Adresse postale :</label>
            <input type="text" id="adresse" name="adresse" required>
        </div>

        <button type="submit" class="inscrire-btn">S'inscrire</button>
    </form>

    <!-- Bouton pour la page de connexion -->
    <a href="connexion.php" class="connexion-btn">Connexion</a>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $dateNaissance = $_POST["dateNaissance"];
    $telephone = $_POST["telephone"];
    $motDePasse = $_POST["motDePasse"];
    $avatarNom = $_FILES["avatar"]["name"];
    $avatarTempName = $_FILES["avatar"]["tmp_name"];
    $adressePostale = $_POST["adresse"]; // Utilisation du même nom de champ que dans le formulaire

    // Validation des données (ajoutez des validations plus poussées selon vos besoins)

    // Hash du mot de passe
    $motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);

    // Connexion à la base de données
    $serveur = "localhost";
    $utilisateur = "root"; // Remplacez par le nom d'utilisateur MySQL correct
    $motDePasseDB = ""; // Remplacez par le mot de passe MySQL correct
    $baseDeDonnées = "disparition"; // Remplacez par le nom de la base de données correcte

    $connexion = new mysqli($serveur, $utilisateur, $motDePasseDB, $baseDeDonnées);

    // Vérification de la connexion à la base de données
    if ($connexion->connect_error) {
        die("La connexion à la base de données a échoué : " . $connexion->connect_error);
    }

    // Requête SQL pour insérer l'utilisateur dans la base de données
    $requete = "INSERT INTO utilisateurs (nom, prenom, mail, mot_de_passe, avatars, adresse_postale, telephone, date_naissance) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $preparation = $connexion->prepare($requete);

    // Vérification de la préparation de la requête
    if ($preparation) {
        $preparation->bind_param("ssssssss", $nom, $prenom, $email, $motDePasseHash, $avatarNom, $adressePostale, $telephone, $dateNaissance);

        // Enregistrement de l'avatar sur le serveur
        $dossierAvatar = "avatars";
        $cheminAvatar = $dossierAvatar . $avatarNom;

        if (move_uploaded_file($avatarTempName, $cheminAvatar)) {
            // Exécution de la requête
            if ($preparation->execute()) {
                echo '<script>alert("Inscription réussie !");</script>';
            } else {
                echo "Erreur lors de l'inscription : " . $preparation->error;
            }
        } else {
            echo "Erreur lors du téléchargement de l'avatar.";
        }

        // Fermeture de la préparation
        $preparation->close();
    } else {
        echo "Erreur de préparation de la requête : " . $connexion->error;
    }

    // Fermeture de la connexion à la base de données
    $connexion->close();
}
?>
