CREATE TABLE usagers (
    id INTEGER PRIMARY KEY,
    name VARCHAR(32) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL UNIQUE
);

-- Tous les mots de passe sont 'password'
INSERT INTO usagers (id, name, password) VALUES (1, "Eric", "$2y$10$ZcmldKPHVjogeHgmvfvoPOJSkEyHHMYHOz/oX46H56yf9mgnqHYPm");
INSERT INTO usagers (id, name, password) VALUES (2, "Hanna", "$2y$10$Y.5j4QQDlFdupWHEi/M5p.luGM7v4exeJQW1F1IvbW549gthNfH2i");
INSERT INTO usagers (id, name, password) VALUES (3, "Beatriz", "$2y$10$qi20VB32kU6TQxwlXtnZ1.Rhv3GxBSVznc.zRttt79fLBVk8zqFA.");
INSERT INTO usagers (id, name, password) VALUES (4, "Ali", "$2y$10$H49T0MgBnaGao1XCbYLqC.q6s/0RXqyTDIpF4HqHuiBR9FJdI5U8O");
INSERT INTO usagers (id, name, password) VALUES (5, "Fatima", "$2y$10$BfdAYr/zNcYRilfvnrEkou3Iu6x5cPruu6aIjqq/ExSd2bQJJnsL6");