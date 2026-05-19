-- Database schema for Task Tracker

-- users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- tasks table
-- CREATE TABLE tasks (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   title VARCHAR(255) NOT NULL,
--   description TEXT,
--   status VARCHAR(50) DEFAULT 'todo',
--   priority INT DEFAULT 3,
--   due_date DATE,
--   assignee_id INT,
--   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   FOREIGN KEY (assignee_id) REFERENCES users(id)
-- );
