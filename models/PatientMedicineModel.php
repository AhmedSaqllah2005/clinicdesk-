<?php

require_once 'BaseModel.php';

class PatientMedicineModel extends BaseModel
{
    // =========================================================================
    // getByDoctor — كل الأدوية التي وصفها طبيب معين مع اسم المريض والدواء
    // =========================================================================
    public function getByDoctor($doctorId)
    {
        $sql = "
            SELECT
                pm.*,
                u.name  AS patient_name,
                u.phone AS patient_phone,
                m.name  AS medicine_name,
                m.dosage_forms,
                m.unit
            FROM patient_medicines pm
            JOIN users    u ON pm.patient_id  = u.id
            JOIN medicines m ON pm.medicine_id = m.id
            WHERE pm.doctor_id = ?
            ORDER BY pm.prescribed_at DESC
        ";
        $result = $this->execute($sql, 'i', [$doctorId]);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // =========================================================================
    // getByPatient — كل الأدوية الخاصة بمريض مع اسم الطبيب
    // =========================================================================
    public function getByPatient($patientId)
    {
        $sql = "
            SELECT
                pm.*,
                m.name         AS medicine_name,
                m.dosage_forms,
                m.unit,
                u.name         AS doctor_name
            FROM patient_medicines pm
            JOIN medicines m ON pm.medicine_id  = m.id
            JOIN doctors   d ON pm.doctor_id    = d.id
            JOIN users     u ON d.user_id        = u.id
            WHERE pm.patient_id = ?
            ORDER BY pm.prescribed_at DESC
        ";
        $result = $this->execute($sql, 'i', [$patientId]);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // =========================================================================
    // create — إضافة دواء لمريض
    // =========================================================================
    public function create($data)
    {
        $sql = "
            INSERT INTO patient_medicines
                (doctor_id, patient_id, medicine_id, dosage, duration, notes)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        return $this->execute($sql, 'iiisss', [
            $data['doctor_id'],
            $data['patient_id'],
            $data['medicine_id'],
            $data['dosage'],
            $data['duration'] ?? '',
            $data['notes']    ?? '',
        ]);
    }

    // =========================================================================
    // update — تعديل دواء موجود (الطبيب يعدّل فقط ما وصفه هو)
    // =========================================================================
    public function update($id, $doctorId, $data)
    {
        $sql = "
            UPDATE patient_medicines
            SET medicine_id = ?, dosage = ?, duration = ?, notes = ?
            WHERE id = ? AND doctor_id = ?
        ";
        return $this->execute($sql, 'isssii', [
            $data['medicine_id'],
            $data['dosage'],
            $data['duration'] ?? '',
            $data['notes']    ?? '',
            $id,
            $doctorId,
        ]);
    }

    // =========================================================================
    // delete — حذف دواء (الطبيب يحذف فقط ما وصفه هو)
    // =========================================================================
    public function delete($id, $doctorId)
    {
        $sql = "DELETE FROM patient_medicines WHERE id = ? AND doctor_id = ?";
        return $this->execute($sql, 'ii', [$id, $doctorId]);
    }

    // =========================================================================
    // findById — جلب سجل واحد للتحقق من الملكية قبل التعديل أو الحذف
    // =========================================================================
    public function findById($id)
    {
        $result = $this->execute("SELECT * FROM patient_medicines WHERE id = ?", 'i', [$id]);
        return $result->fetch_assoc();
    }

    // =========================================================================
    // getPatientsOfDoctor — المرضى الذين لديهم مواعيد مع هذا الطبيب
    // (لقائمة المرضى في فورم إضافة الدواء)
    // =========================================================================
    public function getPatientsOfDoctor($doctorId)
    {
        $sql = "
            SELECT DISTINCT u.id, u.name, u.phone
            FROM appointments a
            JOIN users u ON a.patient_id = u.id
            WHERE a.doctor_id = ?
            ORDER BY u.name
        ";
        $result = $this->execute($sql, 'i', [$doctorId]);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
