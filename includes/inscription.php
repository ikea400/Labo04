<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../assets/styles/styles.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Inscription</title>
</head>

<body>
    <video autoplay muted loop id="video">
        <source src="../assets/videos/background.mp4">
    </video>
    <div class="user-container">
        <div id="errors-container">
            <?php
            define("MIN_PASSWORD_LEN", 8);
            define("MAX_PASSWORD_LEN", 32);
            define("MIN_USERNAME_LEN", 3);
            define("MAX_USERNAME_LEN", 32);
            try {
                // Paramètres de connexion
                $config = require_once "config.php";
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

                // On vérifie si l'utilisateur veux s'enregistrer ou juste voire la page
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
                    require_once "./detection.php";
                    if (
                        detect_sql_injection($username) ||
                        detect_sql_injection($password)
                    ) {
                        // Aviser l'utilisateur qu'il n'est pas autorisé de faire des injections sql.
                        header("Location: ./prohibited.php");
                        die();
                    }

                    $minNameLength = constant('MIN_USERNAME_LEN');
                    $maxNameLength = constant('MAX_USERNAME_LEN');
                    // Vérifie si le nom d'utilisateur est valide
                    if (strlen($username) < $minNameLength || strlen($username) > $maxNameLength) {
                        echo "Le nom d'utilisateur doit etre entre {$minNameLength} et {$maxNameLength} characters.";
                    } else {

                        // Generation de la requete
                        $requete = $pdo->prepare("SELECT 1 FROM usagers WHERE name = :ben;");
                        $requete->execute(["ben" => $username]);

                        // Execution de la requete
                        $result = $requete->fetchAll();
                        if (count($result) > 0) {
                            echo "Nom d'utilisateur déja utiliser";
                        } else {
                            $errors = [];

                            // Vérifie si le mot de passe respecte les contraintes.
                            if (!preg_match('/[A-Z]/', $password)) { // Lettre majuscule
                                array_push($errors, "Le mot de passe doit contenir au moins une lettre majuscule.");
                            }
                            if (!preg_match('/[a-z]/', $password)) { // lettre minuscule
                                array_push($errors, "Le mot de passe doit contenir au moins une lettre minuscule.");
                            }
                            if (!preg_match('/\d/', $password)) { //
                                array_push($errors, "Le mot de passe doit contenir au moins un chiffre.");
                            }

                            // Check password length constraints
                            $minLength = constant('MIN_PASSWORD_LEN');
                            $maxLength = constant('MAX_PASSWORD_LEN');

                            $passwordLength = strlen($password);
                            if ($passwordLength < $minLength) {
                                array_push($errors, "Le mot de passe doit contenir au moins {$minLength} caractères.");
                            } else if ($passwordLength > $maxLength) {
                                array_push($errors, "Le mot de passe doit contenir au maximum {$maxLength} caractères.");
                            }

                            // Return errors or success
                            if (!empty($errors)) {
                                echo implode("<br>", array: $errors);
                            } else {
                                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                                // Generation de la requete
                                $requete = $pdo->prepare("INSERT INTO usagers (name, password) VALUES (:username, :password);");
                                if (!$requete->execute(
                                    [
                                        'username' => $username,
                                        'password' => $hashedPassword
                                    ]
                                )) {
                                    echo "Une erreur inattendue est survenue";
                                } else {

                                    // Sauvegarde le nom d'utilisateur et le mots de passe pour les remplir automatiquement dans la page de connection.
                                    session_start();
                                    $_SESSION["username"] = $username;
                                    $_SESSION["password"] = $password;

                                    // Redirige l'utilisateur vers la page de connection.
                                    header("Location: ../index.php");
                                }
                            }
                        }
                    }
                }
            } catch (PDOException $err) {
                //error_log('PDOException: :' . $err->getMessage())
                //header("Location: erreur.php");
                die($err->getMessage());
            }
            ?>
        </div>
    </div>
    <div id="containerForm">
        <form id="formulaire">
            <label class="label" for="username">Identifiant:</label>
            <input class="input" name="username" type="text" placeholder="Entrez votre nom d'utilisateur" required autofocus/>
            <label class="label" for="password">Mot de passe:</label>
            <input class="input" name="password" placeholder="Entrez votre mot de passe" type="password" required />
            <div id="container">
                <button class="btn" type="submit">S'inscrire</button>

                <button class="btn" type="reset"> Effacer</button>
            </div>
            <div id="inscription">
                <a id="nocompte" href="../index.php">Déja un compte?</a>
            </div>
        </form>
    </div>
</body>

</html>