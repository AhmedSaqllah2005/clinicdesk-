<?php
require_once 'BaseModel.php';

class DoctorModel extends BaseModel
{

    public function findByUserId($userId)
    {
        $sql = "SELECT d.*, u.name, u.email, u.phone, s.name as specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                LEFT JOIN specializations s ON d.specialization_id = s.id
                WHERE d.user_id = ?";
        $result = $this->execute($sql, 'i', [$userId]);
        return $result->fetch_assoc();
    }

    public function getAll()
    {
        $sql = "SELECT d.id, d.user_id, d.specialization_id, d.bio,
                       d.consultation_fee, d.available_days, d.photo, d.years_experience,
                       u.name, u.email, u.phone,
                       s.name as specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                LEFT JOIN specializations s ON d.specialization_id = s.id
                GROUP BY d.id
                ORDER BY u.name";
        $result = $this->execute($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailableDays($doctorId)
    {
        $sql    = "SELECT available_days FROM doctors WHERE id = ?";
        $result = $this->execute($sql, 'i', [$doctorId]);
        $row    = $result->fetch_assoc();
        return explode(',', $row['available_days'] ?? 'Sun,Mon,Tue,Wed,Thu');
    }

    public function create($data)
    {
        $sql = "
        INSERT INTO doctors
        (
            user_id,
            specialization_id,
            consultation_fee,
            available_days,
            bio,
            years_experience
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

        return $this->execute($sql, 'iidssi', [
            $data['user_id'],
            $data['specialization_id'],
            $data['consultation_fee'],
            $data['available_days'],
            $data['bio'] ?? '',
            $data['years_experience'] ?? 0
        ]);
    }

    public function update($doctorId, $data)
    {
        $sql = "UPDATE doctors SET specialization_id = ?, consultation_fee = ?, available_days = ?, bio = ?
                WHERE id = ?";
        return $this->execute($sql, 'idssi', [
            $data['specialization_id'],
            $data['consultation_fee'],
            $data['available_days'],
            $data['bio'] ?? '',
            $doctorId
        ]);
    }

    public function deleteDoctor($id)
    {
        $sql = "DELETE FROM doctors WHERE id = ?";
        return $this->execute($sql, 'i', [$id]);
    }


    public function updatePhoto($doctorId, $photoName)
    {
        $sql = "UPDATE doctors SET photo = ? WHERE id = ?";
        return $this->execute($sql, 'si', [$photoName, $doctorId]);
    }


    public function getIdByUserId($userId)
    {
        $sql = "SELECT id FROM doctors WHERE user_id = ?";
        $result = $this->execute($sql, 'i', [$userId]);
        $row = $result->fetch_assoc();
        return $row['id'] ?? null;
    }


    public function searchDoctors($search = '')
    {
        $search = trim($search);

        if ($search === '') {
            return $this->getAll();
        }

        $sql = "SELECT d.id, d.user_id, d.specialization_id, d.bio,
                       d.consultation_fee, d.available_days, d.photo, d.years_experience,
                       u.name, u.email, u.phone,
                       s.name as specialization_name
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                LEFT JOIN specializations s ON d.specialization_id = s.id
                WHERE (
                    u.name  LIKE ?
                    OR u.email LIKE ?
                )
                GROUP BY d.id
                ORDER BY u.name";

        $like   = '%' . $search . '%';
        $result = $this->execute($sql, 'ss', [$like, $like]);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
