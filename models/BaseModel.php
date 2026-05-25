<?php

class BaseModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function execute($sql, $types = '', $params = [])
    {
        return $this->db->query($sql, $types, $params);
    }

    protected function fetchOne($sql, $types = '', $params = [])
    {
        $result = $this->db->query($sql, $types, $params);

        return $result->fetch_assoc();
    }

    protected function fetchAll($sql, $types = '', $params = [])
    {
        $result = $this->db->query($sql, $types, $params);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}