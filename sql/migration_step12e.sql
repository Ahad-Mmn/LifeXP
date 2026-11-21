CREATE TABLE user_interests (
    user_id     INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, category_id),
    CONSTRAINT fk_interests_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_interests_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_interest_templates (
    user_id     INT UNSIGNED NOT NULL,
    template_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (user_id, template_id),
    CONSTRAINT fk_interest_tpl_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_interest_tpl_template FOREIGN KEY (template_id)
        REFERENCES quest_templates (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE users
    ADD COLUMN onboarded TINYINT(1) NOT NULL DEFAULT 0;