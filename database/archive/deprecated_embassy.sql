CREATE DATABASE IF NOT EXISTS embassy_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE embassy_cms;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'Administrator',
  status ENUM('active','inactive') DEFAULT 'active',
  last_login_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS homepage_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hero_title VARCHAR(255) NOT NULL,
  hero_subtitle TEXT NOT NULL,
  hero_image VARCHAR(255) DEFAULT NULL,
  welcome_message TEXT NOT NULL,
  mission TEXT NOT NULL,
  vision TEXT NOT NULL,
  objectives TEXT NOT NULL,
  featured_announcement TEXT DEFAULT NULL,
  homepage_buttons TEXT DEFAULT NULL,
  featured_services TEXT DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ambassador (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  position VARCHAR(150) NOT NULL,
  biography TEXT NOT NULL,
  welcome_message TEXT NOT NULL,
  signature VARCHAR(150) DEFAULT NULL,
  photo VARCHAR(255) DEFAULT NULL,
  published TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  short_description TEXT NOT NULL,
  content LONGTEXT NOT NULL,
  featured_image VARCHAR(255) DEFAULT NULL,
  author VARCHAR(150) NOT NULL,
  publication_date DATE NOT NULL,
  status ENUM('draft','published','archived') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(100) NOT NULL,
  priority VARCHAR(50) NOT NULL DEFAULT 'Normal',
  image VARCHAR(255) DEFAULT NULL,
  publish_date DATE NOT NULL,
  expiry_date DATE DEFAULT NULL,
  pin_to_homepage TINYINT(1) DEFAULT 0,
  status ENUM('draft','published','archived') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  requirements TEXT DEFAULT NULL,
  required_documents TEXT DEFAULT NULL,
  fees TEXT DEFAULT NULL,
  processing_time VARCHAR(100) DEFAULT NULL,
  office_hours VARCHAR(255) DEFAULT NULL,
  download_forms TEXT DEFAULT NULL,
  status ENUM('draft','published','archived') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS holidays (
  id INT AUTO_INCREMENT PRIMARY KEY,
  holiday_name VARCHAR(255) NOT NULL,
  holiday_date DATE NOT NULL,
  description TEXT DEFAULT NULL,
  status ENUM('draft','published','archived') DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  caption TEXT DEFAULT NULL,
  alt_text VARCHAR(255) DEFAULT NULL,
  image_path VARCHAR(255) NOT NULL,
  featured TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS emergency_contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hotline VARCHAR(100) DEFAULT NULL,
  phone_numbers TEXT DEFAULT NULL,
  whatsapp VARCHAR(100) DEFAULT NULL,
  embassy_email VARCHAR(150) DEFAULT NULL,
  duty_officer VARCHAR(150) DEFAULT NULL,
  google_maps TEXT DEFAULT NULL,
  office_hours VARCHAR(255) DEFAULT NULL,
  emergency_instructions TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  applicant_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  nationality VARCHAR(100) DEFAULT NULL,
  requested_service VARCHAR(255) NOT NULL,
  preferred_date DATE NOT NULL,
  preferred_time VARCHAR(50) NOT NULL,
  status ENUM('pending','approved','rejected','rescheduled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  subject VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('unread','read','archived') DEFAULT 'unread',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS website_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  embassy_name VARCHAR(255) NOT NULL,
  logo_path VARCHAR(255) DEFAULT NULL,
  favicon_path VARCHAR(255) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  phone_numbers TEXT DEFAULT NULL,
  emails TEXT DEFAULT NULL,
  google_maps TEXT DEFAULT NULL,
  working_hours TEXT DEFAULT NULL,
  facebook VARCHAR(255) DEFAULT NULL,
  instagram VARCHAR(255) DEFAULT NULL,
  twitter VARCHAR(255) DEFAULT NULL,
  linkedin VARCHAR(255) DEFAULT NULL,
  footer_info TEXT DEFAULT NULL,
  copyright VARCHAR(255) DEFAULT NULL,
  seo_title VARCHAR(255) DEFAULT NULL,
  seo_description TEXT DEFAULT NULL,
  google_analytics_code TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  action VARCHAR(255) NOT NULL,
  details TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS login_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  ip_address VARCHAR(100) DEFAULT NULL,
  user_agent TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (full_name, email, username, password_hash, role, status) VALUES
('System Administrator', 'admin@embassy.gov', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 'active');

INSERT INTO homepage_content (hero_title, hero_subtitle, welcome_message, mission, vision, objectives) VALUES
('Welcome to the Embassy of the Republic of Congo in Kenya', 'Serving citizens, partners and the diplomatic community with professionalism and care.', 'The embassy is committed to strengthening bilateral ties and serving the Congolese community in Kenya.', 'To represent the Republic of Congo with dignity, professionalism and dedication.', 'To build strong partnerships and provide trusted consular and diplomatic support.', 'To support citizens, strengthen diplomacy and promote cooperation across all sectors.');

INSERT INTO ambassador (name, position, biography, welcome_message, signature, published) VALUES
('H.E. Léon François Yendouma', 'Ambassador', 'Ambassador of the Republic of Congo to Kenya.', 'It is my pleasure to serve the people and strengthen relations between our nations.', 'Ambassador Léon François Yendouma', 1);

INSERT INTO website_settings (embassy_name, address, phone_numbers, emails, working_hours, footer_info, copyright, seo_title, seo_description) VALUES
('Embassy of the Republic of Congo in Kenya', 'United Crescent, Gigiri, Nairobi, Kenya', '+254 707 786 276', 'embacoken.diplomatic@gmail.com', 'Monday - Friday: 09:00 - 17:00', 'Official embassy website for information, services and public notices.', '© 2026 Embassy of the Republic of Congo in Kenya', 'Embassy of the Republic of Congo in Kenya', 'Official embassy website for news, services, announcements and public information.');
