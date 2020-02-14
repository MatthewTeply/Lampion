CREATE TABLE managed_file {
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    title text not null,
    extension varchar(255) not null,
    description text not null,
    created DEFAULT CURRENT_TIMESTAMP,
    changed TIMESTAMP not null,
    metadata text not null,
    tags text not null,
    uid varchar(255) null,
}