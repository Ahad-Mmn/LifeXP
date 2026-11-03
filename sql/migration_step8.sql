CREATE TABLE badges (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code        VARCHAR(30)  NOT NULL,
    name        VARCHAR(50)  NOT NULL,
    description VARCHAR(150) NOT NULL,
    icon        VARCHAR(8)   NOT NULL DEFAULT '11088',
    PRIMARY KEY (id),
    UNIQUE KEY uq_badges_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_badges (
    user_id   INT UNSIGNED NOT NULL,
    badge_id  INT UNSIGNED NOT NULL,
    earned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, badge_id),
    CONSTRAINT fk_ub_user  FOREIGN KEY (user_id)  REFERENCES users (id)  ON DELETE CASCADE,
    CONSTRAINT fk_ub_badge FOREIGN KEY (badge_id) REFERENCES badges (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- icon = Unicode code point number, rendered in the template as &#NNNN;
INSERT INTO badges (code, name, description, icon) VALUES
('first_quest',       'First Blood',     'Complete your first quest',                  '9876'),
('ten_quests',        'Getting Serious', 'Complete 10 quests',                         '127919'),
('twentyfive_quests', 'Quest Machine',   'Complete 25 quests',                         '9889'),
('streak_3',          'On Fire',         'Reach a 3-day streak',                       '128293'),
('streak_7',          'Unstoppable',     'Reach a 7-day streak',                       '127775'),
('level_5',           'Adventurer',      'Reach level 5',                              '127942'),
('epic_slayer',       'Epic Slayer',     'Complete an epic quest',                     '128009'),
('all_rounder',       'All-Rounder',     'Complete quests in 4 different categories',  '127912');