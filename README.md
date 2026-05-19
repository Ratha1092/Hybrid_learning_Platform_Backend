# Web-Based Hybrid Learning Platform

A comprehensive learning management system built with Laravel, featuring admin panels, course management, analytics, and more.

## Technologies & Packages Used

### Core Framework
- **Laravel 12** - PHP web framework
- **PHP 8.2+** - Server-side scripting language

### Database
- **PostgreSQL** (configured) or **SQLite** (default) - Database systems
- **Laravel Eloquent ORM** - Database abstraction layer

### Frontend & Build Tools
- **Node.js & npm** - JavaScript runtime and package manager
- **Vite** - Fast build tool and development server
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **PostCSS** - CSS processing tool
- **Autoprefixer** - CSS vendor prefixing

### Admin & UI
- **Filament 5.3** - Admin panel builder for Laravel
- **Laravel Breeze** - Simple authentication starter kit

### Key Packages
- **Laravel Horizon 5.45** - Queue management dashboard
- **Laravel Sanctum 4.3** - API authentication for SPAs
- **Spatie Laravel Permission 7.2** - Role-based permissions
- **Spatie Laravel Activity Log 4.12** - Activity logging
- **Spatie Laravel Media Library 11.21** - File management
- **Laravel Tinker** - REPL for Laravel

### Development Tools
- **Laravel Debugbar** - Debugging toolbar
- **Laravel IDE Helper** - IDE autocompletion
- **Laravel Pail** - Log file reader
- **Laravel Pint** - Code style fixer
- **PHPUnit** - Testing framework
- **Laravel Sail** - Docker development environment

## Prerequisites

Before running this project, ensure you have the following installed:

### Required Software

#### PHP 8.2 or higher
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-pgsql php8.2-sqlite3

# CentOS/RHEL/Fedora
sudo dnf install php php-cli php-common php-mysql php-zip php-gd php-mbstring php-curl php-xml php-bcmath php-pgsql php-sqlite3

# Verify installation
php --version
```

#### Composer - PHP dependency manager
```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Or using package manager (Ubuntu/Debian)
sudo apt install composer

# Verify installation
composer --version
```

#### Node.js 18+ & npm
```bash
# Using NodeSource repository (recommended)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Or using Node Version Manager (nvm)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
source ~/.bashrc
nvm install 18
nvm use 18

# Verify installation
node --version
npm --version
```

#### Database: PostgreSQL (recommended) or SQLite
```bash
# PostgreSQL
sudo apt install postgresql postgresql-contrib
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Create database and user
sudo -u postgres psql
CREATE DATABASE learning_platform;
CREATE USER your_username WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE learning_platform TO your_username;
\q

# SQLite (simpler alternative - no installation needed, comes with PHP)
# Just ensure php8.2-sqlite3 is installed (included above)
```

### PHP Extensions
The required PHP extensions are installed with the PHP installation commands above. To verify:
```bash
php -m | grep -E "(pdo|pdo_pgsql|pdo_sqlite|mbstring|openssl|tokenizer|xml|ctype|json|bcmath|curl)"
```

### Optional (for full functionality)

#### Redis - For caching and queues
```bash
# Ubuntu/Debian
sudo apt install redis-server
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Verify
redis-cli ping
```

#### Git - Version control
```bash
sudo apt install git
git --version
```

## Installation & Setup

### Option 1: Manual Setup (Recommended for Development)

### 1. Clone the Repository
```bash
git clone <repository-url>
cd Web_Based_Hybrid_Learning_Platform
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy the environment file and configure it:
```bash
cp .env.example .env
```

Edit `.env` file with your settings:
```env
APP_NAME="Web-Based Hybrid Learning Platform"
APP_ENV=local
APP_KEY=  # Will be generated in next step
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=pgsql  # or sqlite for simpler setup
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=learning_platform
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Redis (optional, for queues and caching)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Database Setup

#### For PostgreSQL:
```bash
# Ensure PostgreSQL is running
sudo systemctl status postgresql

# Run migrations
php artisan migrate
```

#### For SQLite (simpler setup):
```bash
# Set in .env: DB_CONNECTION=sqlite
# Create the database file
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### 6. Install Node.js Dependencies
```bash
npm install
```

### 7. Build Frontend Assets
```bash
# For production build
npm run build

# OR for development with hot reload
npm run dev
```

### 8. Seed Database (Optional)
```bash
php artisan db:seed
```

### 9. Create Storage Link
```bash
php artisan storage:link
```

### 10. Set Up Permissions (Linux/Mac)
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Option 2: Using Laravel Sail (Docker - Easier Setup)

Laravel Sail provides a Docker development environment:

```bash
# Install Sail
composer require laravel/sail --dev

# Publish Sail configuration
php artisan sail:install

# Configure .env for Sail
# Set DB_CONNECTION=mysql (Sail's default) or configure PostgreSQL

# Start the environment
./vendor/bin/sail up

# Run commands in Sail container
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

## Running the Application

### Development Server
```bash
php artisan serve
```
Access at: http://localhost:8000

### With Queues (Recommended)
For full functionality with background jobs:
```bash
php artisan queue:work
```

### Development with Hot Reload
```bash
npm run dev
```

### Using Laravel Sail (Docker)
If you prefer Docker:
```bash
# Install Sail
composer require laravel/sail --dev

# Publish Sail
php artisan sail:install

# Start Sail
./vendor/bin/sail up
```

## Key Features & Setup

### Filament Admin Panel
- Access admin panel at: `/admin`
- Default admin user will be created during seeding
- Manage courses, users, analytics, etc.

### Laravel Horizon (Queue Dashboard)
- Access at: `/horizon`
- Requires Redis for full functionality
- Monitor and manage background jobs

### API Authentication
- Uses Laravel Sanctum for API tokens
- Configure in `config/sanctum.php`

### File Uploads
- Uses Spatie Media Library
- Files stored in `storage/app/public`
- Access via symbolic link at `public/storage`

### Permissions & Roles
- Uses Spatie Laravel Permission
- Configure roles in seeders or admin panel

### Activity Logging
- All user activities are logged
- View logs in admin panel or database

## Testing

### Run Tests
```bash
php artisan test
```

### Code Style
```bash
# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint
```

## Development Commands

### Useful Artisan Commands
```bash
# Clear all caches
php artisan optimize:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta

# Create new migration
php artisan make:migration create_example_table

# Create new model
php artisan make:model Example

# Create new controller
php artisan make:controller ExampleController
```

### Composer Scripts
```bash
# Full setup (from composer.json)
composer run setup

# Development environment
composer run dev

# Run tests
composer run test
```

## Project Structure

```
app/
├── Domains/          # Domain-driven design structure
│   ├── Admin/        # Admin functionality
│   ├── Analytics/    # Analytics features
│   ├── Course/       # Course management
│   ├── Finance/      # Payment & finance
│   ├── Learning/     # Learning modules
│   ├── Order/        # Order management
│   ├── Payment/      # Payment processing
│   └── User/         # User management
├── Http/Controllers/ # HTTP controllers
├── Models/          # Eloquent models
├── Providers/       # Service providers
└── Services/        # Business logic services

resources/
├── css/            # Stylesheets
├── js/             # JavaScript files
└── views/          # Blade templates

routes/
├── api.php         # API routes
├── web.php         # Web routes
└── console.php     # Console commands

database/
├── migrations/     # Database migrations
├── seeders/        # Database seeders
└── factories/      # Model factories
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R $USER:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

2. **Database Connection Issues**
   - Check `.env` database settings
   - Ensure database server is running
   - Verify user permissions

3. **Node.js Build Errors**
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   npm run build
   ```

4. **Queue Not Working**
   - Ensure Redis is running
   - Check `QUEUE_CONNECTION` in `.env`
   - Run `php artisan queue:work`

5. **Assets Not Loading**
   ```bash
   php artisan storage:link
   npm run build
   ```

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

Access Laravel Debugbar at the bottom of pages when logged in.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests: `composer run test`
5. Check code style: `./vendor/bin/pint --test`
6. Commit your changes
7. Push to the branch
8. Create a Pull Request

## License

This project is licensed under the MIT License.

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
