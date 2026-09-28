ALTER TABLE ambassador
ADD COLUMN IF NOT EXISTS name_fr VARCHAR(150) NULL
AFTER name,
    ADD COLUMN IF NOT EXISTS name_en VARCHAR(150) NULL
AFTER name_fr,
    ADD COLUMN IF NOT EXISTS position_fr VARCHAR(150) NULL
AFTER position,
    ADD COLUMN IF NOT EXISTS position_en VARCHAR(150) NULL
AFTER position_fr,
    ADD COLUMN IF NOT EXISTS biography_fr LONGTEXT NULL
AFTER biography,
    ADD COLUMN IF NOT EXISTS biography_en LONGTEXT NULL
AFTER biography_fr,
    ADD COLUMN IF NOT EXISTS welcome_message_fr TEXT NULL
AFTER welcome_message,
    ADD COLUMN IF NOT EXISTS welcome_message_en TEXT NULL
AFTER welcome_message_fr,
    ADD COLUMN IF NOT EXISTS signature_fr VARCHAR(150) NULL
AFTER signature,
    ADD COLUMN IF NOT EXISTS signature_en VARCHAR(150) NULL
AFTER signature_fr;