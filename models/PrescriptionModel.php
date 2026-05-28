<?php

require_once 'BaseModel.php';

class PrescriptionModel extends BaseModel
{
    // =========================================================================
    // Get All Prescriptions
    // =========================================================================

    public function getAll()
    {
        $sql = "
            SELECT 
                p.*,
                a.appt_date,

                patient.name AS patient_name,
                doctor.name AS doctor_name

            FROM prescriptions p

            JOIN appointments a
                ON p.appointment_id = a.id

            JOIN users patient
                ON a.patient_id = patient.id

            JOIN doctors d
                ON a.doctor_id = d.id

            JOIN users doctor
                ON d.user_id = doctor.id

            ORDER BY p.created_at DESC
        ";

        $result = $this->execute($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // =========================================================================
    // Get Prescriptions By Patient
    // =========================================================================

    public function getByPatient($patientId)
    {
        $sql = "
            SELECT
                p.*,
                a.appt_date,

                doctor.name AS doctor_name,
                patient.name AS patient_name

            FROM prescriptions p

            JOIN appointments a
                ON p.appointment_id = a.id

            JOIN doctors d
                ON a.doctor_id = d.id

            JOIN users doctor
                ON d.user_id = doctor.id

            JOIN users patient
                ON a.patient_id = patient.id

            WHERE a.patient_id = ?

            ORDER BY a.appt_date DESC
        ";

        $result = $this->execute($sql, 'i', [$patientId]);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    // =========================================================================
    // Create Prescription
    // =========================================================================

    public function create($data)
    {
        $sql = "
            INSERT INTO prescriptions
            (
                appointment_id,
                diagnosis,
                medications,
                notes,
                file_path
            )

            VALUES (?, ?, ?, ?, ?)
        ";

        return $this->execute($sql, 'issss', [

            $data['appointment_id'],
            $data['diagnosis'],
            $data['medications'],
            $data['notes'] ?? '',
            $data['file_path'] ?? null
        ]);
    }

    // =========================================================================
    // Get Prescription By Appointment
    // =========================================================================

    public function getByAppointment($appointmentId)
    {
        $sql = "
            SELECT *
            FROM prescriptions
            WHERE appointment_id = ?
        ";

        $result = $this->execute($sql, 'i', [$appointmentId]);

        return $result->fetch_assoc();
    }

    // =========================================================================
    // Check If Prescription Exists
    // =========================================================================

    public function existsForAppointment($appointmentId)
    {
        $sql = "
            SELECT id
            FROM prescriptions
            WHERE appointment_id = ?
        ";

        $result = $this->execute($sql, 'i', [$appointmentId]);

        return $result->num_rows > 0;
    }
}