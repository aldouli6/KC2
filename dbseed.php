<?php
require 'bootstrap.php';
$pass =  password_hash("password", PASSWORD_DEFAULT);
$statement = <<<EOS
    CREATE TABLE IF NOT EXISTS groups (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=INNODB;

    INSERT INTO groups
        (id, name)
    VALUES
        (1, 'Default'),
        (2, 'Group A'),
        (3, 'Group B'),
        (4, 'Group C');

    CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        username VARCHAR(100) NOT NULL,
        group_id INT DEFAULT NULL,
        password VARCHAR(255) NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (group_id)
            REFERENCES groups(id)
            ON DELETE SET NULL
    ) ENGINE=INNODB;

    INSERT INTO users
        (id, name, username, group_id, password)
    VALUES
        (1, 'Aldo', 'user1', 1,"$pass"),
        (2, 'Maria', 'user2', 2,"$pass"),
        (3, 'John', 'user3', 1,"$pass");
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
?>