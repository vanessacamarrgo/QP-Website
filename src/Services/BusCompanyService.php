<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BusCompany;
use PDO;

final class BusCompanyService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
    }

    public function all(string $name = '', string $status = '', int $page = 1, int $perPage = 10): array
    {
        $sql = 'SELECT * FROM tasks.bus_companies WHERE 1=1';
        $params = [];

        if ($name !== '') {
            $sql .= ' AND name LIKE :name';
            $params['name'] = "%$name%";
        }

        if ($status !== '') {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        } else {
            // Por padrão não mostra deletados na listagem normal
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

        return array_map(
            fn(array $row) => \App\Models\BusCompany::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    public function find(int $id): ?BusCompany
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks.bus_companies WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? BusCompany::fromRow($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks.bus_companies (name, url, city, status, logo)
             VALUES (:name, :url, :city, :status, :logo)'
        );
        $stmt->execute([
            'name'   => $data['name'],
            'url'    => $data['url'],
            'city'   => $data['city'],
            'status' => $data['status'],
            'logo'   => $data['logo'] ?? null,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $data['id'] = $id;
        $this->log('bus_company', $id, 'create', null, json_encode($data));
        return $id;
    }

    public function update(int $id, array $data): void
    {
        $old = $this->find($id);

        $sql = 'UPDATE tasks.bus_companies SET name = :name, url = :url, city = :city, status = :status';
        if ($data['logo'] !== null) $sql .= ', logo = :logo';
        $sql .= ' WHERE id = :id';

        $params = [
            'id'     => $id,
            'name'   => $data['name'],
            'url'    => $data['url'],
            'city'   => $data['city'],
            'status' => $data['status'],
        ];
        if ($data['logo'] !== null) $params['logo'] = $data['logo'];

        $this->pdo->prepare($sql)->execute($params);

        $new = $this->find($id);
        $this->log('bus_company', $id, 'update', json_encode($old), json_encode($new));
    }

    /** Soft delete: muda status para 'deleted' */
    public function delete(int $id): bool
    {
        $company = $this->find($id);
        if (!$company) return false;

        $this->pdo->prepare(
            "UPDATE tasks.bus_companies SET status = 'deleted' WHERE id = :id"
        )->execute(['id' => $id]);

        $this->log('bus_company', $id, 'delete', json_encode($company), null);
        return true;
    }

    /** Restaura um registro deletado */
    public function restore(int $id): bool
    {
        $company = $this->find($id);
        if (!$company || $company->status !== 'deleted') return false;

        $this->pdo->prepare(
            "UPDATE tasks.bus_companies SET status = 'active' WHERE id = :id"
        )->execute(['id' => $id]);

        $restored = $this->find($id);
        $this->log('bus_company', $id, 'restore', json_encode($company), json_encode($restored));
        return true;
    }

    public function allNames(): array
    {
        $query = $this->pdo->query("SELECT name FROM tasks.bus_companies WHERE status != 'deleted' ORDER BY name ASC");
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Logs apenas desta viação (para tela de show) */
    public function getLogsForEntity(int $id): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT l.*, u.name as user_name
             FROM tasks.entity_logs l
             LEFT JOIN tasks.users u ON l.user_id = u.id
             WHERE l.entity_type = 'bus_company' AND l.entity_id = :id
             ORDER BY l.created_at DESC"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Logs globais com filtros (tela de logs) */
    public function getLogs(array $filters = []): array
    {
        $sql = "SELECT l.*, u.name as user_name,
                    b.name as entity_name
                FROM tasks.entity_logs l
                LEFT JOIN tasks.users u ON l.user_id = u.id
                LEFT JOIN tasks.bus_companies b ON l.entity_type = 'bus_company' AND l.entity_id = b.id
                WHERE l.entity_type = 'bus_company'";

        $params = [];

        if (!empty($filters['id'])) {
            $sql .= ' AND l.id = :id';
            $params['id'] = $filters['id'];
        }
        if (!empty($filters['user_id'])) {
            $sql .= ' AND l.user_id = :user_id';
            $params['user_id'] = $filters['user_id'];
        }
        if (!empty($filters['bus_id'])) {
            $sql .= ' AND l.entity_id = :bus_id';
            $params['bus_id'] = $filters['bus_id'];
        }
        if (!empty($filters['bus_name'])) {
            $sql .= ' AND (b.name LIKE :bus_name OR l.old_value LIKE :bus_name_json)';
            $params['bus_name']      = '%' . $filters['bus_name'] . '%';
            $params['bus_name_json'] = '%' . $filters['bus_name'] . '%';
        }
        if (!empty($filters['action'])) {
            $sql .= ' AND l.action = :action';
            $params['action'] = $filters['action'];
        }
        if (!empty($filters['date'])) {
            $sql .= ' AND DATE(l.created_at) = :date';
            $params['date'] = $filters['date'];
        }

        $sql .= ' ORDER BY l.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function log(string $entityType, int $entityId, string $action, ?string $old, ?string $new): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

        $this->pdo->prepare(
            'INSERT INTO tasks.entity_logs (entity_type, entity_id, user_id, action, old_value, new_value)
             VALUES (:entity_type, :entity_id, :user_id, :action, :old, :new)'
        )->execute([
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'user_id'     => $userId,
            'action'      => $action,
            'old'         => $old,
            'new'         => $new,
        ]);
    }
}