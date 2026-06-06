<?php

require_once 'BaseModel.php';

class PatientModel extends BaseModel
{


    public function findByUserId($userId)
    {
       return $this->fetchOne("
            SELECT *
            FROM users
            WHERE id = ?
            AND role = 'patient'
        ", "i", [$userId]);
    }


    public function getAll()
    {
        $sql = "SELECT id, name, email, phone, created_at
                FROM users
                WHERE role = 'patient'
                ORDER BY name ASC";
        $result = $this->execute($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ? AND role = 'patient'";
        $result = $this->execute($sql, 'i', [$id]);
        return $result->fetch_assoc();
    }


    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'patient'";
        $result = $this->execute($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }


    public function getAllPaginated($page = 1, $itemsPerPage = 10)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $sql = "SELECT id, name, email, phone, created_at
                FROM users
                WHERE role = 'patient'
                ORDER BY name ASC
                LIMIT ? OFFSET ?";
        $result = $this->execute($sql, 'ii', [$itemsPerPage, $offset]);
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function create($name, $email, $password, $phone = '')
    {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (name, email, password, role, phone)
                VALUES (?, ?, ?, 'patient', ?)";
        return $this->execute($sql, 'ssss', [$name, $email, $hashed, $phone]);
    }


    public function update($id, $name, $email, $phone = '')
    {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?
                WHERE id = ? AND role = 'patient'";
        return $this->execute($sql, 'sssi', [$name, $email, $phone, $id]);
    }


    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = ? AND role = 'patient'";
        return $this->execute($sql, 'i', [$id]);
    }


    public function toggleActive($id)
    {
        $sql = "UPDATE users SET is_active = NOT is_active
                WHERE id = ? AND role = 'patient'";
        return $this->execute($sql, 'i', [$id]);
    }


    public function searchPatients($search = '')
    {
        $search = trim($search);

        if ($search === '') {
            return $this->getAll();
        }

        $sql = "SELECT id, name, email, phone, created_at
                FROM users
                WHERE role = 'patient'
                AND (
                    id          LIKE ?
                    OR name     LIKE ?
                    OR email    LIKE ?
                    OR phone    LIKE ?
                )
                ORDER BY name ASC";

        $like   = '%' . $search . '%';
        $result = $this->execute($sql, 'ssss', [$like, $like, $like, $like]);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}