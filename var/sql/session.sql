CREATE TABLE session (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    session_id varchar(255) not null,
    user_id int(11) not null,
    created int(11) not null,
    updated int(11) not null,
    ip varchar(255) not null,
    device varchar(255) not null,
    os varchar(255) not null
) CHAR SET 'utf8mb4'