<?php

require_once 'BaseModel.php';

class MedicineModel extends BaseModel
{
    public function getAll()
    {
        $result = $this->execute("SELECT * FROM medicines ORDER BY name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getActive()
    {
        $result = $this->execute("SELECT * FROM medicines WHERE is_active = 1 ORDER BY name ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id)
    {
        $result = $this->execute("SELECT * FROM medicines WHERE id = ?", 'i', [$id]);
        return $result->fetch_assoc();
    }

    public function create($data)
    {
        return $this->execute(
            "INSERT INTO medicines (name, dosage_forms, default_dosage, unit) VALUES (?, ?, ?, ?)",
            'ssss',
            [$data['name'], $data['dosage_forms'] ?? '', $data['default_dosage'] ?? '', $data['unit'] ?? '']
        );
    }

    public function update($id, $data)
    {
        return $this->execute(
            "UPDATE medicines SET name = ?, dosage_forms = ?, default_dosage = ?, unit = ?, is_active = ? WHERE id = ?",
            'ssssi',
            [$data['name'], $data['dosage_forms'] ?? '', $data['default_dosage'] ?? '', $data['unit'] ?? '', $data['is_active'] ?? 1, $id]
        );
    }

    public function delete($id)
    {
        return $this->execute("DELETE FROM medicines WHERE id = ?", 'i', [$id]);
    }

    public function toggleActive($id)
    {
        return $this->execute("UPDATE medicines SET is_active = NOT is_active WHERE id = ?", 'i', [$id]);
    }
}
