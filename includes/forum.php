<?php
session_start();

if (!isset($_SESSION["LOGGED_IDENTITY"]) || empty($_SESSION["LOGGED_IDENTITY"])) {
    header("Location: ../index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php echo "<p>Bonjour {$_SESSION["LOGGED_IDENTITY"]}!</p>"; ?>
    <a href="../">Se déconnecter</a>
    <form>
        <label for="message">Message:</label>
        <input name="message" placeholder="Message à envoyer">
        <button type="submit">Envoyer</button>
    </form>
    <div>
        <?php
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

            if (isset($_GET["message"]) && !empty($_GET["message"])) {

                $requete = $pdo->prepare("SELECT id FROM usagers WHERE name = :username");
                $requete->execute(['username' =>  $_SESSION["LOGGED_IDENTITY"]]);

                $result = $requete->fetchAll();
                if (!empty($result)) {

                    $requete = $pdo->prepare("INSERT INTO messages (user_id, date, message) VALUES (:id, NOW(), :msg);");
                    $requete->execute(["id" => $result[0]["id"], "msg" => $_GET["message"]]);

                    $requete->fetchAll();

                    // Redirige vers la meme page pour enlever les parametres get et èviter la répétition de message
                    header("Location: forum.php");
                    exit();
                }
            }

            $request = $pdo->query("SELECT * FROM messages");
            $users_names = [];
            while ($result = $request->fetch()) {
                $id = $result["user_id"];

                if (!isset($users_names[$id])) {
                    $second_request = $pdo->prepare("SELECT name FROM usagers WHERE id = :user_id");
                    $second_request->execute(['user_id' =>  $id]);

                    $username_result = $second_request->fetchAll();
                    if (count($username_result) == 1) {
                        $users_names[$id] = $username_result[0]["name"];
                    }
                }

                echo "<div>({$result["date"]}) " . htmlspecialchars("{$users_names[$id]}: {$result["message"]}");
            }
        } catch (PDOException $err) {
            die($err->getMessage());
        }
        ?>
    </div>
</body>

</html>