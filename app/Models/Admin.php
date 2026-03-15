<?php
namespace App\Models;

use App\Core\Model;

/**
 * Admin model. Provides methods for retrieving and authenticating admin
 * accounts. The admins table should contain at least the fields defined
 * below. Passwords must always be stored as hashes.
 */
class Admin extends Model
{
    /**
     * Find an admin by their email address.
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM admins WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Verify an admin's credentials. If the email exists and the password
     * matches the stored hash, return the admin record. Otherwise return
     * null.
     *
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public function authenticate(string $email, string $password): ?array
    {
        $admin = $this->findByEmail($email);
        if ($admin && password_verify($password, $admin['password_hash'])) {
            return $admin;
        }
        return null;
    }
}