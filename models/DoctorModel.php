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
        $sql = "SELECT d.*, u.name, u.email, u.phone, s.name as specialization_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN specializations s ON d.specialization_id = s.id 
                ORDER BY u.name";
        $result = $this->execute($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailableDays($doctorId)
    {
        $sql = "SELECT available_days FROM doctors WHERE id = ?";
        $result = $this->execute($sql, 'i', [$doctorId]);
        $row = $result->fetch_assoc();
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
            years_experience
        )

        VALUES (?, ?, ?, ?, ?)
    ";

        return $this->execute($sql, 'iidsi', [

            $data['user_id'],
            $data['specialization_id'],
            $data['consultation_fee'],
            $data['available_days'],
            $data['years_experience']

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
}