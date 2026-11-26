ALTER TABLE quest_completions
    ADD COLUMN track VARCHAR(30) NULL;

UPDATE quest_completions qc
JOIN quests q ON q.id = qc.quest_id
JOIN quest_templates t ON t.id = q.template_id
SET qc.track = t.track
WHERE qc.track IS NULL;

ALTER TABLE badges
    ADD COLUMN category_id INT UNSIGNED NULL,
    ADD COLUMN track VARCHAR(30) NULL,
    ADD COLUMN metric ENUM('category_xp','track_xp','completed','main_completed','epic_completed','categories','streak','level') NULL,
    ADD COLUMN threshold INT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN tier TINYINT UNSIGNED NOT NULL DEFAULT 1,
    ADD COLUMN sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ADD KEY idx_badges_category (category_id),
    ADD CONSTRAINT fk_badges_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON DELETE SET NULL;

UPDATE badges SET metric = 'completed',      threshold = 1,  tier = 1, sort_order = 10 WHERE code = 'first_quest';
UPDATE badges SET metric = 'completed',      threshold = 10, tier = 1, sort_order = 20 WHERE code = 'ten_quests';
UPDATE badges SET metric = 'completed',      threshold = 25, tier = 2, sort_order = 30 WHERE code = 'twentyfive_quests';
UPDATE badges SET metric = 'epic_completed', threshold = 1,  tier = 2, sort_order = 70 WHERE code = 'epic_slayer';
UPDATE badges SET metric = 'categories',     threshold = 4,  tier = 1, sort_order = 80 WHERE code = 'all_rounder';
UPDATE badges SET metric = 'streak',         threshold = 3,  tier = 1, sort_order = 90 WHERE code = 'streak_3';
UPDATE badges SET metric = 'streak',         threshold = 7,  tier = 2, sort_order = 92 WHERE code = 'streak_7';
UPDATE badges SET metric = 'level',          threshold = 5,  tier = 1, sort_order = 100 WHERE code = 'level_5';

INSERT IGNORE INTO badges (code, name, description, icon, category_id, track, metric, threshold, tier, sort_order) VALUES
('completed_100',  'Centurion',            'Complete 100 quests',                          '127941', NULL, NULL, 'completed',      100, 3, 40),
('completed_500',  'Quest Legend',         'Complete 500 quests',                          '128081', NULL, NULL, 'completed',      500, 4, 50),
('main_1',         'First Campaign',       'Complete a main quest',                        '127984', NULL, NULL, 'main_completed', 1,   1, 60),
('main_5',         'Campaigner',           'Complete 5 main quests',                       '129517', NULL, NULL, 'main_completed', 5,   2, 62),
('main_25',        'Saga Complete',        'Complete 25 main quests',                      '128681', NULL, NULL, 'main_completed', 25,  3, 64),
('epic_10',        'Epic Collector',       'Complete 10 epic quests',                      '127755', NULL, NULL, 'epic_completed', 10,  3, 72),
('categories_8',   'Renaissance Soul',     'Complete quests in 8 different categories',    '127752', NULL, NULL, 'categories',     8,   2, 82),
('categories_12',  'Master of Everything', 'Complete quests in all 12 categories',         '127878', NULL, NULL, 'categories',     12,  3, 84),
('streak_14',      'Blazing Trail',        'Reach a 14-day streak',                        '128165', NULL, NULL, 'streak',         14,  2, 94),
('streak_30',      'Unbreakable',          'Reach a 30-day streak',                        '128640', NULL, NULL, 'streak',         30,  3, 96),
('streak_100',     'Eternal Flame',        'Reach a 100-day streak',                       '127776', NULL, NULL, 'streak',         100, 4, 98),
('level_10',       'Veteran',              'Reach level 10',                               '128142', NULL, NULL, 'level',          10,  2, 102),
('level_20',       'Grandmaster',          'Reach level 20',                               '128305', NULL, NULL, 'level',          20,  3, 104);