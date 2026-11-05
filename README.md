# cdp-platform
## Getting started

# Customer Data Platform (CDP)

A lightweight Customer Data Platform backend built with native PHP and MySQL. The application collects customer events, stores customer information, provides customer profiles, and supports customer segmentation.

## 1. Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Composer
- Postman

## 2. Setup Instructions

### Clone the repository

```bash
git clone https://gitlab.com/tours2019/cdp-platform.git
cd cdp-platform
```

### Install dependencies

```bash
composer install
```

### Configure the database

Create a MySQL database:

```sql
CREATE DATABASE cdp_test_4000;
```

Configure your database connection in `config/database.php`:

```php
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'cdp_test_4000',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];
```

Update these values according to your local MySQL configuration.

### Create database tables

Create the following tables:

- `customers`
- `events`
- `event_properties`

Configure primary keys, foreign keys, unique constraints, and indexes according to the database schema.

### Run database seeders

```bash
php database/seed.php
```

This command inserts sample customers and events for testing.

## 3. How to Run the Project

Start the PHP development server:

```bash
php -S localhost:8000 -t public
```

The API will be available at:

```text
http://localhost:8000
```

Use Postman or another HTTP client to send requests.

## 4. API Documentation

All endpoints return JSON responses.

### 4.1 Ingest an Event

**Endpoint**

```http
POST /api/events
```

**Headers**

```http
Content-Type: application/json
```

**Request body**

```json
{
    "email": "joel@test.com",
    "name": "Joel kevin",
    "event_type": "purchase",
    "properties": {
        "price": 1200,
        "currency": "EUR",
        "quantity": 1
    }
}
```

**Successful response (201)**

```json
{
    "success": true,
    "data": {
        "customer_id": 1,
        "event_id": 25
    }
}
```

The API creates a customer if the email does not exist. Otherwise, it updates the existing customer's name and associates the new event with that customer.

### 4.2 Get a Customer Profile

**Endpoint**

```http
GET /api/customers/{id}
```

**Example**

```http
GET /api/customers/1
```

Returns customer information, statistics, and the last 10 events.

**Example response**

```json
{
    "success": true,
    "data": {
        "customer": {
            "id": 1,
            "name": "Joel Kevin",
            "email": "joel@test.com"
        },
        "statistics": {
            "total_purchases": 2,
            "total_spend": 1500
        },
        "recent_events": []
    }
}
```

### 4.3 List Customers

**Endpoint**

```http
GET /api/customers
```

**Query parameters**

| Parameter | Description | Default |
|---|---|---|
| `page` | Page number | 1 |
| `limit` | Number of customers per page | 20 |

**Example**

```http
GET /api/customers?page=1&limit=10
```

Returns a paginated list of customers.

### 4.4 Customer Segmentation

**Endpoint**

```http
GET /api/segments/customers
```

**Query parameters**

| Parameter | Description |
|---|---|
| `min_purchases` | Minimum number of purchases |
| `min_spend` | Minimum total spending |

**Example**

```http
GET /api/segments/customers?min_purchases=2&min_spend=1000
```

Returns customers matching the specified purchase and spending criteria.

### 4.5 Dynamic Segmentation

**Endpoint**

```http
POST /api/segments/query
```

**Headers**

```http
Content-Type: application/json
```

**Request body**

```json
{
    "conditions": [
        {
            "event": "purchase",
            "property": "price",
            "operator": ">",
            "value": 100
        }
    ]
}
```

Supported operators:

- `=`
- `!=`
- `>`
- `<`
- `>=`
- `<=`

Multiple conditions can be provided. Conditions are combined using AND logic.

Returns customers matching the requested event conditions.

## 6. Running Tests

### Unit tests

Run the unit tests:

```bash
vendor/bin/phpunit tests/Unit
```

Unit tests use mocks to test application services independently of the database.


## 7. Architecture

The project follows a layered architecture inspired by MVC:

```text
app/
├── Core/
├── Controllers/
├── Middleware/
├── Repositories/
├── Services/

config/
database/
├── connection/
└── seeders/

public/
├── assets/
├── index.php
└── dashboard.php

routes/
tests/
├── Unit/
└── Integration/

```

### Main responsibilities

- **Controllers:** Handle HTTP requests and responses.
- **Services:** Contain business logic and validation.
- **Repositories:** Handle database operations using PDO.
- **Middleware:** Handle cross-cutting concerns such as API authentication.
- **Core:** Contains shared application components such as routing and constants.

## 8. Design Decisions and Tradeoffs

### Native PHP instead of a framework

The application uses native PHP to demonstrate backend fundamentals, including object-oriented programming, PDO, HTTP handling, and application architecture.

**Tradeoff:** More manual implementation is required compared with using a framework such as Laravel.

### MySQL relational database

Customers, events, and event properties are stored in separate tables.

**Reason:** This structure avoids unnecessary duplication and supports relational queries.

**Tradeoff:** Retrieving event properties requires joins, but indexes help improve query performance.

### Event properties stored separately

Event properties use a key-value structure in `event_properties`.

**Reason:** Different event types can have different properties without requiring new database columns for every possible property.


### PDO prepared statements

All dynamic values are passed through prepared statements.

**Reason:** This helps prevent SQL injection and separates SQL instructions from user-provided data.

### Database transactions

Customer creation, event insertion, and property insertion are performed in a transaction.

**Reason:** Either all related operations succeed, or the transaction is rolled back.

### EXISTS for dynamic segmentation

Dynamic segmentation uses SQL `EXISTS` conditions to check matching events and properties.

**Reason:** This avoids unnecessary duplicate customer rows when multiple events match.

**Tradeoff:** Query performance depends on appropriate indexes and the number of segmentation conditions.

### Pagination

Customer listing uses `LIMIT` and `OFFSET`.

**Reason:** It avoids returning the entire customer table in one request.

**Tradeoff:** Large OFFSET values can become inefficient; cursor-based pagination may be preferable for very large datasets.


## 9. Security Considerations

- Validate incoming API data.
- Use PDO prepared statements.
- Avoid exposing database credentials.
- Store API keys as secure hashes if API key authentication is enabled.
- Do not commit secrets or environment-specific credentials to Git.
- Return generic error messages to API clients and log technical details server-side.

## 10. Future Improvements

- Add API key authentication and permissions.
- Improve job queue concurrency and retry handling.
- Add more unit and integration tests.
- Improve segmentation query optimization.
- Add structured logging and monitoring.
- Support multi-currency purchase calculations.
- Introduce cursor-based pagination for large datasets.j

