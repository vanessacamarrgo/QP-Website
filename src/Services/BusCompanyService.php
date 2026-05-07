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
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks.bus_company_logs (bus_company_id, action, old_value, new_value) 
             VALUES (:id, :action, :old, :new)'
        );
        $stmt->execute(['id' => $id, 'action' => $action, 'old' => $old, 'new' => $new]);
    }

    /** Busca apenas os nomes de todas as viações para o autocomplete. */
    public function allNames(): array
    {
        $query = $this->pdo->query("SELECT name FROM tasks.bus_companies ORDER BY name ASC");
        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }


    /** Busca o histórico completo de alterações */
    public function getLogs(): array
    {
        $stmt = $this->pdo->query("
        SELECT 
            l.*, 
            l.bus_company_id AS viação_id, 
            b.name AS viação_nome, 
            b.logo AS viação_logo
        FROM tasks.bus_company_logs l
        LEFT JOIN tasks.bus_companies b ON b.id = l.bus_company_id
        ORDER BY l.created_at DESC
    ");

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function delete(int $id): bool
    {
        //Busca os dados da viação enquanto ela ainda existe
        $company = $this->find($id);

        if ($company) {
            try {
                //Cria o log de 'delete' salvando ID, Nome e Logo no JSON
                $sqlLog = "INSERT INTO tasks.bus_company_logs (bus_company_id, action, old_value, new_value, created_at) 
                       VALUES (:id, 'delete', :old, NULL, NOW())";

                $stmtLog = $this->pdo->prepare($sqlLog);
                $stmtLog->execute([
                    'id'  => $id,
                    'old' => json_encode([
                        'id'   => $company->id,
                        'name' => $company->name,
                        'logo' => $company->logo
                    ])
                ]);

                // Desvincula o ID do log para permitir o DELETE
                $this->pdo->prepare("UPDATE tasks.bus_company_logs SET bus_company_id = NULL WHERE bus_company_id = :id")
                    ->execute(['id' => $id]);

            } catch (\Exception $e) {
                // Ignora erro no log e segue para a exclusão
            }

            //Deleta a viação da tabela principal
            $sql = "DELETE FROM tasks.bus_companies WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        }

        return false;
    }
}