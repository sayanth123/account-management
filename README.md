# RemitSo Account Management System

## Overview
RemitSo is a robust Laravel-based account management system designed for secure and efficient financial tracking. The system provides comprehensive features for user account creation, transaction management, and API-driven interactions.

## Prerequisites
- PHP 7.4 or 8.0
- Composer
- MySQL or PostgreSQL
- Git

## Project Setup

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/remitso-account-management.git
cd remitso-account-management
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
1. Copy `.env.example` to `.env`
2. Configure database settings in `.env`
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=remitso_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Database Setup
```bash
# Create database
mysql -u your_username -p
CREATE DATABASE remitso_db;
exit;

# Run migrations
php artisan migrate
```

## Authentication

### User Registration and Login
- `POST /register`: Create a new user account
  - Required: `name`, `email`, `password`
- `POST /login`: Authenticate user and obtain Sanctum token
  - Required: `email`, `password`

### Authentication Workflow
1. Register a user via `/register`
2. Login via `/login` to obtain authentication token
3. Include token in subsequent API requests via Bearer authentication

## API Endpoints

### Authenticated Routes (Requires Sanctum Token)

#### Accounts Management
- `POST /accounts`: Create a new account
  - Required: `account_name`, `account_type` (Personal/Business), `currency` (USD/EUR/GBP)
- `GET /accounts/{account_number}`: Retrieve specific account details
- `PUT /accounts/{account_number}`: Update account information
- `DELETE /accounts/{account_number}`: Close/delete account

#### Transactions
- `POST /transactions`: Record a new transaction
  - Required: `account_id`, `type` (Credit/Debit), `amount`
  - Optional: `description`
- `GET /transactions`: Retrieve transactions
  - Optional filters: `account_id`, `from` date, `to` date

## Testing

### Test Coverage

### Running Tests
```bash
# Run all tests
php artisan test
```


### Performance Metrics
   PASS  Tests\Unit\AccountTest
  ✓ example

   PASS  Tests\Unit\ExampleTest
  ✓ example

   PASS  Tests\Unit\LuhnHelperTest
  ✓ it generates a valid luhn compliant account number
  ✓ it detects an invalid luhn number

   PASS  Tests\Feature\AccountTest
  ✓ it creates an account successfully

   PASS  Tests\Feature\TransactionTest
  ✓ it creates a transaction successfully
  ✓ it fails transaction due to insufficient balance
  ✓ it processes a deposit successfully

  Tests:  8 passed
  Time:   0.32s

## Key Features
- UUID-based primary keys
- Luhn algorithm for account number generation
- Sanctum API authentication
- Soft delete support
- Transaction validation
- Overdraft prevention

## Security Considerations
- Passwords are hashed
- All account and transaction routes require authentication
- Transactions are processed within database transactions
- Input validation for all endpoints
