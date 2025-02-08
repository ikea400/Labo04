<?php
define("MIN_PASSWORD_LEN", 8);
define("MAX_PASSWORD_LEN", 32);
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

    if (
        isset($_GET["username"]) &&
        !empty($_GET["username"]) &&
        !empty($_GET["password"]) &&
        isset($_GET["password"])
    ) {
        $username = trim($_GET["username"]);
        if (strlen($username) < 3 || strlen($username) > 32) {
            echo "Le nom d'utilisateur doit etre entre 3 et 32 characters.";
        } else {

            $requete = $pdo->prepare("SELECT 1 FROM usagers WHERE name = :ben;");
            $requete->execute(["ben" => $username]);

            $result = $requete->fetchAll();
            if (count($result) > 0) {
                echo "Nom d'utilisateur déja utiliser";
            } else {

                $password = trim($_GET["password"]);
                $password_len = strlen($password);

                $errors = [];

                // Check if password meets complexity requirements
                if (!preg_match('/[A-Z]/', $password)) {
                    array_push($errors, "Le mot de passe doit contenir au moins une lettre majuscule.");
                }
                if (!preg_match('/[a-z]/', $password)) {
                    array_push($errors, "Le mot de passe doit contenir au moins une lettre minuscule.");
                }
                if (!preg_match('/\d/', $password)) {
                    array_push($errors, "Le mot de passe doit contenir au moins un chiffre.");
                }

                // Check password length constraints
                $passwordLength = strlen($password);
                $minLength = constant('MIN_PASSWORD_LEN');
                $maxLength = constant('MAX_PASSWORD_LEN');

                if ($passwordLength < $minLength) {
                    array_push($errors, "Le mot de passe doit contenir au moins $minLength caractères.");
                }
                if ($passwordLength > $maxLength) {
                    array_push($errors, "Le mot de passe doit contenir au maximum $maxLength caractères.");
                }

                // Return errors or success
                if (!empty($errors)) {
                    echo implode("<br>", $errors);
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    //$requete = $pdo->prepare("SELECT password FROM usagers WHERE name = :username");
                    $requete = $pdo->prepare("INSERT INTO usagers (name, password) VALUES (:username, :password);");
                    $requete->execute(
                        [
                            'username' => $username,
                            'password' => $hashed_password
                        ]
                    );
                    
                    session_start();
                    $_SESSION["username"] = $username;
                    $_SESSION["password"] = $password;

                    header("Location: ../index.php");
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <style>
        form {
            display: flex;
            flex-direction: column;
            width: 20em;
            gap: 0.5em;
            align-items: center;
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <form>
        <label for="username">Username</label>
        <input name="username" type="text" required />
        <label for="password">Password</label>
        <input name="password" type="password" required />
        <button type="submit">Senregister</button>
        <a href="./">Se connecter</a>
    </form>
</body>

</html>