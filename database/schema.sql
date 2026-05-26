-- Database schema for Task Tracker

-- users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- priority lookup table
CREATE TABLE IF NOT EXISTS priority (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  value INT NOT NULL UNIQUE
);

-- status lookup table
CREATE TABLE IF NOT EXISTS status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE,
  value INT NOT NULL UNIQUE
);

-- tasks table
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  status_id INT DEFAULT 2,
  priority_id INT DEFAULT 3,
  due_date DATE,
  user_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (status_id) REFERENCES status(id),
  FOREIGN KEY (priority_id) REFERENCES priority(id)
);

-- Insert priority lookup data
INSERT IGNORE INTO priority (id, name, value) VALUES
(1, 'High', 1),
(2, 'Medium', 2),
(3, 'Low', 3);

-- Insert status lookup data
INSERT IGNORE INTO status (id, name, value) VALUES
(1, 'Completed', 1),
(2, 'To Do', 2),
(3, 'Pending', 3);
