CREATE TABLE points_of_sale(
    id VARCHAR(255) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    location POINT NOT NULL,
    services INT NOT NULL,
    payMethods VARCHAR(255) NOT NULL,
    SPATIAL INDEX location_index(location)
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE points_of_sale_opening_days(
    id INT PRIMARY KEY AUTO_INCREMENT,
    points_of_sale_id VARCHAR(255) NOT NULL,
    days_id INT NOT NULL,
    FOREIGN KEY(points_of_sale_id) REFERENCES points_of_sale(id),
    FOREIGN KEY(days_id) REFERENCES days(id)
);

CREATE TABLE points_of_sale_opening_hours(
    id INT PRIMARY KEY AUTO_INCREMENT,
    points_of_sale_opening_days_id INT NOT NULL,
    hours_id INT NOT NULL,
    FOREIGN KEY(points_of_sale_opening_days_id) REFERENCES points_of_sale_opening_days(id),
    FOREIGN KEY(hours_id) REFERENCES hours(id)
);
