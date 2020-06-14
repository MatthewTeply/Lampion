CREATE TABLE user (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    username varchar(255) not null,
    pwd varchar(60) not null,
    role JSON,
    img JSON
) CHAR SET 'utf8mb4'