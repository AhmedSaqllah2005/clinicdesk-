<?php

require_once 'BaseModel.php';

class PrescriptionModel extends BaseModel
{
    public function getAll()
    {
        $sql = "
            SELECT

                p.*,

                a.appt_date,

                patient.name AS patient_name,

                doctor_user.name AS doctor_name

            FROM prescriptions p

            JOIN appointments a
                ON p.appointment_id = a.id

            JOIN users AS patient
                ON a.patient_id = patient.id

            JOIN doctors d
                ON a.doctor_id = d.id

            JOIN users AS doctor_user
                ON d.user_id = doctor_user.id

            ORDER BY p.id DESC
        ";

        $result = $this->execute($sql);

        if ($result && $result->num_rows > 0) {

            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }


    public function getByPatient($patientId)
    {
        $sql = "
            SELECT

                p.*,

                a.appt_date,

                u.name as doctor_name

            FROM prescriptions p

            JOIN appointments a
                ON p.appointment_id = a.id

            JOIN doctors d
                ON a.doctor_id = d.id

            JOIN users u
                ON d.user_id = u.id

            WHERE a.patient_id = ?

            ORDER BY a.appt_date DESC
        ";

        $result = $this->execute($sql, 'i', [$patientId]);

        if ($result && $result->num_rows > 0) {

            return $result->fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

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