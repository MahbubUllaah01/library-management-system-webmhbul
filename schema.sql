CREATE DATABASE IF NOT EXISTS library_db CHARACTER SET utf8mb4;
USE library_db;

CREATE TABLE IF NOT EXISTS books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  author VARCHAR(100) NOT NULL,
  isbn VARCHAR(20) NOT NULL UNIQUE,
  category VARCHAR(60) NOT NULL,
  copies INT NOT NULL DEFAULT 1,
  available INT NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS issues (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT NOT NULL,
  member_id INT NOT NULL,
  issue_date DATE NOT NULL,
  due_date DATE NOT NULL,
  return_date DATE NULL,
  FOREIGN KEY (book_id) REFERENCES books(id),
  FOREIGN KEY (member_id) REFERENCES members(id)
);

-- Sample data for testing
INSERT IGNORE INTO books (title, author, isbn, category, copies, available) VALUES
('Introduction to Algorithms', 'Thomas H. Cormen', '9780262046305', 'Computer Science', 3, 3),
('Database System Concepts', 'Abraham Silberschatz', '9780078022159', 'Computer Science', 2, 2),
('Computer Networking', 'James F. Kurose', '9780133594140', 'Networking', 2, 2);
INSERT IGNORE INTO members (name, email, phone) VALUES
('Sample Member', 'member@example.com', '01700000000');
