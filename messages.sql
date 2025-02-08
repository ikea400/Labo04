-- Sélectionner la base de données
USE tch056_labo_forum;

-- Créer une table qui contient les utilisateurs
CREATE TABLE messages (
    user_id INT UNSIGNED REFERENCES usagers(id),
    date DATETIME NOT NULL,
    message VARCHAR(255) NOT NULL
);

-- Populer la base de données
INSERT INTO messages (user_id, date, message) VALUES (1, NOW(), 'Message de Eric');
INSERT INTO messages (user_id, date, message) VALUES (2, NOW(), 'Message de Hanna');
INSERT INTO messages (user_id, date, message) VALUES (3, NOW(), 'Message de Beatriz');
INSERT INTO messages (user_id, date, message) VALUES (4, NOW(), 'Message de Ali');
INSERT INTO messages (user_id, date, message) VALUES (5, NOW(), 'Message de Fatima');

-- Afficher le contenu
SELECT * FROM messages;