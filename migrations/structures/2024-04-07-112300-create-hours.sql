CREATE TABLE hours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `from` TIME NOT NULL,
    `to` TIME NOT NULL,
    UNIQUE KEY(`from`, `to`)
);

CREATE TABLE days (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `from` INT NOT NULL,
    `to` INT NOT NULL,
    UNIQUE KEY(`from`, `to`)
);

