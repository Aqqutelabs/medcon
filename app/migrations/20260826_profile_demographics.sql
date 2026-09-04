-- Add demographic and acquisition fields without removing existing data.
CREATE TABLE IF NOT EXISTS agents (
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, organization_name VARCHAR(150), city VARCHAR(100),
  recruitment_experience TEXT, expected_student_volume VARCHAR(100), website_social_link VARCHAR(255),
  referral_code VARCHAR(100) NOT NULL UNIQUE, referral_slug VARCHAR(100) NOT NULL UNIQUE,
  commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  approval_status ENUM('pending','approved','rejected','suspended') NOT NULL DEFAULT 'pending',
  approved_by INT NULL, approved_at DATETIME NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

ALTER TABLE students
  ADD COLUMN IF NOT EXISTS state VARCHAR(100) NULL AFTER education_level,
  ADD COLUMN IF NOT EXISTS lga VARCHAR(120) NULL AFTER state,
  ADD COLUMN IF NOT EXISTS address VARCHAR(500) NULL AFTER lga,
  ADD COLUMN IF NOT EXISTS heard_about VARCHAR(50) NULL AFTER address,
  ADD COLUMN IF NOT EXISTS referral_code VARCHAR(100) NULL AFTER heard_about,
  ADD COLUMN IF NOT EXISTS agent_id INT NULL AFTER user_id;

ALTER TABLE agents
  ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL AFTER user_id,
  ADD COLUMN IF NOT EXISTS gender VARCHAR(16) NULL AFTER date_of_birth,
  ADD COLUMN IF NOT EXISTS state VARCHAR(100) NULL AFTER city,
  ADD COLUMN IF NOT EXISTS lga VARCHAR(120) NULL AFTER state,
  ADD COLUMN IF NOT EXISTS address VARCHAR(500) NULL AFTER lga,
  ADD COLUMN IF NOT EXISTS heard_about VARCHAR(50) NULL AFTER address;

UPDATE agents SET lga=city WHERE lga IS NULL AND city IS NOT NULL AND city<>'';

CREATE TABLE IF NOT EXISTS agent_commissions (
  id INT AUTO_INCREMENT PRIMARY KEY, agent_id INT NOT NULL, application_id INT NOT NULL UNIQUE,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0.00, status ENUM('pending','received') NOT NULL DEFAULT 'pending',
  received_at DATETIME NULL, received_by INT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL
);
