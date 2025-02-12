<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./assets/styles/styles.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Login</title>
</head>

<body>
    <video autoplay muted loop id="video">
        <source src="./assets/videos/background.mp4">
    </video>
    <div class="user-container">
        <div id="errors-container">
            <?php
            session_start();
            // Réinitialiser la session
            unset($_SESSION["LOGGED_IDENTITY"]);
            try {
                // Paramètres de connexion
                $config = require_once "./includes/config.php";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ];

                // Instancier la connexion
                $pdo = new PDO(
                    "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8",
                    $config['username'],
                    $config['password'],
                    $options
                );

                // Vérifier si l'utilisateur veux se connecter
                if (
                    isset($_GET["username"]) &&
                    !empty($_GET["username"]) &&
                    !empty($_GET["password"]) &&
                    isset($_GET["password"])
                ) {
                    // Enleve tous les espace de début et de fin pour éviter les erreurs utilisateurs
                    $username = trim($_GET["username"]);
                    $password = trim($_GET["password"]);

                    // Detecter les injections sql et les rediriger si trouver
                    require_once "./includes/detection.php";
                    if (
                        detect_sql_injection($username) ||
                        detect_sql_injection($password)
                    ) {
                        // Aviser l'utilisateur qu'il n'est pas autorisé de faire des injections sql.
                        header("Location: ./includes/prohibited.php");
                        die();
                    }

                    // Generation de la requete
                    $requete = $pdo->prepare("SELECT password FROM usagers WHERE name = :username");
                    $requete->execute(['username' => $username]);

                    // Execution de la requete
                    $results = $requete->fetchAll();

                    if (
                        count($results) == 1 &&
                        password_verify($password,  $results[0]["password"])
                    ) {
                        // L'utilisateur c'est connectée, connectons et redirigeons le vers le forum.
                        $_SESSION["LOGGED_IDENTITY"] = $username;
                        header("Location: includes/forum.php");
                    } else {
                        // Afficher à l'utilisateur une error
                        echo "<div class='error'>Invalid username or password</div>";
                    }
                }
            } catch (PDOException $err) {
                // On affiche l'erreur à l'utilisateur. Pas optimal et devrais pas être mits en productions
                die($err->getMessage());
            }
            ?>
        </div>
        <div id="containerForm">
            <form id="formulaire">
                <label class="label" for="username">Identifiant:</label>
                <input class="input" name="username" type="text" placeholder="Entrez votre nom d'utilisateur" autofocus required
                    value='<?php echo isset($_SESSION["username"]) && !empty($_SESSION["username"]) ? $_SESSION["username"] : "" ?>' />
                <label class="label" for="password">Mot de passe:</label>
                <input class="input" name="password" placeholder="Entrez votre mot de passe" type="password" required
                    value='<?php echo isset($_SESSION["password"]) && !empty($_SESSION["password"]) ? $_SESSION["password"] : "" ?>' />
                <div id="container">
                    <button class="btn" type="submit">Connecter</button>
                    <button class="btn" type="reset"> Effacer</button>
                </div>
                <div id="inscription">
                    <a id="nocompte" href="./includes/inscription.php">Pas de compte ?</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

<?php
// These session tokens should be used only once
unset($_SESSION["username"]);
unset($_SESSION["password"]);
