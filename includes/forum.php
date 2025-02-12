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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/styles/styles.css" rel="stylesheet">
    <title>Forum</title>

</head>

<body id="body-frame">
    <img src="../assets/images/screen.png" alt="image de fond" id="frame">
    <div id="screen">
        Hello world !
        <div id="forum-window">

            <?php echo "<h4 id='welcome-msg'>Bonjour {$_SESSION["LOGGED_IDENTITY"]}!</h4>" ?>
            <button class="btn-forum" id="btn-deconnect" onclick="location.href='../index.php'">
                Se déconnecter
            </button>
            <div id="text-box-container">
                <div id="text-box">
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

                        // Si l'utilisateur veux envoyer un message, sauvegarder son message dans la bd
                        if (isset($_GET["message"]) && !empty($_GET["message"])) {

                            // Generation de la requete
                            $requete = $pdo->prepare("SELECT id FROM usagers WHERE name = :username");
                            $requete->execute(['username' =>  $_SESSION["LOGGED_IDENTITY"]]);

                            // Execution de la requete
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
                        // Generation de la requete
                        $request = $pdo->query("SELECT * FROM (
                                                    SELECT * FROM messages 
                                                    ORDER BY date DESC 
                                                    LIMIT 100
                                                ) AS subquery
                                                ORDER BY date ASC;");

                        $users_names = [];
                        // Execution de la requete
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
            </div>
            <form>
                <input name="message" id="msg" placeholder="Entrez votre message" maxlength="256" autofocus>
                <button class="btn-forum" id="btn-send" type="submit">Envoyer</button>
            </form>
        </div>
    </div>
</body>

</html>