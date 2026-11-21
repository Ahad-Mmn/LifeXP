ALTER TABLE users
    ADD COLUMN profile_visibility ENUM('public','private') NOT NULL DEFAULT 'public',
    ADD COLUMN show_on_leaderboards TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN show_badges TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN show_activity TINYINT(1) NOT NULL DEFAULT 1;