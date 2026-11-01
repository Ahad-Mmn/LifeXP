-- LifeXP schema (core tables)

CREATE TABLE users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(30)  NOT NULL,
    email         VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    avatar        VARCHAR(255) NOT NULL DEFAULT 'default.png',
    total_xp      INT UNSIGNED NOT NULL DEFAULT 0,
    level         INT UNSIGNED NOT NULL DEFAULT 1,
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name  VARCHAR(50) NOT NULL,
    icon  VARCHAR(30) NOT NULL DEFAULT 'star',
    color CHAR(7)     NOT NULL DEFAULT '#6366f1',
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quests (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    title       VARCHAR(100) NOT NULL,
    description TEXT NULL,
    difficulty  ENUM('easy','medium','hard','epic') NOT NULL DEFAULT 'easy',
    xp_reward   SMALLINT UNSIGNED NOT NULL DEFAULT 10,
    due_date    DATE NULL,
    status      ENUM('active','completed') NOT NULL DEFAULT 'active',
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_quests_user_status (user_id, status),
    KEY idx_quests_category (category_id),
    CONSTRAINT fk_quests_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_quests_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quest_completions (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    quest_id     INT UNSIGNED NOT NULL,
    user_id      INT UNSIGNED NOT NULL,
    xp_earned    SMALLINT UNSIGNED NOT NULL,
    completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_completions_user_date (user_id, completed_at),
    CONSTRAINT fk_completions_quest FOREIGN KEY (quest_id)
        REFERENCES quests (id) ON DELETE CASCADE,
    CONSTRAINT fk_completions_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;