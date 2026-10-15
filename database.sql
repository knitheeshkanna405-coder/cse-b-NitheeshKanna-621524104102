CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    category VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(100) NOT NULL,
 email VARCHAR(100) UNIQUE NOT NULL,
 password VARCHAR(255) NOT NULL,
);
CREATE TABLE transactions (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 type ENUM('income','expense') NOT NULL,
 category VARCHAR(50) NOT NULL,
 amount DECIMAL(10,2) NOT NULL,
 description VARCHAR(255),
 transaction_date DATE NOT NULL DEFAULT (CURRENT_DATE),
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
INSERT IGNORE INTO users(username,email,password)
VALUES('student','knitheesh@gmail.com','12345');
ALTER TABLE users ADD COLUMN full_name VARCHAR(100) NOT NULL AFTER username;