<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table         = 'siswa';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nis', 'nama', 'kelas'];

    public function getSiswa()
    {
        return $this->findAll();
    }

    /**
     * Map class name to 1-digit major code:
     * 1 = RPL, 2 = Broadcast, 3 = Animation
     */
    public function getMajorCode(string $kelas): ?string
    {
        if (stripos($kelas, 'RPL') !== false) {
            return '1';
        }
        if (stripos($kelas, 'Broadcast') !== false || stripos($kelas, 'BC') !== false) {
            return '2';
        }
        if (stripos($kelas, 'Animation') !== false || stripos($kelas, 'ANM') !== false) {
            return '3';
        }
        return null;
    }

    /**
     * Auto-generate next 8-digit NIS for major: "250" + J + NNNN (1001..9999).
     * Next number = highest existing NNNN within prefix "250"+J, plus 1.
     * Deleted numbers are never reused (checked across both active siswa and nis_history).
     */
    public function getNextNis(string $majorCode): string
    {
        $prefix = '250' . $majorCode;
        $db = $this->db;

        // Query maximum existing NIS from both active siswa and nis_history (including deleted/previous NIS)
        $sql = "SELECT MAX(n) as max_nis FROM (
                    SELECT nis as n FROM siswa WHERE nis LIKE ?
                    UNION
                    SELECT old_nis as n FROM nis_history WHERE old_nis LIKE ?
                    UNION
                    SELECT new_nis as n FROM nis_history WHERE new_nis LIKE ?
                ) as all_nis";

        $likePrefix = $prefix . '%';
        $query = $db->query($sql, [$likePrefix, $likePrefix, $likePrefix]);
        $row = $query->getRow();

        if (!empty($row->max_nis)) {
            $lastNumber = (int) substr($row->max_nis, 4, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1001;
        }

        if ($nextNumber > 9999) {
            throw new \RuntimeException("NIS limit reached for major code {$majorCode} (maximum is 9999).");
        }

        return $prefix . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Insert new student with auto-generated NIS inside a transaction with 1 retry on duplicate.
     */
    public function insertWithNis(string $nama, string $kelas): string
    {
        $majorCode = $this->getMajorCode($kelas);
        if (!$majorCode) {
            throw new \InvalidArgumentException("Invalid class major for '{$kelas}'.");
        }

        $db = $this->db;
        $maxRetries = 2;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $db->transBegin();
            try {
                $nis = $this->getNextNis($majorCode);
                $this->insert([
                    'nis'   => $nis,
                    'nama'  => $nama,
                    'kelas' => $kelas,
                ]);
                $siswaId = $this->getInsertID();

                // Record in nis_history
                $db->table('nis_history')->insert([
                    'siswa_id'   => $siswaId,
                    'old_nis'    => null,
                    'new_nis'    => $nis,
                    'changed_at' => date('Y-m-d H:i:s'),
                ]);

                if ($db->transStatus() === false) {
                    $db->transRollback();
                    if ($attempt === $maxRetries) {
                        throw new \RuntimeException("Failed to insert student after retries.");
                    }
                    continue;
                }

                $db->transCommit();
                return $nis;
            } catch (\Throwable $e) {
                $db->transRollback();
                if ($attempt === $maxRetries) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException("Failed to generate unique NIS.");
    }

    /**
     * Update student. If major changed, regenerates NIS and records in nis_history.
     * If major unchanged, keeps existing NIS.
     */
    public function updateWithNis(int $id, string $nama, string $newKelas): array
    {
        $student = $this->find($id);
        if (!$student) {
            throw new \InvalidArgumentException("Student not found.");
        }

        $oldMajor = $this->getMajorCode($student['kelas']);
        $newMajor = $this->getMajorCode($newKelas);

        if (!$newMajor) {
            throw new \InvalidArgumentException("Invalid new class major: {$newKelas}");
        }

        $db = $this->db;

        // If major is unchanged, keep NIS exactly as is
        if ($oldMajor === $newMajor) {
            $this->update($id, [
                'nama'  => $nama,
                'kelas' => $newKelas,
            ]);
            return [
                'nis_changed' => false,
                'old_nis'     => $student['nis'],
                'new_nis'     => $student['nis'],
            ];
        }

        // Major changed: generate new NIS for the new major inside a transaction
        $maxRetries = 2;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $db->transBegin();
            try {
                $newNis = $this->getNextNis($newMajor);
                $this->update($id, [
                    'nis'   => $newNis,
                    'nama'  => $nama,
                    'kelas' => $newKelas,
                ]);

                // Record change in history
                $db->table('nis_history')->insert([
                    'siswa_id'   => $id,
                    'old_nis'    => $student['nis'],
                    'new_nis'    => $newNis,
                    'changed_at' => date('Y-m-d H:i:s'),
                ]);

                if ($db->transStatus() === false) {
                    $db->transRollback();
                    if ($attempt === $maxRetries) {
                        throw new \RuntimeException("Failed to update student after retries.");
                    }
                    continue;
                }

                $db->transCommit();
                return [
                    'nis_changed' => true,
                    'old_nis'     => $student['nis'],
                    'new_nis'     => $newNis,
                ];
            } catch (\Throwable $e) {
                $db->transRollback();
                if ($attempt === $maxRetries) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException("Failed to update student NIS.");
    }

    /**
     * Delete student while preserving their NIS in history so it is never reused.
     */
    public function deleteWithHistory(int $id): bool
    {
        $student = $this->find($id);
        if (!$student) {
            return false;
        }

        $db = $this->db;
        $db->transBegin();
        try {
            $db->table('nis_history')->insert([
                'siswa_id'   => $id,
                'old_nis'    => $student['nis'],
                'new_nis'    => null,
                'changed_at' => date('Y-m-d H:i:s'),
            ]);

            $this->delete($id);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return false;
            }

            $db->transCommit();
            return true;
        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }
}