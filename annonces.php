<!DOCTYPE html>
<html>
<head>
    <title>Annonces</title>
    <link rel="stylesheet" type="text/css" href="annonces.css">
    <a href="profil.php" class="profil-btn">Profil</a>
</head>
<body>

    <h1>Liste des Annonces</h1>

    <!-- Affichage de toutes les annonces depuis la base de données -->
    <?php
    // Connexion à la base de données (à personnaliser)
    $serveur = "localhost";
    $utilisateur = "root"; // Remplacez par le nom d'utilisateur MySQL correct
    $motDePasseDB = ""; // Remplacez par le mot de passe MySQL correct
    $baseDeDonnees = "disparition"; // Remplacez par le nom de la base de données correcte

    $connexion = new mysqli($serveur, $utilisateur, $motDePasseDB, $baseDeDonnees);

    if ($connexion->connect_error) {
        die("La connexion à la base de données a échoué : " . $connexion->connect_error);
    }

    $requeteAnnonces = "SELECT * FROM annonces ORDER BY date DESC";
    $resultatAnnonces = $connexion->query($requeteAnnonces);

    if ($resultatAnnonces->num_rows > 0) {
        while ($annonce = $resultatAnnonces->fetch_assoc()) {
            $annonceId = $annonce["id"];
            $titre = $annonce["titre"];
            $description = $annonce["description"];
            $photo = $annonce["photos"];
            $date = $annonce["date"];
            
            // Affichez les détails de chaque annonce
            echo '<div class="annonce">';
            echo '<h2>' . $titre . '</h2>';
            echo '<p>Date de création : ' . $date . '</p>';
            echo '<p>' . $description . '</p>';
            echo '<img src="' . $photo . '" alt="' . $titre . '" width="300">';
            
            // Formulaire de commentaire
            echo '<form method="post" action="traitement_commentaires.php">';
            echo '<input type="hidden" name="annonce_id" value="' . $annonceId . '">';
            echo '<label for="commentaire">Laisser un commentaire :</label>';
            echo '<textarea id="commentaire" name="commentaire" required></textarea>';
            echo '<button type="submit">Poster un commentaire</button>';
            echo '</form>';
            
            // Afficher les commentaires pour cette annonce (vous devez implémenter cette partie)
            // ...

            echo '</div>';
        }
    } else {
        echo 'Aucune annonce trouvée.';
    }

    // Fermeture de la connexion à la base de données
    $connexion->close();
    ?>

</body>
</html>
