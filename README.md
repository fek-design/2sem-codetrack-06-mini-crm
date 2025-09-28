# Mini CRM Application

A lightweight Customer Relationship Management (CRM) system built with PHP for managing customers, leads, and interactions.

## Features

- **Customer Management**: Create, edit, delete, and search customers
- **Lead Management**: Track leads from initial contact to conversion
- **Lead Conversion**: Convert qualified leads to customers automatically
- **Interaction History**: Record and track all communications
- **Dashboard**: View metrics and recent activity
- **Authentication**: Secure login system with CSRF protection

## Technology Stack

- PHP 8.1+ with custom MVC framework
- SQLite database with migrations
- HTML5, CSS3, vanilla JavaScript
- RESTful routing

## Installation

### Prerequisites
- PHP 8.1 or higher
- [Laravel Herd](https://herd.laravel.com/) (recommended) or another PHP server
- Composer

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd 2sem-codetrack-06-mini-crm
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Run database migrations**
   ```bash
   php bin/migrate.php
   ```

4. **Start the server**
   
   **With Laravel Herd:**
   - Herd will automatically serve the project
   - Access at the local domain Herd provides
   
   **Alternative (built-in PHP server):**
   ```bash
   php -S localhost:8000 -t public
   ```

## Usage

- **Dashboard**: `/dashboard` - View metrics and recent activity
- **Customers**: `/customers` - Manage customer records
- **Leads**: `/leads` - Manage leads and convert to customers
- **Authentication**: Login required for all features

## Development

The project follows MVC architecture with:
- Controllers in `src/Controllers/`
- Models in `src/Models/`
- Views in `views/`
- Database migrations in `database/migrations/`

Run migrations after database changes:
```bash
php bin/migrate.php
```
