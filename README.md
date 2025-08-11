# ERP Shift Scheduling and Resource Allocation API

A Laravel 11-based RESTful API for managing employee shift scheduling and resource allocation in an ERP system.  
This project includes concurrency-safe shift requests, resource slot reservation, and automatic slot release for unapproved requests.

---

## Features

- **Shift Scheduling:** Managers define shifts with start/end times and max employees/resources per shift.
- **Resource Allocation:** Employees request shift slots with concurrency control to avoid overbooking.
- **Auto Release:** Unapproved requests automatically expire after 5 minutes, freeing reserved slots.
- **No external concurrency tools:** Uses MySQL row-level locking and custom logic to handle concurrency.
- **Overlapping shift validation:** Prevent employees from joining overlapping shifts.
- **Repository design pattern:** Clean separation of data access and business logic.
- **Feature test:** Validates the entire flow including concurrency handling.

---

## Concurrency Solution Overview

To prevent race conditions during simultaneous employee shift requests, the API employs a **database transaction with row-level locking** using MySQL's `SELECT ... FOR UPDATE`:

- When an employee requests a shift, the system starts a transaction and locks the shift record.
- If there is enough capacity (based on max employees), the request is stored as **pending**.
- If the shift is full, the request is rejected.
- Pending requests are automatically rejected after 5 minutes (via a scheduled task), releasing reserved slots.
- Overlapping shifts are checked before allowing a request.

This approach ensures that concurrent requests are processed safely without over-allocating resources.

---

## Setup & Run Instructions

### Requirements

- PHP 8.2+
- Composer
- MySQL or compatible relational database
- Laravel 11
- (Optional) `php artisan serve` for local development

### Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/MOohamedMahfouz/shift_scheduling_task.git
    cd shift_scheduling_task
    ```

2. Install dependencies:
    ```bash
    composer install
    ```

3. Configure your `.env` file with database credentials and other settings.

4. Run database migrations:
    ```bash
    php artisan migrate
    ```

5. Seed sample data:
    ```bash
    php artisan db:seed
    ```

6. Start the development server:
    ```bash
    php artisan serve
    ```

### Running Tests

- To run feature and unit tests (including concurrency tests), use:
    ```bash
    php artisan test
    ```

> **Note:**  
> For concurrency tests that spawn multiple parallel requests, a running Laravel server (`php artisan serve`) is required.  
> Make sure the test database is persistent and not using in-memory SQLite, to ensure spawned processes can share the same DB state.
