CREATE TABLE entity_file (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    filename text not null,
    fullPath text not null,
    relativePath text not null,
    extension varchar(255) not null,
    note text not null,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    metadata JSON not null,
    tags JSON not null,
    user_id int(11) null
) CHAR SET 'utf8mb4';