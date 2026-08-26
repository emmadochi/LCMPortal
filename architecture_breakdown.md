# Church Reporting & Administrative Portal Architecture

This document provides a comprehensive, visual, and conceptual breakdown of the architecture, data models, and request lifecycle for the **Church Reporting & Administrative Portal**.

---

## 1. High-Level System Architecture

The application is built on a custom, lightweight **MVC (Model-View-Controller)** engine implemented in PHP 8. It leverages a front controller pattern, custom routing engine, middleware system, and standard relational database access layers.

```mermaid
graph TD
    %% Styling
    classDef client fill:#f9f9f9,stroke:#333,stroke-width:2px;
    classDef core fill:#e1f5fe,stroke:#0288d1,stroke-width:2px;
    classDef mvc fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px;
    classDef storage fill:#fff3e0,stroke:#ef6c00,stroke-width:2px;

    Client["Client Browser <br/> (HTML5 / Bootstrap 5 / AJAX)"]:::client

    subgraph "Public Entry Point"
        Index["public/index.php <br/> (Bootstrap & Autoloader)"]:::core
    end

    subgraph "Core MVC Engine (app/core)"
        App["App Core <br/> (App.php)"]:::core
        Router["Router <br/> (Router.php)"]:::core
        Request["Request <br/> (Request.php)"]:::core
        Response["Response <br/> (Response.php)"]:::core
        Session["Session <br/> (Session.php)"]:::core
    end

    subgraph "Application Layer (app/)"
        Middleware["Middleware <br/> (Auth, CSRF, Roles)"]:::mvc
        Controllers["Controllers <br/> (extends BaseController)"]:::mvc
        Models["Models <br/> (extends BaseModel)"]:::mvc
        Views["Views & Layouts <br/> (PHP Templates)"]:::mvc
    end

    subgraph "Database Layer"
        DBConnection["Database Wrapper <br/> (Database.php Singleton)"]:::storage
        MySQL["MySQL Database <br/> (Prepared Statements)"]:::storage
    end

    %% Routing / Control Flow Connections
    Client -->|HTTP Request| Index
    Index -->|Initialize & Run| App
    App -->|Load routes/web.php & Dispatch| Router
    Router -->|Read Query & Input| Request
    Router -->|Verify Rules| Middleware
    Middleware -->|Authorize / CSRF OK| Controllers
    Controllers -->|Read/Write Data| Models
    Controllers -->|Get Session State| Session
    Controllers -->|Render Data| Views
    Views -->|Send HTML/JSON| Response
    Response -->|HTTP Response| Client

    %% Database connections
    Models -->|Query builder / Prepared statements| DBConnection
    DBConnection -->|Connection instance| MySQL
```

---

## 2. The Request-Response Lifecycle

Every HTTP request undergoes a structured routing, middleware filtration, and execution flow before returning a response to the client.

```mermaid
sequenceDiagram
    autonumber
    actor User as Client (Browser)
    participant Index as public/index.php
    participant App as app/core/App
    participant Router as app/core/Router
    participant MW as Middleware (e.g. CSRF, Auth)
    participant Ctrl as Controller (e.g. ReportController)
    participant Model as Model (e.g. Report)
    participant View as View (Layout + View Content)

    User->>Index: Sends HTTP GET /reports
    activate Index
    Note over Index: Autoloads files via Composer<br/>Loads env config & sets timezone
    Index->>App: new App() -> run()
    activate App
    App->>Router: dispatch()
    activate Router
    Note over Router: Reads defined routes from routes/web.php
    Router->>MW: handle()
    activate MW
    Note over MW: Verifies user session<br/>Checks CSRF tokens
    MW-->>Router: next() (Authorized)
    deactivate MW

    Router->>Ctrl: executeHandler('ReportController@index')
    activate Ctrl
    Ctrl->>Model: findAll(['status' => 'pending'])
    activate Model
    Note over Model: Prepares and executes SQL query<br/>via Database (mysqli wrapper)
    Model-->>Ctrl: Array of reports
    deactivate Model
    
    Ctrl->>View: render('reports/index', $data)
    activate View
    Note over View: Extracts variables<br/>Buffers view output<br/>Wraps within layouts/admin.php
    View-->>Ctrl: Rendered HTML string
    deactivate View
    
    Ctrl-->>Router: Response output sent (echo HTML)
    deactivate Ctrl
    Router-->>User: Returns 200 OK HTML payload
    deactivate Router
    deactivate App
    deactivate Index
```

---

## 3. Core Database & Model Relationships (ERD)

The portal supports dynamic multi-department reporting structures. The diagram below represents the relationship between users, units, leadership assignments (directors), reports, and associated metadata.

```mermaid
erDiagram
    CHURCHES ||--o{ UNITS : "owns"
    CHURCHES ||--o{ USERS : "contains"
    
    USERS ||--o{ UNIT_USER : "belongs to"
    UNITS ||--o{ UNIT_USER : "has members"

    USERS ||--o{ UNIT_DIRECTORS : "leads"
    UNITS ||--o{ UNIT_DIRECTORS : "led by"
    
    UNITS ||--o{ REPORTS : "submits"
    USERS ||--o{ REPORTS : "authored by"
    
    REPORTS ||--o{ REPORT_FILES : "contains"
    
    UNITS ||--o{ ATTENDANCE : "tracks"
    UNITS ||--o{ FINANCE_RECORDS : "records"
    UNITS ||--o{ PROJECTS : "executes"
    
    CHURCHES {
        int id PK
        string name
        string address
        timestamp created_at
    }

    USERS {
        int id PK
        int church_id FK
        string username
        string email
        string password_hash
        string role "admin | pastor | director | officer | standard"
        timestamp created_at
    }

    UNITS {
        int id PK
        int church_id FK
        string name
        string description
        timestamp created_at
    }

    UNIT_USER {
        int id PK
        int unit_id FK
        int user_id FK
        string role_in_unit
    }

    UNIT_DIRECTORS {
        int id PK
        int unit_id FK
        int user_id FK
        timestamp assigned_at
    }

    REPORTS {
        int id PK
        int unit_id FK
        int user_id FK
        string report_type "weekly | event | outreach | media | logistics"
        string title
        text content
        date reporting_date
        timestamp created_at
    }

    REPORT_FILES {
        int id PK
        int report_id FK
        string file_path
        string file_type
        timestamp uploaded_at
    }
```

---

## 4. Class Hierarchy & Architectural Design Patterns

The system relies on solid OOP patterns to separate concerns, enforce DRY principles, and ensure modularity:

*   **Singleton Pattern (`Database`, `Session`)**: Ensures only one active connection to the MySQL server and one PHP session instance exist at a time.
*   **Front Controller Pattern (`App`, `Router`)**: All client requests are funneled through a single file (`index.php`), ensuring uniform logging, configuration loading, security handling, and route dispatching.
*   **Base Pattern (`BaseModel`, `BaseController`)**: 
    *   `BaseModel` abstracts SQL operations (`find`, `findAll`, `create`, `update`, `delete`, `paginate`, `count`) using MySQLi prepared statements to automatically avoid SQL injection.
    *   `BaseController` implements custom output rendering (layout engines), redirection helpers, authorization logic, and payload validators.

```mermaid
classDiagram
    class Database {
        -static Database instance
        -mysqli connection
        +static getInstance()
        +prepare()
        +query()
    }
    
    class Session {
        -static Session instance
        +static getInstance()
        +get(key)
        +set(key, value)
        +hasPermission(perm)
    }

    class BaseModel {
        <<abstract>>
        #string table
        #string primaryKey
        #array fillable
        +Database db
        +find(id)
        +findAll(conditions)
        +create(data)
        +update(id, data)
        +delete(id)
        +paginate(page, perPage)
    }
    
    class User {
        +verifyPassword(password)
        +getAssignedUnits()
        +isDirectorOf(unitId)
    }
    
    class Unit {
        +getMembers()
        +getDirectors()
        +getReports()
    }

    class BaseController {
        <<abstract>>
        #Request request
        #Response response
        #Session session
        #render(view, data)
        #json(data, status)
        #redirect(url)
        #authorize(permission)
    }

    class ReportController {
        +index()
        +create()
        +store()
        +show(id)
    }

    BaseModel <|-- User
    BaseModel <|-- Unit
    BaseController <|-- ReportController
    
    BaseModel --> Database : "uses"
    BaseController --> Session : "uses"
```

---

## 5. Security Protocols Implemented

To safeguard ecclesiastical and financial records, the application integrates multiple layers of protection:

1.  **Strict Routing & Middleware**: Unauthenticated users are redirected to `/login` via the `AuthMiddleware`, and role restrictions are handled uniformly by checking the user session in `RoleMiddleware` or specific controller authorization points.
2.  **CSRF (Cross-Site Request Forgery) Tokens**: Built-in verification triggers on all mutate requests (POST, PUT, DELETE) through `CSRFMiddleware`.
3.  **Prepared Statements**: The core `BaseModel` requires all dynamic queries to bind parameter variables, completely neutralizing SQL Injection (SQLi) vulnerabilities.
4.  **XSS Sanitization & File Upload Safety**: All incoming request arguments are sanitized, and the file upload utility checks file extensions and MIME headers to prevent remote code execution.
