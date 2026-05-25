<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use PDO;

final class UserService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    public function all(string $name = '', string $status = '', int $page = 1, int $perPage = 10): array
    {
        $sql = 'SELECT * FROM tasks.users WHERE 1=1';
        $params = [];

        if ($name !== '') {
            $sql .= ' AND name LIKE :name';
            $params['name'] = "%$name%";
        }

        if ($status !== '') {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        } else {
            $sql .= " AND status != 'deleted'";
        }

        $countSql = str_replace('SELECT *', 'SELECT COUNT(*)', $sql);
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items'   => array_map(fn($row) => User::fromRow($row), $stmt->fetchAll()),
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => (int) ceil($total / $perPage),
        ];
    }

    public function find(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks.users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? User::fromRow($row) : null;
    }

    public function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks.users (name, email, password, status)
             VALUES (:name, :email, :password, :status)'
        );
        $stmt->execute([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $hash,
            'status'   => $data['status'] ?? 'active',
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $safe = ['id' => $id, 'name' => $data['name'], 'email' => $data['email'], 'status' => $data['status'] ?? 'active'];
        $this->log($id, 'create', null, json_encode($safe));
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $old = $this->find($id);

        $sql = 'UPDATE tasks.users SET name = :name, email = :email, status = :status';
        $params = [
            'id'     => $id,
            'name'   => $data['name'],
            'email'  => $data['email'],
            'status' => $data['status'],
        ];

        if (!empty($data['password'])) {
            $sql .= ', password = :password';
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= ' WHERE id = :id';
        $this->pdo->prepare($sql)->execute($params);

        $new = $this->find($id);
        $this->log($id, 'update', json_encode($old), json_encode($new));
    }

    /** Soft delete */
    public function delete(int $id): bool
    {
        $user = $this->find($id);
        if (!$user) return false;

        $this->pdo->prepare(
            "UPDATE tasks.users SET status = 'deleted' WHERE id = :id"
        )->execute(['id' => $id]);

        $this->log($id, 'delete', json_encode($user), null);
        return true;
    }

    /** Restaurar usuário deletado */
    public function restore(int $id): bool
    {
        $user = $this->find($id);
        if (!$user || $user->status !== 'deleted') return false;

        $this->pdo->prepare(
            "UPDATE tasks.users SET status = 'active' WHERE id = :id"
        )->execute(['id' => $id]);

        $restored = $this->find($id);
        $this->log($id, 'restore', json_encode($user), json_encode($restored));
        return true;
    }

    public function getLogsForEntity(int $id): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT l.*, u.name as user_name
             FROM tasks.entity_logs l
             LEFT JOIN tasks.users u ON l.user_id = u.id
             WHERE l.entity_type = 'user' AND l.entity_id = :id
             ORDER BY l.created_at DESC"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function log(int $id, string $action, ?string $old, ?string $new): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

        $this->pdo->prepare(
            'INSERT INTO tasks.entity_logs (entity_type, entity_id, user_id, action, old_value, new_value)
             VALUES (:entity_type, :entity_id, :user_id, :action, :old, :new)'
        )->execute([
            'entity_type' => 'user',
            'entity_id'   => $id,
            'user_id'     => $userId,
            'action'      => $action,
            'old'         => $old,
            'new'         => $new,
        ]);
    }
}