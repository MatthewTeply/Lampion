CREATE TABLE api_keys (
    id int(11) not null PRIMARY KEY AUTO_INCREMENT,
    api_key text not null,
    note text not null
) CHAR SET 'utf8mb4'