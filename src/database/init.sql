SET NAMES utf8mb4;
USE tasks;

-- Apaga tudo para evitar erro de tabela fantasma
DROP TABLE IF EXISTS bus_company_logs;
DROP TABLE IF EXISTS bus_companies;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS tasks;

-- 1. Usuários
CREATE TABLE users (
                       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(255) NOT NULL,
                       email VARCHAR(191) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL,
                       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Viações
CREATE TABLE bus_companies (
                               id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                               name VARCHAR(255) NOT NULL,
                               url VARCHAR(255) NOT NULL,
                               city VARCHAR(100) NOT NULL,
                               status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
                               logo VARCHAR(255) NULL,
                               created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Logs
CREATE TABLE bus_company_logs (
                                  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                  bus_company_id INT UNSIGNED NOT NULL,
                                  action VARCHAR(20) NOT NULL,
                                  old_value TEXT NULL,
                                  new_value TEXT NULL,
                                  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tasks (Sua tabela original)
CREATE TABLE tasks (
                       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                       title VARCHAR(255) NOT NULL,
                       is_done TINYINT(1) NOT NULL DEFAULT 0,
                       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;