CREATE TABLE IF NOT EXISTS eligibility_assessments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  student_id INT NULL,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(60) NOT NULL,
  result_status ENUM('eligible','potential','not-eligible') NOT NULL,
  result_label VARCHAR(255) NOT NULL,
  result_summary TEXT NOT NULL,
  result_next_step TEXT NOT NULL,
  answers_json LONGTEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_eligibility_email_created (email,created_at),
  KEY idx_eligibility_user (user_id,created_at),
  CONSTRAINT fk_eligibility_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_eligibility_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS eligibility_account_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assessment_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_eligibility_account_token (token_hash),
  KEY idx_eligibility_account_assessment (assessment_id,created_at),
  CONSTRAINT fk_eligibility_account_assessment FOREIGN KEY (assessment_id) REFERENCES eligibility_assessments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

