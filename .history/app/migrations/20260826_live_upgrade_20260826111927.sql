-- Medcon Edu: upgrade an existing installation to the current schema.
-- Select the live Medcon database before running this file.
-- Back up the database first. This script does not drop tables or delete rows.

DELIMITER $$

DROP PROCEDURE IF EXISTS medcon_upgrade_existing_installation$$

CREATE PROCEDURE medcon_upgrade_existing_installation()
BEGIN
  DECLARE table_count INT DEFAULT 0;
  DECLARE column_count INT DEFAULT 0;
  DECLARE foreign_key_count INT DEFAULT 0;

  SELECT COUNT(*) INTO table_count
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents';

  IF table_count = 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'The selected database has no documents table. Import app/migrations.sql first.';
  END IF;

  -- Rename legacy document columns only when the current names are absent.
  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'original_name';

  IF column_count > 0 THENLe
    SELECT COUNT(*) INTO column_count
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'documents'
      AND COLUMN_NAME = 'file_name';

    IF column_count = 0 THEN
      ALTER TABLE documents CHANGE original_name file_name VARCHAR(255) NULL;
    END IF;
  END IF;

  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'storage_path';

  IF column_count > 0 THEN
    SELECT COUNT(*) INTO column_count
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'documents'
      AND COLUMN_NAME = 'file_path';

    IF column_count = 0 THEN
      ALTER TABLE documents CHANGE storage_path file_path VARCHAR(500) NULL;
    END IF;
  END IF;

  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'review_note';

  IF column_count > 0 THEN
    SELECT COUNT(*) INTO column_count
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'documents'
      AND COLUMN_NAME = 'admin_note';

    IF column_count = 0 THEN
      ALTER TABLE documents CHANGE review_note admin_note TEXT NULL;
    END IF;
  END IF;

  -- Add document metadata fields that are missing from the older schema.
  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'file_type';
  IF column_count = 0 THEN
    ALTER TABLE documents ADD COLUMN file_type VARCHAR(100) NULL AFTER file_path;
  END IF;

  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'file_size';
  IF column_count = 0 THEN
    ALTER TABLE documents ADD COLUMN file_size INT NULL AFTER file_type;
  END IF;

  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'uploaded_at';
  IF column_count = 0 THEN
    ALTER TABLE documents ADD COLUMN uploaded_at DATETIME NULL AFTER file_size;
  END IF;

  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'reviewed_at';
  IF column_count = 0 THEN
    ALTER TABLE documents ADD COLUMN reviewed_at DATETIME NULL AFTER admin_note;
  END IF;

  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'reviewed_by';
  IF column_count = 0 THEN
    ALTER TABLE documents ADD COLUMN reviewed_by INT NULL AFTER reviewed_at;
  END IF;

  -- Normalize the status values used by the current document workflow.
  ALTER TABLE documents
    MODIFY status ENUM('pending','uploaded','approved','rejected','resubmission_required')
      NOT NULL DEFAULT 'uploaded';

  SELECT COUNT(*) INTO foreign_key_count
  FROM information_schema.KEY_COLUMN_USAGE
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'documents'
    AND COLUMN_NAME = 'reviewed_by'
    AND REFERENCED_TABLE_NAME = 'users'
    AND REFERENCED_COLUMN_NAME = 'id';

  IF foreign_key_count = 0 THEN
    ALTER TABLE documents
      ADD CONSTRAINT documents_reviewed_by_fk
      FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL;
  END IF;

  UPDATE documents
  SET uploaded_at = created_at
  WHERE uploaded_at IS NULL
    AND file_path IS NOT NULL;

  -- Add the JAMB field when it is missing.
  SELECT COUNT(*) INTO column_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'applications'
    AND COLUMN_NAME = 'jamb_result';

  IF column_count = 0 THEN
    ALTER TABLE applications ADD COLUMN jamb_result VARCHAR(100) NULL AFTER science_background;
  END IF;

  UPDATE documents
  SET document_type = 'wassce_result'
  WHERE document_type = 'academic_result';

  INSERT IGNORE INTO colleges (name, slug) VALUES
    ('PLT College of Medicine', 'plt-college-of-medicine'),
    ('The Manila Times College School of Medicine', 'manila-times-college-school-of-medicine');

  INSERT INTO programmes (college_id, name)
  SELECT id, 'Doctor of Medicine'
  FROM colleges c
  WHERE NOT EXISTS (
    SELECT 1
    FROM programmes p
    WHERE p.college_id = c.id
      AND p.name = 'Doctor of Medicine'
  );
END$$

CALL medcon_upgrade_existing_installation()$$
DROP PROCEDURE medcon_upgrade_existing_installation$$

DELIMITER ;
