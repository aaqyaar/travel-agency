# Travel System

This is a travel system that allows users to book trips and manage their trips. The system has the following features:

-   Laravel
-   Filament
-   Tailwind CSS
-   React Native
-   MySQL

## Installation

1. Clone the repository
2. Copy the `.env.example` file to `.env`
    - Configure the database connection in the `.env` file
    - Create database called `travel`
3. Run `composer install`
4. Run `php artisan migrate:fresh --seed`

5. Run the following commands to seed the database and create a super admin user:

```bash
php artisan migrate:fresh --seed
```

6. Run `php artisan serve`
