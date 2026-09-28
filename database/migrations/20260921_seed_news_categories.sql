-- Seed the default Embassy CMS news categories.
-- This is idempotent and safe to run more than once.
INSERT IGNORE INTO news_categories (name)
VALUES ('Embassy News'),
    ('Consular Services'),
    ('Diplomatic Relations'),
    ('Events'),
    ('Community'),
    ('Public Information');