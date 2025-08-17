CREATE TABLE users (
    id BINARY(16) NOT NULL DEFAULT (UUID_TO_BIN(UUID())),
    name VARCHAR(100),
    email VARCHAR(256),
    city VARCHAR(100),
    state CHAR(2),
    password VARCHAR(1000),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
