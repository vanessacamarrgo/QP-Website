#  Projeto Quero Passagem

> Mini-framework PHP 8.4 com arquitetura inspirada no Laravel para gestão de empresas de transporte rodoviário, com design system de alta fidelidade baseado na Quero Passagem.

---

##  Índice

- [Visão Geral](#-visão-geral)
- [Stack Tecnológica](#-stack-tecnológica)
- [Arquitetura do Sistema](#-arquitetura-do-sistema)
- [Infraestrutura Docker](#-infraestrutura-docker)
- [Interface e UX](#-interface-e-experiência-do-utilizador-ux)
- [API REST JSON](#-API-REST-JSON)
- [CRUD e Formulários](#-crud-e-formulários)
- [Como Executar](#-como-executar)
- [Testes de API](#-testes-de-api)
- [Banco de Dados](#-banco-de-dados)
- [O que foi Aprendido](#-o-que-foi-aprendido-e-implementado)

---

##  Visão Geral

Este projeto é um **mini-framework PHP 8.0** desenvolvido do zero, com separação de responsabilidades clara e arquitetura em camadas inspirada no Laravel. A aplicação gerencia entidades de `BusCompany` (Viações), oferecendo:

- ️ **Painel Administrativo** completo com CRUD
-  **Homepage pública** fiel ao design da Quero Passagem
-  **Histórico de Alterações** (audit log) automático
-  **Ambiente Dockerizado** com Apache + MySQL 8

---

##  Stack Tecnológica

| Camada | Tecnologia                                       |
|---|--------------------------------------------------|
| **Linguagem** | PHP 8.0                                          |
| **Servidor Web** | Apache 2 com `mod_rewrite`                       |
| **Banco de Dados** | MySQL 8.0                                        |
| **Containerização** | Docker + Docker Compose                          |
| **Autoloading** | PSR-4 via Composer                               |
| **Frontend** | HTML5, CSS3 (Grid + Flexbox), JavaScript Vanilla |
| **Fontes** | Google Fonts – Sora, Inter                           |

---

##  Arquitetura do Sistema

### Fluxo de Execução (Request Lifecycle)

```
HTTP Request
     │
     ▼
src/public/index.php          ← Front Controller
     │  Inicia sessão, carrega autoload e db.php
     ▼
src/Core/Router.php           ← Roteador customizado
     │  Match de método (GET/POST) + URI com regex nomeada
     │  Ex: /bus-companies/{id}/edit → ['id' => 42]
     ▼
App\Controllers\BusCompanyController
     │  Recebe parâmetros, chama Service, renderiza View
     ▼
App\Services\BusCompanyService
     │  Toda lógica de negócio e acesso ao PDO
     │  Grava audit logs automaticamente
     ▼
App\Models\BusCompany         ← Value Object imutável
     │  Hydration via BusCompany::fromRow(array $row)
     ▼
src/Core/View::render()       ← Sistema de templates
     │  ob_start() + require + layout wrapper
     ▼
HTTP Response (HTML ou JSON)
```

### Organização de Namespaces (PSR-4)

```
src/
├── public/
│   ├── index.php          # Front Controller (web)
│   ├── api.php            # Front Controller (API)
│   ├── app.css            # Estilos do painel ADM
│   ├── styles.css         # Estilos da homepage pública
│   └── script.js / home.js
│
├── Controllers/
│   ├── BusCompanyController.php   # App\Controllers
│   └── Api/
│       └── TaskApiController.php  # App\Controllers\Api
│
├── Services/
│   └── BusCompanyService.php      # App\Services
│
├── Models/
│   └── BusCompany.php             # App\Models
│
├── Core/
│   ├── Router.php                 # App\Core
│   └── View.php                   # App\Core
│
├── database/
│   ├── db.php                     # Singleton PDO
│   └── init.sql                   # Schema + seed inicial
│
├── routes/
│   ├── web.php                    # Rotas HTML
│   └── api.php                    # Rotas JSON
│
└── views/
    ├── _layout.php                # Layout base (painel ADM)
    ├── home.php                   # Homepage pública standalone
    ├── index.php                  # Listagem de viações
    ├── create.php                 # Formulário de criação
    ├── edit.php                   # Formulário de edição
    ├── logs.php                   # Histórico de alterações
    └── partials/
        └── form.php               # Partial reutilizável
```

### O Roteador (`App\Core\Router`)

O `Router` converte padrões de rota com `{placeholders}` em expressões regulares com grupos nomeados, suportando tipagem automática de parâmetros:

```php
// Registro de rota
$router->get('/bus-companies/{id}/edit', [BusCompanyController::class, 'edit']);

// Internamente converte para regex:
// #^/bus-companies/(?P<id>\d+)/edit$#

// O dispatch extrai e converte automaticamente:
// $params = ['id' => 42]  ← cast para int via ctype_digit()
```

Suporte a rotas `GET` e `POST`, com fallback JSON automático para URIs `/api/*` não encontradas.

### O View Engine (`App\Core\View`)

Sistema de templates com dois modos de renderização:

```php
// Painel ADM — usa _layout.php como wrapper
View::render('index', ['title' => 'Viações', 'companies' => $list]);

// Homepage pública — arquivo standalone sem layout compartilhado
View::renderHome('home', ['companies' => $list]);
```

Suporte a **Flash Messages** via `$_SESSION`, implementando o padrão PRG (Post/Redirect/Get):

```php
View::flash('success', 'Viação criada com sucesso.');
View::redirect('/bus-companies');
```

### O Model (`App\Models\BusCompany`)

Value Object imutável com factory method para hidratação a partir de rows do PDO:

```php
final class BusCompany
{
    public function __construct(
        public int $id,
        public string $name,
        public string $url,
        public string $city,
        public string $status,
        public ?string $logo,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromRow(array $row): self { ... }
}
```

---

##  Infraestrutura Docker

### Arquitetura dos Containers

```
┌─────────────────────────────────────────────────────┐
│                   Docker Network                     │
│                                                      │
│   ┌──────────────────┐    ┌──────────────────────┐  │
│   │   task_app_app   │    │    task_app_db        │  │
│   │                  │    │                       │  │
│   │  PHP 8.4-Apache  │───▶│    MySQL 8.0          │  │
│   │  port 8081:80    │    │    port 3308:3306     │  │
│   │                  │    │    TZ: UTC-3          │  │
│   │  DocumentRoot:   │    │                       │  │
│   │  /src/public/    │    │  Volume persistente:  │  │
│   └──────────────────┘    │  task_mysql_data      │  │
│                            └──────────────────────┘  │
└─────────────────────────────────────────────────────┘
```

### Dockerfile

```dockerfile
FROM php:8.4-apache

# Composer 2 via multi-stage copy
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Extensões PDO para MySQL
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# DocumentRoot apontando para /src/public (estilo Laravel)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/src/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' \
        /etc/apache2/apache2.conf

WORKDIR /var/www/html
```

### docker-compose.yml

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: task_app_app
    ports:
      - "8081:80"
    command: >
      sh -c "if [ ! -f vendor/autoload.php ]; then
        composer install --no-interaction --prefer-dist;
      else
        composer dump-autoload --no-interaction --optimize;
      fi && apache2-foreground"
    volumes:
      - ./:/var/www/html
    environment:
      DB_HOST: db
      DB_NAME: tasks
      DB_USER: app
      DB_PASSWORD: app123
    depends_on:
      - db

  db:
    image: mysql:8.0
    container_name: task_app_db
    restart: unless-stopped
    command: --default-time-zone='-03:00'
    environment:
      MYSQL_DATABASE: tasks
      MYSQL_USER: app
      MYSQL_PASSWORD: app123
      MYSQL_ROOT_PASSWORD: root123
    ports:
      - "3308:3306"
    volumes:
      - task_mysql_data:/var/lib/mysql
      - ./src/database/init.sql:/docker-entrypoint-initdb.d/init.sql:ro

volumes:
  task_mysql_data:
```

**Destaques da infraestrutura:**
-  O `init.sql` é montado como **read-only** (`ro`) — protege o arquivo original de modificações pelo container
-  `--default-time-zone='-03:00'` garante que `CURRENT_TIMESTAMP` e `updated_at` respeitem o horário de Brasília
-  O `command` do app verifica se o Composer já rodou antes de instalar novamente, tornando restarts mais rápidos
-  Volume nomeado `task_mysql_data` garante **persistência dos dados** entre restarts

---

##  Banco de Dados

### Diagrama Entidade-Relacionamento

```
┌─────────────────────────────────────────┐
│              bus_companies              │
├──────────────┬──────────────────────────┤
│ id           │ INT (PK, AUTO_INCREMENT) │
│ name         │ VARCHAR(255) NOT NULL    │
│ url          │ VARCHAR(255)             │
│ city         │ VARCHAR(100)             │
│ status       │ ENUM('active','inactive')│
│ logo         │ VARCHAR(255) NULL        │
│ created_at   │ TIMESTAMP                │
│ updated_at   │ TIMESTAMP (ON UPDATE)    │
└──────────────┴──────────────────────────┘
          │ 1
          │
          │ N
┌─────────────────────────────────────────┐
│           bus_company_logs              │
├──────────────┬──────────────────────────┤
│ id           │ INT (PK, AUTO_INCREMENT) │
│ bus_company_id│ INT (FK, NULLABLE)      │
│ action       │ VARCHAR(20)              │
│ old_value    │ TEXT (JSON serializado)  │
│ new_value    │ TEXT (JSON serializado)  │
│ created_at   │ TIMESTAMP                │
└──────────────┴──────────────────────────┘
```

>  `bus_company_id` é **nullable** no log de exclusão para preservar o histórico mesmo após a remoção do registro pai. O nome da viação excluída é salvo diretamente no campo `old_value`.

---

##  Interface e Experiência do Utilizador (UX)

- **Fundo:** Cinza claro `#F4F6F9` — cria profundidade sem peso visual
- **Cards:** Brancos `#FFFFFF` com `border-radius: 12px` e `box-shadow: 0 2px 12px rgba(0,0,0,0.07)` — fazem o conteúdo "flutuar" sobre o fundo
- **Tipografia:** Hierarquia clara com `font-family: 'Sora', sans-serif` para títulos e `Arial` para conteúdo tabular

```css
:root {
    --blue-dark:  #1a2e6e;
    --blue-main:  #1a2e6e;
    --yellow:     #f5c518;
    --text-dark:  #1a1a1a;
    --text-gray:  #46484d;
    --bg-light:   #f5f7fa;
}
```

### Seção de Rotas — CSS Grid + Flexbox

A seção "Top 15 Trechos" usa **CSS Grid de 3 colunas** para o layout macro e **Flexbox** interno para alinhar o ícone de seta azul com os nomes das cidades:

```css
/* Grid de 3 colunas para os cards de rotas */
.routes-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}

/* Flexbox nas células para alinhar cidade + ícone */
.routes-grid td {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}
```

### Banner Promocional — Controle de Largura Máxima

Para evitar distorção em monitores ultrawide, o banner usa `max-width` com centralização automática:

```css
/* Garante aspeto de "zoom afastado" em telas grandes */
.banner-content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 37px;
}
```

### Componentes Interativos

- **Autocomplete:** Campo de busca por nome com lista dropdown, navegação via teclado (`↑↓ Enter Esc`) e fechamento ao clicar fora
- **Modal de Confirmação:** Overlay com animação `modalIn` + `shake` para deleção, com propagação do URL de destino via `form.action`
- **Swap de Cidades:** Botão flutuante com rotação CSS (`rotate(180deg)`) para inverter origem/destino no formulário de busca
- **FAQ Accordion:** Toggle `aria-expanded` + `max-height` animado para abertura suave das respostas

---

##  API REST JSON

A API está disponível no prefixo `/api` e suporta exclusivamente o método `GET`.

### Rotas Disponíveis

| Método | Endpoint | Descrição |
|---|---|---|
| `GET` | `/api` | Lista todas as tasks |
| `GET` | `/api/tasks` | Lista todas as tasks (alias) |
| `GET` | `/api/tasks/{id}` | Retorna uma task pelo ID |

### Exemplos de Resposta

**`GET /api/tasks` — Sucesso (200)**
```json
{
  "ok": true,
  "count": 2,
  "data": [
    {
      "id": 1,
      "title": "Estudar roteamento",
      "description": "Implementar um Router simples (GET/POST + params).",
      "is_done": false,
      "created_at": "2026-01-15 10:00:00",
      "updated_at": null
    }
  ]
}
```

**`GET /api/tasks/99` — Not Found (404)**
```json
{
  "ok": false,
  "message": "BusCompany not found."
}
```

**Método não permitido (405)**
```json
{
  "ok": false,
  "message": "Method not allowed. Use GET."
}
```

---

##  CRUD e Formulários

### Rotas Web Completas

```php
// Homepage pública
GET  /                              → BusCompanyController::home()

// Painel ADM — Listagem com filtros
GET  /bus-companies                 → BusCompanyController::index()

// Histórico de audit logs
GET  /bus-companies/logs            → BusCompanyController::logs()

// Criação
GET  /bus-companies/create          → BusCompanyController::create()
POST /bus-companies                 → BusCompanyController::store()

// Edição
GET  /bus-companies/{id}/edit       → BusCompanyController::edit()
POST /bus-companies/{id}/update     → BusCompanyController::update()

// Exclusão
POST /bus-companies/{id}/delete     → BusCompanyController::destroy()
```

### Validação e Feedback Visual

```php
private function validate(array $data): array
{
    $errors = [];
    if ($data['name'] === '') {
        $errors[] = 'O nome é obrigatório.';
    }
    if ($data['url'] !== '' && !filter_var($data['url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'URL inválida.';
    }
    return $errors;
}
```

-  Se válido → PRG: `flash('success', ...)` + `redirect('/bus-companies')`
-  Se inválido → Re-renderiza o formulário com `$errors` e `$old` (dados preenchidos preservados)

### Upload de Logo

```php
private function handleUpload(): ?string
{
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

    $fileName = uniqid() . '_' . basename($_FILES['logo']['name']);
    move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName);

    return $fileName; // Apenas o nome; o caminho é construído na view
}
```

### Audit Log Automático

Toda operação de escrita gera um log com snapshot JSON do estado anterior e posterior:

```php
// Em BusCompanyService::update()
$old = $this->find($id);           // snapshot antes
$this->pdo->prepare($sql)->execute($params);
$new = $this->find($id);           // snapshot depois

$this->log($id, 'update', json_encode($old), json_encode($new));
```

---

##  Como Executar

### Pré-requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado e em execução
- Portas `8081` e `3308` disponíveis na máquina host

### Passo a Passo

```bash
# 1. Clone o repositório
git clone <url-do-repositorio>
cd

# 2. Suba os containers (primeira execução instala dependências via Composer)
docker compose up -d --build

# 3. Aguarde ~30 segundos para o MySQL inicializar
# Verifique os logs se necessário:
docker compose logs db

# 4. Acesse a aplicação
# Homepage pública:  http://localhost:8081
# Painel ADM:        http://localhost:8081/bus-companies
# API:               http://localhost:8081/api/tasks
```

### Comandos Úteis

```bash
# Parar os containers
docker compose down

# Parar E remover os dados do banco (volume)
docker compose down -v

# Ver logs em tempo real
docker compose logs -f app

# Acessar o shell do container
docker exec -it task_app_app bash

# Acessar o MySQL diretamente
docker exec -it task_app_db mysql -u app -papp123 tasks
```

---

## Testes de API

Crie um arquivo `requests.http` na raiz do projeto para testar a API diretamente no VS Code (extensão REST Client) ou PhpStorm:

```http
### Listar todas as tasks
GET http://localhost:8081/api/tasks
Accept: application/json

###

### Buscar task por ID
GET http://localhost:8081/api/tasks/1
Accept: application/json

###

### Task inexistente (deve retornar 404)
GET http://localhost:8081/api/tasks/999
Accept: application/json

###

### Método não permitido (deve retornar 405)
POST http://localhost:8081/api/tasks
Content-Type: application/json
```

---

##  O que foi Aprendido e Implementado

### Separação de Responsabilidades (SoC)

Cada camada tem uma única responsabilidade bem definida:

| Camada | Responsabilidade |
|---|---|
| `Router` | Apenas rotear — nenhuma lógica de negócio |
| `Controller` | Orquestrar request/response — sem SQL |
| `Service` | Lógica de negócio e acesso a dados |
| `Model` | Representar a entidade — apenas estrutura |
| `View` | Renderizar HTML — sem processamento |

### Padrão Post/Redirect/Get (PRG)

Implementado em todas as operações de escrita para evitar reenvio de formulários ao atualizar a página:

```
POST /bus-companies         ← Usuário envia formulário
     │
     ├── Validação falhou → render('create', ['errors' => ...])
     │
     └── Validação OK    → flash('success', ...) → redirect('/bus-companies')
                                                          │
                                                    GET /bus-companies   ← Nova request limpa
```

###  CSS Moderno — Grid e Flexbox

- **Grid** para layouts macro (seções de 3+ colunas)
- **Flexbox** para alinhamento micro (ícone + texto, botões, filtros)
- **CSS Custom Properties** para consistência do design system
- **Transitions e Keyframes** para micro-interações (hover, modal, FAQ)

###  Boas Práticas de Segurança

- `htmlspecialchars()` em todos os outputs dinâmicos (XSS prevention)
- Prepared Statements PDO em todas as queries (SQL Injection prevention)
- Validação server-side antes de qualquer persistência
- Upload com nome aleatório via `uniqid()` (evita sobrescrita e path traversal)

###  Audit Logging

Sistema de rastreabilidade completo que registra o estado JSON completo antes e depois de cada alteração, com diff visual na tela de logs.

---

##  Estrutura de Pastas Completa

```
├── docker-compose.yml
├── Dockerfile
├── composer.json
├── requests.http
└── src/
    ├── public/                 ← DocumentRoot do Apache
    │   ├── index.php
    │   ├── api.php
    │   ├── app.css
    │   ├── styles.css
    │   ├── script.js
    │   ├── home.js
    │   ├── .htaccess
    │   └── uploads/            ← Logos das viações (gerado em runtime)
    ├── Controllers/
    │   ├── BusCompanyController.php
    │   └── Api/
    │       └── TaskApiController.php
    ├── Services/
    │   └── BusCompanyService.php
    ├── Models/
    │   └── BusCompany.php
    ├── Core/
    │   ├── Router.php
    │   └── View.php
    ├── database/
    │   ├── db.php
    │   └── init.sql
    ├── routes/
    │   ├── web.php
    │   └── api.php
    └── views/
        ├── _layout.php
        ├── home.php
        ├── index.php
        ├── create.php
        ├── edit.php
        ├── logs.php
        └── partials/
            └── form.php
```

---


Este projeto foi desenvolvido como estudo de arquitetura PHP e design system.

---

*Desenvolvido com PHP 8.0 · Docker · MySQL 8 · Inspirado no Laravel e na Quero Passagem*