create table file_uses (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    file_id int(11) not null,
    enity_name varchar(255) not null,
    entity_id int(11) not null,
    property text not null
) CHAR SET 'utf8mb4'