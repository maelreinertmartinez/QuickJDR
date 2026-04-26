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
    health integer NOT NULL,
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
