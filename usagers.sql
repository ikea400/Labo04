-- Sélectionner la base de données
USE tch056_labo_forum;

-- Créer une table qui contient les utilisateurs
CREATE TABLE usagers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL UNIQUE
);

-- Populer la base de données
-- Tous les mots de passe sont 'Password9'
INSERT INTO usagers (name, password) VALUES ("Eric", "$2y$10$3Z4Y0ZNUAmbTReEcFlFTO.gFOs5XtB2CXhzkMphTgzYmAusaNzLsa");
INSERT INTO usagers (name, password) VALUES ("Hanna", "$2y$10$pUq794J/A1qPup5vtJjmgek0mvX2Tpc3nsBL2t6ruboAV6sbGUQRy");
INSERT INTO usagers (name, password) VALUES ("Beatriz", "$2y$10$jMO/gX5VaUuP43j9.OG2AeMQmBexw/aaktaxo6kKdh.b7DEWo1nbG");
INSERT INTO usagers (name, password) VALUES ("Ali", "$2y$10$DNx0iWpHbD2qjSFW5UgF7ul3UEPuxVYTZ1XB24FvGraWEkOzAeEPe");
INSERT INTO usagers (name, password) VALUES ("Fatima", "$2y$10$iASi5Y7kuLWa7PM6qOT60efvw74gkR6N9QepCZ4wgi4ntkoySUn26");

-- Afficher le contenu
SELECT * FROM usagers;