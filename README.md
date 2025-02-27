## Laravel Project Installation Guide

### Prerequisites
Before starting, make sure you have the following installed on your system:
- **PHP** (8.0 or later) - [Download here](https://www.php.net/)
- **Composer** - [Download here](https://getcomposer.org/)
- **Git** - [Download here](https://git-scm.com/)
- **XAMPP** (for MySQL and Apache) - [Download here](https://www.apachefriends.org/)

### Clone the Repository
Run the following command to clone the repository:
```sh
git clone https://github.com/your-username/your-repo.git
```
Replace `your-username/your-repo` with your actual GitHub username and repository name.

### Navigate to the Project Directory
```sh
cd your-repo
```
Replace `your-repo` with the cloned repository's folder name.

### Install Dependencies
```sh
composer install
```

### Environment Configuration
1. Copy the example environment file:
   ```sh
   cp .env.example .env
   ```
2. Open `.env` and configure the database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   - Ensure XAMPP MySQL is running and create the database manually through **phpMyAdmin**.
   - Set `DB_DATABASE` to your actual database name.

### Generate Application Key
```sh
php artisan key:generate
```

### Run Database Migrations
```sh
php artisan migrate
```

### Start the Development Server
```sh
php artisan serve
```
Your Laravel application should now be accessible at `http://127.0.0.1:8000/`.

### Using XAMPP for MySQL Database
1. Start **XAMPP Control Panel**.
2. Ensure **Apache** and **MySQL** services are running.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin/`).
4. Create a new database matching the `.env` `DB_DATABASE` value.

### Additional Commands
| Command               | Description |
|----------------------|-------------|
| `php artisan serve` | Start development server |
| `php artisan migrate` | Run database migrations |
| `php artisan db:seed` | Seed database with test data |
| `php artisan tinker` | Interact with application through command line |
| `php artisan queue:work` | Process background jobs |

---
Your Laravel project is now set up! 🚀