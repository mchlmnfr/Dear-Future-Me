<?php
namespace App\Models;

use App\Core\Model;

/**
 * Participant model. Handles CRUD operations for participant accounts.
 * Participants are employees taking part in the wellness challenge.
 */
class Participant extends Model
{
    /**
     * Create a new participant account. Returns the newly created ID.
     *
     * @param string $fullName
     * @param int    $departmentId
     * @param string $employeeId
     * @param string $passwordHash
     * @param bool   $mustChangePassword
     * @return int|null
     */
    public function create(string $fullName, int $departmentId, string $employeeId, string $passwordHash, bool $mustChangePassword = true): ?int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO participants (full_name, department_id, employee_id, password_hash, must_change_password, status, created_at, updated_at)
             VALUES (:full_name, :department_id, :employee_id, :password_hash, :must_change_password, :status, NOW(), NOW())'
        );
        $status = 'active';
        $result = $stmt->execute([
            'full_name'          => $fullName,
            'department_id'      => $departmentId,
            'employee_id'        => $employeeId,
            'password_hash'      => $passwordHash,
            'must_change_password' => $mustChangePassword ? 1 : 0,
            'status'             => $status,
        ]);
        return $result ? (int)$this->db->lastInsertId() : null;
    }

    /**
     * Find a participant by employee ID. Returns an associative array or null.
     *
     * @param string $employeeId
     * @return array|null
     */
    public function findByEmployeeId(string $employeeId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM participants WHERE employee_id = :employee_id LIMIT 1');
        $stmt->execute(['employee_id' => $employeeId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Update a participant's password and optionally the must_change_password flag.
     *
     * @param int    $participantId
     * @param string $passwordHash
     * @param bool   $requireChange
     * @return bool
     */
    public function updatePassword(int $participantId, string $passwordHash, bool $requireChange = false): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE participants
             SET password_hash = :password_hash, must_change_password = :must_change_password, updated_at = NOW()
             WHERE id = :id'
        );
        return $stmt->execute([
            'password_hash'        => $passwordHash,
            'must_change_password' => $requireChange ? 1 : 0,
            'id'                   => $participantId,
        ]);
    }

    /**
     * Authenticate a participant using employee ID and password. Returns
     * participant record on success or null on failure.
     *
     * @param string $employeeId
     * @param string $password
     * @return array|null
     */
    public function authenticate(string $employeeId, string $password): ?array
    {
        $participant = $this->findByEmployeeId($employeeId);
        if ($participant && password_verify($password, $participant['password_hash'])) {
            return $participant;
        }
        return null;
    }

    /**
     * Find a participant by primary key ID.
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM participants WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}