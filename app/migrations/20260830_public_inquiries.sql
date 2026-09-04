CREATE TABLE IF NOT EXISTS inquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  source_page VARCHAR(40) NOT NULL,
  person_type ENUM('student','parent','agent') NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(60) NOT NULL,
  country VARCHAR(120) NOT NULL,
  education_level VARCHAR(120) NOT NULL,
  preferred_programme VARCHAR(160) NULL,
  preferred_college VARCHAR(180) NULL,
  intended_intake ENUM('September 2026','September 2027') NOT NULL,
  message TEXT NULL,
  submission_channel ENUM('form','whatsapp') NOT NULL DEFAULT 'form',
  status ENUM('not_contacted','contacted','not_qualified','qualified','applied') NOT NULL DEFAULT 'not_contacted',
  consent_at DATETIME NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_inquiries_status_created (status,created_at),
  KEY idx_inquiries_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

