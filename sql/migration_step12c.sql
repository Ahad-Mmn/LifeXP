-- 1. The pool of ready-made quests 
CREATE TABLE quest_templates (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id INT UNSIGNED NOT NULL,
    track       VARCHAR(30)  NULL,
    quest_type  ENUM('main','side','bonus') NOT NULL,
    title       VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    why         VARCHAR(255) NOT NULL DEFAULT '',
    difficulty  ENUM('easy','medium','hard','epic') NOT NULL DEFAULT 'easy',
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_template_title (category_id, title),
    KEY idx_templates_pick (category_id, quest_type, is_active),
    CONSTRAINT fk_templates_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Every quest now has a type and an optional link to its template
ALTER TABLE quests
    ADD COLUMN quest_type ENUM('main','side','bonus','custom') NOT NULL DEFAULT 'custom' AFTER category_id,
    ADD COLUMN template_id INT UNSIGNED NULL AFTER quest_type,
    ADD KEY idx_quests_user_type (user_id, quest_type, status),
    ADD CONSTRAINT fk_quests_template FOREIGN KEY (template_id)
        REFERENCES quest_templates (id) ON DELETE SET NULL;