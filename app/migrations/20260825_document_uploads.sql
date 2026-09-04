-- Upgrade the initial documents table for PRD 5.
ALTER TABLE documents
  CHANGE original_name file_name VARCHAR(255) NULL,
  CHANGE storage_path file_path VARCHAR(500) NULL,
  CHANGE review_note admin_note TEXT NULL,
  ADD COLUMN file_type VARCHAR(100) NULL AFTER file_path,
  ADD COLUMN file_size INT NULL AFTER file_type,
  ADD COLUMN uploaded_at DATETIME NULL AFTER file_size,
  ADD COLUMN reviewed_at DATETIME NULL AFTER admin_note,
  ADD COLUMN reviewed_by INT NULL AFTER reviewed_at,
  MODIFY status ENUM('pending','uploaded','approved','rejected','resubmission_required') NOT NULL DEFAULT 'uploaded',
  ADD CONSTRAINT documents_reviewed_by_fk FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL;

UPDATE documents SET uploaded_at = created_at WHERE uploaded_at IS NULL AND file_path IS NOT NULL;
