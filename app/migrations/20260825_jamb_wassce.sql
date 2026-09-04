ALTER TABLE applications ADD COLUMN jamb_result VARCHAR(100) NULL AFTER science_background;
UPDATE documents SET document_type='wassce_result' WHERE document_type='academic_result';
