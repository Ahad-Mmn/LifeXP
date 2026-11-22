ALTER TABLE users
    ADD COLUMN terms_accepted_at TIMESTAMP NULL,
    ADD COLUMN terms_version VARCHAR(10) NULL;