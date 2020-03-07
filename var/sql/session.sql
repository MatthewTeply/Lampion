CREATE TABLE session (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    token varchar(255) not null,
    user_id int(11) not null,
    created int(11) not null,
    updated int(11) not null
) CHAR SET 'utf8mb4'