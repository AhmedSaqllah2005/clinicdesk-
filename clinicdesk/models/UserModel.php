<?php
require_once 'BaseModel.php';

class UserModel extends BaseModel
{

    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $result = $this->execute($sql, 'i', [$id]);
        return $result->fetch_assoc();
    }
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $result = $this->execute($sql, 's', [$email]);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    public function create($data)
    {
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)";
        $this->execute($sql, 'sssss', [
            $data['name'],
            $data['email'],
            $hashed,
            $data['role'],
            $data['phone'] ?? ''
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        return $this->execute($sql, 'sssi', [
            $data['name'],
            $data['email'],
            $data['phone'] ?? '',
            $id
        ]);
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->execute($sql, 'i', [$id]);
    }

    public function toggleActive($id)
    {
        $sql = "UPDATE users SET is_active = NOT is_active WHERE id = ?";
        return $this->execute($sql, 'i', [$id]);
    }

    public function getAllPaginated($page, $role = "", $search = "")
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($role)) {
            $sql .= " AND role = ?";
            $params[] = $role;
            $types .= 's';
        }

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $types .= 'sss';
        }

        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;
        $types .= 'ii';

        $result = $this->execute($sql, $types, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function countAll($role = "", $search = "")
    {
        $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($role)) {
            $sql .= " AND role = ?";
            $params[] = $role;
            $types .= 's';
        }

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $types .= 'sss';
        }

        $result = $this->execute($sql, $types, $params);
        $row = $result->fetch_assoc();
        return $row['total'];
    }


    public function getPatients()
    {
        $sql = "SELECT id, name, email, phone FROM users WHERE role = 'patient' ORDER BY name";
        $result = $this->execute($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDoctors()
    {
        $sql = "SELECT u.id, u.name, u.email, d.specialization_id 
                FROM users u 
                JOIN doctors d ON u.id = d.user_id 
                WHERE u.role = 'doctor'
                ORDER BY u.name";
        $result = $this->execute($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $result = $this->execute($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $result = $this->execute($sql, 'i', [$id]);
        return $result->fetch_assoc();
    }

    public function createUser($name, $email, $password, $role)
    {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        return $this->execute($sql, 'ssss', [$name, $email, $hashed, $role]);
    }

    public function updateUser($id, $name, $email)
    {
        $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        return $this->execute($sql, 'ssi', [$name, $email, $id]);
    }

    public function countByRole($role)
    {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = ?";
        $result = $this->execute($sql, 's', [$role]);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // =========================================================================
    // updatePassword — تحديث كلمة المرور مع hash آمن
    // =========================================================================

    public function updatePassword($id, $newPassword)
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->execute($sql, 'si', [$hashed, $id]);
    }
}
