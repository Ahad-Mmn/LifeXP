ALTER TABLE quests
    MODIFY COLUMN status ENUM('offered','active','completed','skipped','expired','declined') NOT NULL DEFAULT 'active',
    ADD COLUMN delivered_at DATETIME NULL,
    ADD COLUMN resolved_at DATETIME NULL,
    ADD KEY idx_quests_delivered (user_id, delivered_at);

ALTER TABLE users
    ADD COLUMN productivity_score TINYINT UNSIGNED NOT NULL DEFAULT 50;