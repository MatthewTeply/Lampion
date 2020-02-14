CREATE TABLE managed_file (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    extension varchar(255) not null,
    description text not null,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    changed TIMESTAMP not null,
    metadata text not null,
    tags text not null,
    uid varchar(255)
) CHAR SET 'utf8mb4';