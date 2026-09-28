ALTER TABLE homepage_content
ADD COLUMN IF NOT EXISTS hero_title_fr VARCHAR(255) NULL
AFTER hero_title,
    ADD COLUMN IF NOT EXISTS hero_title_en VARCHAR(255) NULL
AFTER hero_title_fr,
    ADD COLUMN IF NOT EXISTS hero_subtitle_fr TEXT NULL
AFTER hero_subtitle,
    ADD COLUMN IF NOT EXISTS hero_subtitle_en TEXT NULL
AFTER hero_subtitle_fr,
    ADD COLUMN IF NOT EXISTS welcome_message_fr TEXT NULL
AFTER welcome_message,
    ADD COLUMN IF NOT EXISTS welcome_message_en TEXT NULL
AFTER welcome_message_fr,
    ADD COLUMN IF NOT EXISTS mission_fr TEXT NULL
AFTER mission,
    ADD COLUMN IF NOT EXISTS mission_en TEXT NULL
AFTER mission_fr,
    ADD COLUMN IF NOT EXISTS vision_fr TEXT NULL
AFTER vision,
    ADD COLUMN IF NOT EXISTS vision_en TEXT NULL
AFTER vision_fr,
    ADD COLUMN IF NOT EXISTS objectives_fr TEXT NULL
AFTER objectives,
    ADD COLUMN IF NOT EXISTS objectives_en TEXT NULL
AFTER objectives_fr;