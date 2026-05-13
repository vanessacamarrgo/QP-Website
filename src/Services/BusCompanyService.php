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

    public function all(string $name = '', string $status = ''): array
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
        }

        $stmt = $this->pdo->prepare($sql . ' ORDER BY created_at DESC');
        $stmt->execute($params);

        return array_map(fn($row) => BusCompany::fromRow($row), $stmt->fetchAll());
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
            'name' => $data['name'],
            'url' => $data['url'],
            'city' => $data['city'],
            'status' => $data['status'],
            'logo' => $data['logo']
        ]);

        $id = (int)$this->pdo->lastInsertId();

        $data['id'] = $id;

        $this->log($id, 'create', null, json_encode($data));
        return $id;
    }
    public function update(int $id, array $data): void
    {
        $old = $this->find($id);

        $sql = 'UPDATE tasks.bus_companies SET name = :name, url = :url, city = :city, status = :status';
        if ($data['logo'] !== null) $sql .= ', logo = :logo';
        $sql .= ' WHERE id = :id';

        $params = [
            'id' => $id,
            'name' => $data['name'],
            'url' => $data['url'],
            'city' => $data['city'],
            'status' => $data['status'],
        ];
        if ($data['logo'] !== null) $params['logo'] = $data['logo'];

        $this->pdo->prepare($sql)->execute($params);

        $new = $this->find($id);
        $this->log($id, 'update', json_encode($old), json_encode($new));
    }

    private function log(int $id, string $action, ?string $old, ?string $new): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Captura o ID da sessão como inteiro
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks.bus_company_logs (bus_company_id, action, user_id, old_value, new_value) 
         VALUES (:id, :action, :user_id, :old, :new)'
        );

        $stmt->execute([
            'id'      => $id,
            'action'  => $action,
            'user_id' => $userId,
            'old'     => $old,
            'new'     => $new
        ]);
    }

    public function allNames(): array
    {
        $query = $this->pdo->query("SELECT name FROM tasks.bus_companies ORDER BY name ASC");
        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }


    public function getLogs(array $filters = []): array
    {
        $sql = "SELECT l.*, b.name as bus_name 
            FROM tasks.bus_company_logs l
            LEFT JOIN tasks.bus_companies b ON l.bus_company_id = b.id
            WHERE 1=1"; // O 1=1 facilita adicionar ANDs dinamicamente

        $params = [];

        // Filtro por ID do Log
        if (!empty($filters['id'])) {
            $sql .= " AND l.id = :id";
            $params['id'] = $filters['id'];
        }

        // Filtro por ID do Usuário
        if (!empty($filters['user_id'])) {
            $sql .= " AND l.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }

        // Filtro por ID da Viação (olha no log ou no JSON se necessário)
        if (!empty($filters['bus_id'])) {
            $sql .= " AND l.bus_company_id = :bus_id";
            $params['bus_id'] = $filters['bus_id'];
        }

        // Filtro por Nome da Viação (Busca no nome atual ou dentro do JSON old_value)
        if (!empty($filters['bus_name'])) {
            $sql .= " AND (b.name LIKE :bus_name OR l.old_value LIKE :bus_name_json)";
            $params['bus_name'] = "%" . $filters['bus_name'] . "%";
            $params['bus_name_json'] = "%" . $filters['bus_name'] . "%";
        }

        // Filtro por Ação (CREATE, UPDATE, DELETE)
        if (!empty($filters['action'])) {
            $sql .= " AND l.action = :action";
            $params['action'] = $filters['action'];
        }

        // Filtro por Data (converte para o formato do banco)
        if (!empty($filters['date'])) {
            $sql .= " AND DATE(l.created_at) = :date";
            $params['date'] = $filters['date'];
        }

        $sql .= " ORDER BY l.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function delete(int $id): bool
    {
        $company = $this->find($id);

        if ($company) {
            try {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $userId = $_SESSION['user_id'] ?? null;

                $sqlLog = "INSERT INTO tasks.bus_company_logs (bus_company_id, action, user_id, old_value, new_value, created_at) 
                       VALUES (:id, 'delete', :user_id, :old, NULL, NOW())";

                $stmtLog = $this->pdo->prepare($sqlLog);
                $stmtLog->execute([
                    'id'      => $id,
                    'user_id' => $userId,
                    'old'     => json_encode([
                        'id'   => $company->id,
                        'name' => $company->name,
                        'logo' => $company->logo
                    ])
                ]);

                $this->pdo->prepare("UPDATE tasks.bus_company_logs SET bus_company_id = NULL WHERE bus_company_id = :id")
                    ->execute(['id' => $id]);

            } catch (\Exception $e) {
            }

            $sql = "DELETE FROM tasks.bus_companies WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        }

        return false;
    }
}