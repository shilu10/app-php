CREATE TABLE job_listings (
    id INT NOT NULL AUTO_INCREMENT,
    user_id BINARY(16) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    tags VARCHAR(255),
    company VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state CHAR(2) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    requirements TEXT,
    benefits TEXT,
    PRIMARY KEY (id),
    KEY idx_user_id (user_id),
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
