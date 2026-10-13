-- =====================================================================
-- Database: mathlab
-- Project: MathLab - dynamic course support website (PHP + MySQL)
--
-- How to use: import this file once (phpMyAdmin -> Import). It creates the
-- database, the tables and some demo data. Re-importing it RESETS the data.
--
-- The PHP scripts connect with the credentials in includes/config.php
-- (default: user root, no password, as in XAMPP). Those database
-- credentials are unrelated to the tutor/student accounts of the website,
-- which are stored in the `users` table below.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS mathlab
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mathlab;

-- ---------------------------------------------------------------------
-- Table: users
-- Website accounts (tutors and students). `loginname` is the user's
-- e-mail address and must be unique. Passwords are stored hashed
-- (bcrypt, via PHP's password_hash), never as plain text.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name    VARCHAR(100)        NOT NULL,
  last_name     VARCHAR(100)        NOT NULL,
  loginname     VARCHAR(190)        NOT NULL,
  password      VARCHAR(255)        NOT NULL,
  role          ENUM('tutor','student') NOT NULL DEFAULT 'student',
  created_at    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_loginname (loginname)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: announcements
-- Course announcements. When a new assignment is created (see
-- homework_form.php) a row is added here automatically.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS announcements;
CREATE TABLE announcements (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  announcement_date   DATE          NOT NULL,
  subject             VARCHAR(255)  NOT NULL,
  body                TEXT          NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: documents
-- Course documents/notes available for download. `file_path` is relative
-- to the project folder (e.g. files/file1.doc).
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS documents;
CREATE TABLE documents (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title         VARCHAR(255)  NOT NULL,
  description   TEXT          NULL,
  file_path     VARCHAR(255)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: homework
-- Course assignments. Objectives and deliverables are stored as text with
-- one item per line and are displayed as lists.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS homework;
CREATE TABLE homework (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  objectives        TEXT          NOT NULL,
  assignment_file   VARCHAR(255)  NOT NULL,
  deliverables      TEXT          NOT NULL,
  due_date          DATE          NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- Demo data (seed data)
-- =====================================================================

-- Users: 1 tutor + 2 students
-- Demo passwords (before hashing):
--   tsitlakidis@mathlab.tutor.gr / tutor123
--   student1@mathlab.student.gr / student123
--   student2@mathlab.student.gr / student456
INSERT INTO users (first_name, last_name, loginname, password, role) VALUES
('Γιώργος', 'Τσιτλακίδης', 'tsitlakidis@mathlab.tutor.gr',
  '$2y$12$Zfi7cvVl8EjBwVECX5hpreWEdOcKUGfSgS80bwOljgV5zCBfdUFLe', 'tutor'),
('Μαρία', 'Παπαδοπούλου', 'student1@mathlab.student.gr',
  '$2y$12$9x.A4.2/3pqpZDOsp53zye5gUQLUVtqIdLGqq1YIrrhgf7Bs2UHSG', 'student'),
('Νίκος', 'Ιωαννίδης', 'student2@mathlab.student.gr',
  '$2y$12$9FGUh45e8apML69vehs5y.pWW./ytpJvAUD2w4dCLfEODq8alxClq', 'student');

-- Announcements (the Greek text is website content shown to users)
INSERT INTO announcements (announcement_date, subject, body) VALUES
('2026-09-10', 'Έναρξη μαθημάτων', 'Τα μαθήματα αρχίζουν τη Δευτέρα 10/09/2026.'),
('2026-09-15', 'Ανάρτηση εργασίας', 'Η 1η εργασία έχει ανακοινωθεί στην ιστοσελίδα «Εργασίες». Η προθεσμία παράδοσης λήγει την Παρασκευή 22/12/2026.');

-- Course documents
INSERT INTO documents (title, description, file_path) VALUES
('Σημειώσεις Κεφαλαίου 1 - Σύνολα & Συναρτήσεις', 'Εισαγωγικές σημειώσεις στη θεωρία συνόλων και στις συναρτήσεις.', 'files/file1.doc'),
('Σημειώσεις Κεφαλαίου 2 - Όρια & Συνέχεια Συναρτήσεων', 'Σημειώσεις για όρια συναρτήσεων και συνέχεια.', 'files/file2.doc');

-- Course assignments
INSERT INTO homework (objectives, assignment_file, deliverables, due_date) VALUES
('Να εξασκηθούν οι φοιτητές στις βασικές έννοιες των συνόλων και των συναρτήσεων, καθώς και στον υπολογισμό τιμών και στη μελέτη βασικών χαρακτηριστικών συναρτήσεων.\nΝα κατανοήσουν την έννοια των ορίων και της συνέχειας και να μπορούν να υπολογίζουν απλά όρια και να εξετάζουν τη συνέχεια μιας συνάρτησης σε ένα σημείο.\nΝα αναπτύξουν την ικανότητα επίλυσης και παρουσίασης μαθηματικών προβλημάτων με σαφήνεια, σωστό μαθηματικό συμβολισμό και κατάλληλη αιτιολόγηση.',
 'assignments/ergasia1.doc',
 'Γραπτή αναφορά σε word\nΠαρουσίαση σε powerpoint',
 '2026-09-28'),
('Να εξοικειωθούν οι φοιτητές με την έννοια της παραγώγου και να εφαρμόζουν τους βασικούς κανόνες παραγώγισης σε διάφορες συναρτήσεις.\nΝα μπορούν να χρησιμοποιούν την παράγωγο για τη μελέτη συναρτήσεων, τον εντοπισμό κρίσιμων σημείων και τον προσδιορισμό τοπικών μεγίστων και ελαχίστων.\nΝα κατανοήσουν τη χρήση των παραγώγων σε πρακτικά προβλήματα, όπως η μελέτη κίνησης και προβλήματα μεγιστοποίησης εμβαδού.',
 'assignments/ergasia2.doc',
 'Γραπτή αναφορά σε word\nΠαρουσίαση σε powerpoint',
 '2026-10-24');
