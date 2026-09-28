-- Non-destructive migration for the CMS “À Propos” section.
CREATE TABLE IF NOT EXISTS about_pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) NOT NULL UNIQUE,
  title_fr VARCHAR(255) NOT NULL,
  title_en VARCHAR(255) NOT NULL,
  content_fr LONGTEXT DEFAULT NULL,
  content_en LONGTEXT DEFAULT NULL,
  hero_image VARCHAR(255) DEFAULT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_about_pages_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO about_pages (slug, title_fr, title_en, content_fr, content_en, status)
VALUES
('about-congo', 'À propos du Congo', 'About Congo', '<p>Ce contenu est un espace réservé éditable depuis le CMS.</p>', '<p>This is editable placeholder content managed in the CMS.</p>', 'draft'),
('about-embassy', 'À propos de l’Ambassade', 'About the Embassy', '<p>Ce contenu est un espace réservé éditable depuis le CMS.</p>', '<p>This is editable placeholder content managed in the CMS.</p>', 'draft'),
('invest-in-congo', 'Investir au Congo', 'Invest in Congo', '<p>Ce contenu est un espace réservé éditable depuis le CMS.</p>', '<p>This is editable placeholder content managed in the CMS.</p>', 'draft')
ON DUPLICATE KEY UPDATE slug = VALUES(slug);
