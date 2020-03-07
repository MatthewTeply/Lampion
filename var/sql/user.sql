CREATE TABLE user (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    username varchar(255) not null,
    pwd varchar(60) not null,
    role varchar(255) not null
) CHAR SET 'utf8mb4'