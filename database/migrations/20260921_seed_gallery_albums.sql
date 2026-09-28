-- Seed standard Embassy CMS gallery albums.
-- This is idempotent and safe to run more than once.
INSERT IGNORE INTO gallery_albums (name, description)
VALUES (
        'Embassy & Chancery',
        'Official embassy premises and administrative spaces.'
    ),
    (
        'Diplomatic Events',
        'Meetings, ceremonies, and diplomatic activities.'
    ),
    (
        'Consular Services',
        'Consular support and service delivery moments.'
    ),
    (
        'Official Meetings',
        'High-level meetings and bilateral engagements.'
    ),
    (
        'National Celebrations',
        'National holidays and public commemorations.'
    ),
    (
        'Community & Diaspora',
        'Community engagement and diaspora events.'
    );