-- Create a table for storing spam check results
CREATE TABLE spam_checks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    is_spam BOOLEAN NOT NULL,
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email), -- Index on email for faster searching
    INDEX idx_checked_at (checked_at) -- Index on check date for faster filtering by date
) ENGINE=InnoDB;

-- Create a table for storing known spam patterns
CREATE TABLE spam_patterns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pattern VARCHAR(255) NOT NULL,
    description TEXT,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pattern (pattern(10)) -- Index on the beginning of the pattern for faster searching
) ENGINE=InnoDB;

-- Create a table for logging API requests for spam checking
CREATE TABLE api_spam_checks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    response TEXT NOT NULL,
    checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email), -- Index on email for faster searching
    INDEX idx_checked_at (checked_at) -- Index on check date for faster filtering by date
) ENGINE=InnoDB;
