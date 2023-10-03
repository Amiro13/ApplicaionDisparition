<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" href="connexion.css">
</head>
<body>
    <h2>Connexion</h2>

    <?php
    session_start(); // Démarrez la session

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $motDePasse = $_POST["motDePasse"];

        // Validez les données (ajoutez des validations supplémentaires selon vos besoins)

        // Connexion à la base de données
        $serveur = "localhost";
        $utilisateur = "root"; // Remplacez par le nom d'utilisateur MySQL correct
        $motDePasseDB = ""; // Remplacez par le mot de passe MySQL correct
        $baseDeDonnees = "disparition"; // Remplacez par le nom de la base de données correcte

        $connexion = new mysqli($serveur, $utilisateur, $motDePasseDB, $baseDeDonnees);

        // Vérification de la connexion à la base de données
        if ($connexion->connect_error) {
            die("La connexion à la base de données a échoué : " . $connexion->connect_error);
        }

        // Requête SQL pour vérifier l'authentification de l'utilisateur
        $requete = "SELECT id, nom, mot_de_passe FROM utilisateurs WHERE mail = ?";
        $preparation = $connexion->prepare($requete);

        if ($preparation) {
            $preparation->bind_param("s", $email);
            $preparation->execute();
            $resultat = $preparation->get_result();

            if ($resultat->num_rows == 1) {
                $utilisateur = $resultat->fetch_assoc();
                if (password_verify($motDePasse, $utilisateur["mot_de_passe"])) {
                    // L'utilisateur est connecté avec succès
                    $_SESSION["utilisateur_id"] = $utilisateur["id"]; // Stockez l'ID de l'utilisateur dans la session
                    // Rediriger l'utilisateur vers sa page de profil
                    header("Location: profil.php");
                    exit();

                    // Après une connexion réussie
                    setcookie("nouvelle_annonce", "true", time() + 3600); // Cookie expirant dans 1 heure
                    header("Location: profil.php");
                    exit();


                } else {
                    $motDePasseIncorrect = true;
                }
            } else {
                $emailNonTrouve = true;
            }

            $preparation->close();
        } else {
            echo "Erreur de préparation de la requête : " . $connexion->error;
        }

        // Fermeture de la connexion à la base de données
        $connexion->close();
    }
    ?>

    <!-- Message modal pour l'erreur de mot de passe -->
    <div id="erreur-modal" class="modal">
        <div class="modal-content red">
            <p id="erreur-message">Mot de passe incorrect.</p>
        </div>
    </div>

    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="email">Adresse e-mail :</label>
        <input type="email" id="email" name="email" required>

        <label for="motDePasse">Mot de passe :</label>
        <input type="password" id="motDePasse" name="motDePasse" required>

        <button type="submit">Se connecter</button>
    </form>

    <a href="inscription.php" class="inscription-btn">inscription</a>

    <!-- Ajoutez le code JavaScript à la fin de votre page HTML, juste avant la balise </body> -->
    <script>
        // Fonction pour afficher le message modal
        function afficherMessageModal(message) {
            var modal = document.getElementById("erreur-modal");
            var messageSpan = document.getElementById("erreur-message");

            messageSpan.innerHTML = message;
            modal.style.display = "block";

            // Masquer le message modal après 4 secondes
            setTimeout(function () {
                modal.style.display = "none";
            }, 3000);
        }

        // Appel de la fonction pour afficher le message modal en cas d'erreur de mot de passe
        <?php
        if (isset($motDePasseIncorrect) && $motDePasseIncorrect) {
            echo 'afficherMessageModal("Mot de passe incorrect.");';
        }
        ?>
    </script>
</body>
</html>
