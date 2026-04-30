CREATE TABLE users (
    id bigint GENERATED ALWAYS AS IDENTITY,
    username VARCHAR(50) NOT NULL,
    password varchar(72) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE roles (
    id bigint GENERATED ALWAYS AS IDENTITY,
    label VARCHAR(50) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE characters (
    id bigint GENERATED ALWAYS AS IDENTITY,
    user_id bigint NOT NULL,
    party_id bigint NOT NULL,
    name VARCHAR(50) NOT NULL,
    health integer DEFAULT 100,
    max_health integer NOT NULL,
    armor integer DEFAULT 0,
    max_armor integer NOT NULL,
    mana integer DEFAULT 0,
    max_mana integer NOT NULL,
    level integer DEFAULT 1,
    experience integer DEFAULT 0,
    PRIMARY KEY (id)
);

CREATE INDEX ON characters (user_id);

CREATE INDEX ON characters (party_id);

CREATE TABLE skills (
    id bigint GENERATED ALWAYS AS IDENTITY,
    label varchar NOT NULL,
    description varchar NOT NULL,
    mana_cost integer NOT NULL,
    healing integer,
    damage integer,
    dice_cost integer NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE character_skills (
    id bigint GENERATED ALWAYS AS IDENTITY,
    character_id bigint NOT NULL,
    skill_id bigint NOT NULL,
    level integer DEFAULT 1,
    experience integer DEFAULT 0,
    PRIMARY KEY (id)
);

CREATE TABLE user_role (
    user_id bigint NOT NULL,
    role_id bigint NOT NULL,
    PRIMARY KEY (user_id, role_id)
);

CREATE TABLE dice (
    id bigint GENERATED ALWAYS AS IDENTITY,
    character_id integer,
    value integer NOT NULL,
    max_value integer NOT NULL,
    launched_at timestamptz NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
);

CREATE INDEX ON dice (character_id);

CREATE TABLE party (
    id bigint GENERATED ALWAYS AS IDENTITY,
    mj_id bigint NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE session (
    id bigint GENERATED ALWAYS AS IDENTITY,
    user_id bigint NOT NULL,
    token varchar(64) NOT NULL,
    started_at timestamptz NOT NULL DEFAULT now(),
    ended_at timestamptz,
    PRIMARY KEY (id)
);

CREATE INDEX ON party (mj_id);

INSERT INTO roles (label) VALUES ('game_master');

-- ============================================================
-- DONNÉES DE TEST
-- ============================================================

-- 10 Users
INSERT INTO users (id, username, password) OVERRIDING SYSTEM VALUE VALUES
(1,  'alice',   'hashed_password_alice'),
(2,  'bob',     'hashed_password_bob'),
(3,  'charlie', 'hashed_password_charlie'),
(4,  'diana',   'hashed_password_diana'),
(5,  'evan',    'hashed_password_evan'),
(6,  'fiona',   'hashed_password_fiona'),
(7,  'george',  'hashed_password_george'),
(8,  'helen',   'hashed_password_helen'),
(9,  'ivan',    'hashed_password_ivan'),
(10, 'julia',   'hashed_password_julia');

-- 5 Parties (mj_id = users 1 à 5, chaque MJ dirige une partie)
INSERT INTO party (id, mj_id) OVERRIDING SYSTEM VALUE VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5);

-- Rôle game_master (id=1 déjà inséré) assigné aux users 1 à 5
INSERT INTO user_role (user_id, role_id) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1);

-- 20 Skills (4 par classe : Guerrier, Mage, Archer, Prêtre, Voleur)
INSERT INTO skills (id, label, description, mana_cost, healing, damage, dice_cost) OVERRIDING SYSTEM VALUE VALUES
-- Guerrier
(1,  'Coup de bouclier', 'Frappe l''ennemi avec son bouclier',        0,  NULL, 15, 75),
(2,  'Frappe puissante', 'Attaque lourde infligeant de gros dégâts',  5,  NULL, 30, 50),
(3,  'Cri de guerre',    'Augmente la défense temporairement',        10, NULL, 0,  65),
(4,  'Résistance',       'Récupère un peu de vie',                    15, 20,   0,  60),
-- Mage
(5,  'Boule de feu',     'Lance une boule de feu sur l''ennemi',      20, NULL, 40, 55),
(6,  'Éclair',           'Foudroie l''ennemi',                        15, NULL, 25, 65),
(7,  'Soin magique',     'Soigne le personnage',                      25, 35,   0,  60),
(8,  'Bouclier magique', 'Crée un bouclier d''énergie',               30, NULL, 0,  45),
-- Archer
(9,  'Tir précis',       'Tir précis sur la cible',                   0,  NULL, 20, 70),
(10, 'Pluie de flèches', 'Lance plusieurs flèches à la fois',         15, NULL, 35, 45),
(11, 'Piège',            'Pose un piège au sol',                      10, NULL, 15, 60),
(12, 'Esquive',          'Évite la prochaine attaque',                5,  NULL, 0,  65),
-- Prêtre
(13, 'Bénédiction',      'Bénit un allié pour augmenter ses stats',   20, NULL, 0,  65),
(14, 'Soin sacré',       'Soigne massivement un allié',               30, 50,   0,  50),
(15, 'Lumière divine',   'Inflige des dégâts de lumière',             25, NULL, 30, 55),
(16, 'Résurrection',     'Ressuscite un allié tombé au combat',       50, 70,   0,  20),
-- Voleur
(17, 'Coup sournois',    'Attaque dans le dos pour plus de dégâts',   0,  NULL, 25, 60),
(18, 'Évasion',          'Se téléporte derrière l''ennemi',           10, NULL, 0,  70),
(19, 'Poison',           'Empoisonne l''ennemi sur la durée',         15, NULL, 10, 65),
(20, 'Vol',              'Vole un objet à l''ennemi',                  5, NULL, 0,  35);

-- 25 Characters (5 par partie, user_id cyclé de 1 à 10)
INSERT INTO characters (id, user_id, party_id, name, health, max_health, armor, max_armor, mana, max_mana, level, experience) OVERRIDING SYSTEM VALUE VALUES
-- Partie 1
(1,  1,  1, 'Thorin',   100, 120, 20, 50, 30,  80,  3, 250),
(2,  2,  1, 'Lyria',    80,  100, 10, 30, 90,  150, 4, 400),
(3,  3,  1, 'Kael',     90,  110, 15, 40, 50,  100, 2, 100),
(4,  4,  1, 'Sera',     70,  90,  5,  20, 120, 180, 5, 600),
(5,  5,  1, 'Drak',     110, 130, 25, 60, 20,  50,  3, 300),
-- Partie 2
(6,  6,  2, 'Eira',     80,  100, 10, 30, 100, 160, 4, 450),
(7,  7,  2, 'Moren',    100, 120, 20, 50, 30,  80,  3, 270),
(8,  8,  2, 'Sylva',    70,  90,  5,  20, 130, 190, 6, 700),
(9,  9,  2, 'Gareth',   110, 130, 30, 65, 10,  40,  2, 150),
(10, 10, 2, 'Niara',    85,  105, 12, 35, 70,  120, 3, 310),
-- Partie 3
(11, 1,  3, 'Aldric',   105, 125, 22, 55, 25,  70,  3, 260),
(12, 2,  3, 'Vex',      75,  95,  8,  25, 110, 170, 5, 550),
(13, 3,  3, 'Brynn',    95,  115, 18, 45, 45,  95,  2, 110),
(14, 4,  3, 'Zara',     65,  85,  3,  15, 140, 200, 6, 800),
(15, 5,  3, 'Orin',     115, 135, 28, 62, 15,  45,  4, 420),
-- Partie 4
(16, 6,  4, 'Tessa',    80,  100, 10, 30, 95,  155, 3, 280),
(17, 7,  4, 'Korrak',   100, 120, 22, 52, 28,  75,  4, 380),
(18, 8,  4, 'Luna',     72,  92,  6,  22, 125, 185, 5, 520),
(19, 9,  4, 'Bane',     112, 132, 28, 63, 8,   35,  2, 130),
(20, 10, 4, 'Isolde',   88,  108, 14, 38, 65,  115, 3, 290),
-- Partie 5
(21, 1,  5, 'Fenris',   102, 122, 21, 51, 28,  78,  3, 255),
(22, 2,  5, 'Cassiel',  78,  98,  9,  28, 105, 165, 4, 430),
(23, 3,  5, 'Ryker',    92,  112, 16, 42, 48,  98,  2, 105),
(24, 4,  5, 'Aelindra', 68,  88,  4,  18, 135, 195, 6, 750),
(25, 5,  5, 'Grond',    118, 138, 27, 61, 12,  42,  3, 320);

-- 100 character_skills (4 skills par character, classe cyclée : Guerrier→Mage→Archer→Prêtre→Voleur)
INSERT INTO character_skills (id, character_id, skill_id, level, experience) OVERRIDING SYSTEM VALUE VALUES
-- Character 1 : Guerrier (skills 1-4)
(1,  1,  1, 2, 150), (2,  1,  2, 1, 80),  (3,  1,  3, 2, 120), (4,  1,  4, 1, 60),
-- Character 2 : Mage (skills 5-8)
(5,  2,  5, 3, 300), (6,  2,  6, 2, 180), (7,  2,  7, 3, 250), (8,  2,  8, 1, 90),
-- Character 3 : Archer (skills 9-12)
(9,  3,  9, 2, 160), (10, 3,  10, 1, 70), (11, 3,  11, 2, 130),(12, 3,  12, 1, 55),
-- Character 4 : Prêtre (skills 13-16)
(13, 4,  13, 4, 400),(14, 4,  14, 3, 280),(15, 4,  15, 3, 260),(16, 4,  16, 2, 200),
-- Character 5 : Voleur (skills 17-20)
(17, 5,  17, 2, 170),(18, 5,  18, 2, 140),(19, 5,  19, 1, 85), (20, 5,  20, 1, 65),
-- Character 6 : Guerrier
(21, 6,  1, 2, 140),(22, 6,  2, 1, 75),  (23, 6,  3, 1, 95),  (24, 6,  4, 2, 110),
-- Character 7 : Mage
(25, 7,  5, 3, 320),(26, 7,  6, 2, 190), (27, 7,  7, 2, 210), (28, 7,  8, 1, 80),
-- Character 8 : Archer
(29, 8,  9, 3, 350),(30, 8,  10, 2, 200),(31, 8,  11, 3, 280),(32, 8,  12, 2, 160),
-- Character 9 : Prêtre
(33, 9,  13, 1, 100),(34, 9,  14, 1, 80),(35, 9,  15, 1, 70),(36, 9,  16, 1, 50),
-- Character 10 : Voleur
(37, 10, 17, 2, 180),(38, 10, 18, 1, 90),(39, 10, 19, 2, 150),(40, 10, 20, 1, 70),
-- Character 11 : Guerrier
(41, 11, 1, 2, 155),(42, 11, 2, 2, 130),(43, 11, 3, 1, 85),  (44, 11, 4, 1, 65),
-- Character 12 : Mage
(45, 12, 5, 4, 420),(46, 12, 6, 3, 260),(47, 12, 7, 3, 290), (48, 12, 8, 2, 180),
-- Character 13 : Archer
(49, 13, 9, 1, 60),(50, 13, 10, 1, 55),(51, 13, 11, 1, 50),  (52, 13, 12, 1, 45),
-- Character 14 : Prêtre
(53, 14, 13, 5, 550),(54, 14, 14, 4, 380),(55, 14, 15, 4, 360),(56, 14, 16, 3, 270),
-- Character 15 : Voleur
(57, 15, 17, 3, 250),(58, 15, 18, 2, 160),(59, 15, 19, 3, 220),(60, 15, 20, 2, 130),
-- Character 16 : Guerrier
(61, 16, 1, 2, 145),(62, 16, 2, 1, 85),(63, 16, 3, 2, 115),  (64, 16, 4, 1, 55),
-- Character 17 : Mage
(65, 17, 5, 3, 310),(66, 17, 6, 2, 195),(67, 17, 7, 3, 265), (68, 17, 8, 2, 170),
-- Character 18 : Archer
(69, 18, 9, 4, 380),(70, 18, 10, 3, 240),(71, 18, 11, 3, 260),(72, 18, 12, 2, 150),
-- Character 19 : Prêtre
(73, 19, 13, 1, 90),(74, 19, 14, 1, 75),(75, 19, 15, 1, 65),(76, 19, 16, 1, 40),
-- Character 20 : Voleur
(77, 20, 17, 2, 175),(78, 20, 18, 2, 145),(79, 20, 19, 1, 80),(80, 20, 20, 1, 60),
-- Character 21 : Guerrier
(81, 21, 1, 2, 150),(82, 21, 2, 1, 80),(83, 21, 3, 2, 120),  (84, 21, 4, 1, 60),
-- Character 22 : Mage
(85, 22, 5, 3, 305),(86, 22, 6, 2, 185),(87, 22, 7, 3, 255), (88, 22, 8, 1, 95),
-- Character 23 : Archer
(89, 23, 9, 1, 65),(90, 23, 10, 1, 60),(91, 23, 11, 1, 55),  (92, 23, 12, 1, 50),
-- Character 24 : Prêtre
(93, 24, 13, 5, 580),(94, 24, 14, 4, 400),(95, 24, 15, 4, 370),(96, 24, 16, 3, 290),
-- Character 25 : Voleur
(97, 25, 17, 2, 190),(98, 25, 18, 2, 155),(99, 25, 19, 2, 170),(100, 25, 20, 1, 75);