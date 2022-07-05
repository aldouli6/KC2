<?php
namespace Src\TableGateways;

class UserGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "SELECT
         u.id, u.name, u.username,g.name as group_name, u.group_id
    FROM
        users u
        LEFT JOIN groups g ON u.group_id = g.id ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    
}