<?php
session_start();

if (!isset($_SESSION["LOGGED_IDENTITY"]) || empty($_SESSION["LOGGED_IDENTITY"])) {
    header("Location: ../index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forum</title>
    <link rel="stylesheet" href="../assets/styles/styles.css">
</head>

<body id="forum-body">
    <div id="forum-container">
        <header id="forum-header">
            <?php echo "<div>Bonjour {$_SESSION["LOGGED_IDENTITY"]}!</div>" ?>
            <button onclick="location.href='../index.php'" type="button">
                Se déconnecter
            </button>
        </header>
        <div id="chat">
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

                $request = $pdo->query("SELECT * FROM messages  ORDER BY date LIMIT 0, 50");
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

                    echo "<div>({$result["date"]}) " . htmlspecialchars("{$users_names[$id]}: {$result["message"]}") . "</div>";
                }
            } catch (PDOException $err) {
                die($err->getMessage());
            }
            ?>
        </div>
        <form id="message_form">
            <input id="message" name="message" placeholder="Message à envoyer" autofocus />
            <button type="submit">Envoyer</button>
        </form>
    </div>
</body>

</html>