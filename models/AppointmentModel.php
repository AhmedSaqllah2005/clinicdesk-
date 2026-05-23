<?php
require_once 'BaseModel.php';

class AppointmentModel extends BaseModel
{

    // ============= الدوال الأساسية =============

    public function findById($id)
    {
        $sql = "SELECT a.*, p.name as patient_name, u.name as doctor_name 
                FROM appointments a 
                JOIN users p ON a.patient_id = p.id 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users u ON d.user_id = u.id 
                WHERE a.id = ?";
        $result = $this->execute($sql, 'i', [$id]);
        return $result->fetch_assoc();
    }

    public function book($data)
    {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";
        return $this->execute($sql, 'iisss', [
            $data['patient_id'],
            $data['doctor_id'],
            $data['appt_date'],
            $data['appt_time'],
            $data['reason']
        ]);
    }

    public function hasConflict($doctorId, $date, $time)
    {
        $sql = "SELECT id FROM appointments WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?";
        $result = $this->execute($sql, 'iss', [$doctorId, $date, $time]);
        return $result->num_rows > 0;
    }

    public function updateStatus($id, $status, $notes = null)
    {
        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            return false;
        }

        $sql = "UPDATE appointments SET status = ?";
        $params = [$status];
        $types = 's';

        if ($notes !== null) {
            $sql .= ", doctor_notes = ?";
            $params[] = $notes;
            $types .= 's';
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';

        return $this->execute($sql, $types, $params);
    }

    // ============= دوال الإحصائيات =============

    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM appointments";
        $result = $this->execute($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTodayCount($doctorId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM appointments WHERE appt_date = CURDATE()";
        $params = [];
        $types = '';

        if ($doctorId) {
            $sql .= " AND doctor_id = ?";
            $params[] = $doctorId;
            $types .= 'i';
        }

        $result = $this->execute($sql, $types, $params);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getDashboardStats($doctorId)
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM appointments 
                WHERE doctor_id = ? AND MONTH(appt_date) = MONTH(CURDATE())";
        $result = $this->execute($sql, 'i', [$doctorId]);
        $row = $result->fetch_assoc();
        return [
            'total' => $row['total'] ?? 0,
            'pending' => $row['pending'] ?? 0,
            'confirmed' => $row['confirmed'] ?? 0,
            'completed' => $row['completed'] ?? 0,
            'cancelled' => $row['cancelled'] ?? 0
        ];
    }

    // ============= دوال جلب البيانات =============

    public function getByPatient($patientId, $limit = 10, $offset = 0)
    {
        return $this->fetchAll("
        SELECT 
            appointments.*,
            users.name AS doctor_name
        FROM appointments
        JOIN doctors ON appointments.doctor_id = doctors.id
        JOIN users ON doctors.user_id = users.id
        WHERE appointments.patient_id = ?
        ORDER BY appointments.appt_date DESC,
                 appointments.appt_time DESC
        LIMIT $limit OFFSET $offset
    ", "i", [$patientId]);
    }

    public function getByDoctor($doctorId, $page, $filters = [])
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $sql = "SELECT a.*, p.name as patient_name 
                FROM appointments a 
                JOIN users p ON a.patient_id = p.id 
                WHERE a.doctor_id = ?";
        $params = [$doctorId];
        $types = 'i';

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        $sql .= " ORDER BY a.appt_date DESC LIMIT ? OFFSET ?";
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;
        $types .= 'ii';

        $result = $this->execute($sql, $types, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll($page, $filters = [])
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $sql = "SELECT a.*, p.name as patient_name, u.name as doctor_name, s.name as specialization 
                FROM appointments a 
                JOIN users p ON a.patient_id = p.id 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users u ON d.user_id = u.id 
                LEFT JOIN specializations s ON d.specialization_id = s.id 
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['doctor_id'])) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $filters['doctor_id'];
            $types .= 'i';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['patient_name'])) {
            $sql .= " AND p.name LIKE ?";
            $params[] = "%{$filters['patient_name']}%";
            $types .= 's';
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND a.appt_date BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
            $types .= 'ss';
        }

        $sql .= " ORDER BY a.appt_date DESC LIMIT ? OFFSET ?";
        $params[] = ITEMS_PER_PAGE;
        $params[] = $offset;
        $types .= 'ii';

        $result = $this->execute($sql, $types, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countFiltered($scope, $scopedId, $filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM appointments a ";

        if ($scope == 'patient') {
            $sql .= "WHERE a.patient_id = ?";
            $params = [$scopedId];
            $types = 'i';
        } elseif ($scope == 'doctor') {
            $sql .= "WHERE a.doctor_id = ?";
            $params = [$scopedId];
            $types = 'i';
        } else {
            $sql .= "WHERE 1=1";
            $params = [];
            $types = '';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        $result = $this->execute($sql, $types, $params);
        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }

    public function deleteAppointment($id)
    {
        $sql = "DELETE FROM appointments WHERE id = ?";
        return $this->execute($sql, 'i', [$id]);
    }
    public function getAllForExport($filters = [])
    {
        $sql = "SELECT a.*, 
                   p.name as patient_name, 
                   u.name as doctor_name, 
                   s.name as specialization 
            FROM appointments a 
            JOIN users p ON a.patient_id = p.id 
            JOIN doctors d ON a.doctor_id = d.id 
            JOIN users u ON d.user_id = u.id 
            LEFT JOIN specializations s ON d.specialization_id = s.id 
            WHERE 1=1";

        $params = [];
        $types = '';

        if (!empty($filters['doctor_id'])) {
            $sql .= " AND a.doctor_id = ?";
            $params[] = $filters['doctor_id'];
            $types .= 'i';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND a.appt_date BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
            $types .= 'ss';
        }

        $sql .= " ORDER BY a.appt_date DESC";

        $result = $this->execute($sql, $types, $params);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}