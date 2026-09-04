-- SQL migrations for the Medcon Edu application.
-- Canonical database name: farmyksn_medcon
-- Select or create that database before running this file.

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('student','admin','agent','super_admin') NOT NULL DEFAULT 'student',
  first_name VARCHAR(120) NOT NULL,
  last_name VARCHAR(120) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  phone VARCHAR(50),
  country VARCHAR(120),
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','pending','suspended') NOT NULL DEFAULT 'active',
  email_verified TINYINT(1) DEFAULT 0,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  agent_id INT NULL,
  date_of_birth DATE NULL,
  gender VARCHAR(32) NULL,
  nationality VARCHAR(120) NULL,
  education_level VARCHAR(120) NULL,
  state VARCHAR(100) NULL,
  lga VARCHAR(120) NULL,
  address VARCHAR(500) NULL,
  heard_about VARCHAR(50) NULL,
  referral_code VARCHAR(100) NULL,
  parent_name VARCHAR(255) NULL,
  parent_phone VARCHAR(50) NULL,
  parent_email VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS agents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  organization_name VARCHAR(150),
  city VARCHAR(100),
  date_of_birth DATE NULL,
  gender VARCHAR(16) NULL,
  state VARCHAR(100) NULL,
  lga VARCHAR(120) NULL,
  address VARCHAR(500) NULL,
  heard_about VARCHAR(50) NULL,
  recruitment_experience TEXT,
  expected_student_volume VARCHAR(100),
  website_social_link VARCHAR(255),
  referral_code VARCHAR(100) NOT NULL UNIQUE,
  referral_slug VARCHAR(100) NOT NULL UNIQUE,
  commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  approval_status ENUM('pending','approved','rejected','suspended') NOT NULL DEFAULT 'pending',
  approved_by INT NULL,
  approved_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS colleges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS programmes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  college_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (college_id) REFERENCES colleges(id)
);

CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  agent_id INT NULL,
  college_id INT NULL,
  programme_id INT NULL,
  intended_intake VARCHAR(100),
  education_level VARCHAR(150),
  science_background TEXT,
  jamb_result VARCHAR(100),
  passport_status VARCHAR(100),
  budget_range VARCHAR(100),
  accommodation_preference VARCHAR(100),
  study_goal TEXT,
  message TEXT,
  initial_fee_paid TINYINT(1) NOT NULL DEFAULT 0,
  initial_fee_paid_at DATETIME NULL,
  status ENUM('draft','submitted','received','under_review','documents_pending','approved','rejected','admission_issued','visa_processing','enrolled') NOT NULL DEFAULT 'draft',
  submitted_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE SET NULL,
  FOREIGN KEY (programme_id) REFERENCES programmes(id) ON DELETE SET NULL,
  INDEX applications_student_status (student_id, status)
);

CREATE TABLE IF NOT EXISTS agent_commissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  agent_id INT NOT NULL,
  application_id INT NOT NULL UNIQUE,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  status ENUM('pending','received') NOT NULL DEFAULT 'pending',
  received_at DATETIME NULL,
  received_by INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE CASCADE,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX agent_commissions_status (agent_id, status)
);

CREATE TABLE IF NOT EXISTS documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  application_id INT NULL,
  document_type VARCHAR(150) NOT NULL,
  file_name VARCHAR(255) NULL,
  file_path VARCHAR(500) NULL,
  file_type VARCHAR(100) NULL,
  file_size INT NULL,
  status ENUM('pending','uploaded','approved','rejected','resubmission_required') NOT NULL DEFAULT 'uploaded',
  admin_note TEXT NULL,
  uploaded_at DATETIME NULL,
  reviewed_at DATETIME NULL,
  reviewed_by INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX documents_student_status (student_id, status)
);

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_user_id INT NULL,
  recipient_user_id INT NOT NULL,
  application_id INT NULL,
  subject VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  read_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (recipient_user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE SET NULL,
  INDEX messages_recipient_created (recipient_user_id, created_at)
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  scheduled_at DATETIME NULL,
  status ENUM('requested','confirmed','completed','cancelled') NOT NULL DEFAULT 'requested',
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  INDEX bookings_student_status (student_id, status)
);

INSERT IGNORE INTO colleges (name, slug) VALUES
  ('PLT College of Medicine', 'plt-college-of-medicine'),
  ('The Manila Times College School of Medicine', 'manila-times-college-school-of-medicine');

INSERT INTO programmes (college_id, name)
SELECT id, 'Doctor of Medicine' FROM colleges c
WHERE NOT EXISTS (SELECT 1 FROM programmes p WHERE p.college_id = c.id AND p.name = 'Doctor of Medicine');
