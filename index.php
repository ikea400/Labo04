<?php
session_start();
// Reset login state
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

    if (
        isset($_GET["username"]) &&
        !empty($_GET["username"]) &&
        !empty($_GET["password"]) &&
        isset($_GET["password"])
    ) {
        // Generation de la requete
        $requete = $pdo->prepare("SELECT password FROM usagers WHERE name = :username");
        $requete->execute(['username' => $_GET['username']]);

        // Execution de la requete
        $results = $requete->fetchAll();

        if (
            count($results) == 1 &&
            password_verify($_GET["password"],  $results[0]["password"])
        ) {
            $_SESSION["LOGGED_IDENTITY"] = $_GET["username"];

            header("Location: includes/forum.php");
        } else {
            echo "Invalid username or password";
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
        <input name="username" type="text" value='<?php echo isset($_SESSION["username"]) && !empty($_SESSION["username"]) ? $_SESSION["username"] : "" ?>' required />
        <label for="password">Password</label>
        <input name="password" type="password" value='<?php echo isset($_SESSION["password"]) && !empty($_SESSION["password"]) ? $_SESSION["password"] : "" ?>' required />
        <button type='submit'>Login</button>
        <a href="./includes/inscription.php">S'enregister</a>
    </form>
</body>

</html>

<?php
unset($_SESSION["username"]);
unset($_SESSION["password"]);