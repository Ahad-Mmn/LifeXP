-- 1. Every XP change is recorded here (positive = earned, negative = penalty)
CREATE TABLE xp_ledger (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED NOT NULL,
    delta      INT NOT NULL,
    reason     VARCHAR(30) NOT NULL,
    quest_id   INT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ledger_user_date (user_id, created_at),
    CONSTRAINT fk_ledger_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. A completion remembers its own category, difficulty and quest type
ALTER TABLE quest_completions
    ADD COLUMN category_id INT UNSIGNED NULL,
    ADD COLUMN difficulty ENUM('easy','medium','hard','epic') NULL,
    ADD COLUMN quest_type ENUM('main','side','bonus','custom') NOT NULL DEFAULT 'custom',
    ADD KEY idx_completions_category (category_id, completed_at);

-- 3. Fill in the old rows from their quests
UPDATE quest_completions qc
JOIN quests q ON q.id = qc.quest_id
SET qc.category_id = q.category_id, qc.difficulty = q.difficulty;

-- 4. Deleting a quest no longer deletes its completion history
ALTER TABLE quest_completions DROP FOREIGN KEY fk_completions_quest;
ALTER TABLE quest_completions MODIFY quest_id INT UNSIGNED NULL;
ALTER TABLE quest_completions
    ADD CONSTRAINT fk_completions_quest FOREIGN KEY (quest_id)
    REFERENCES quests (id) ON DELETE SET NULL;

-- 5. Turn existing history into ledger rows
INSERT INTO xp_ledger (user_id, delta, reason, quest_id, created_at)
SELECT user_id, xp_earned, 'quest_complete', quest_id, completed_at
FROM quest_completions;
FROM quest_completions;