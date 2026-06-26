<?php
class UserModel extends Model
{
    public function findByEmail(string $email): ?array
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    public function findById(int $id): ?array
    {
        $db = $this->getDb();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: null;
    }

    public function create(array $data): bool
    {
        $db = $this->getDb();
        $stmt = $db->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $role = $data['role'] ?? 'pelanggan';
        $stmt->bind_param("ssss", $data['nama'], $data['email'], $data['password'], $role);
        return $stmt->execute();
    }
}
