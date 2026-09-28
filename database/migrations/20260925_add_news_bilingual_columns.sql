ALTER TABLE news
ADD COLUMN IF NOT EXISTS title_fr VARCHAR(255) NULL
AFTER title,
    ADD COLUMN IF NOT EXISTS title_en VARCHAR(255) NULL
AFTER title_fr,
    ADD COLUMN IF NOT EXISTS short_description_fr TEXT NULL
AFTER short_description,
    ADD COLUMN IF NOT EXISTS short_description_en TEXT NULL
AFTER short_description_fr,
    ADD COLUMN IF NOT EXISTS content_fr LONGTEXT NULL
AFTER content,
    ADD COLUMN IF NOT EXISTS content_en LONGTEXT NULL
AFTER content_fr;