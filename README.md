# ROUTS

A comprehensive route management system developed as part of my graduation project. This application provides efficient route planning, optimization, and management capabilities for transportation and logistics companies.

## Features

- Route planning and optimization
- Real-time vehicle tracking
- Driver management
- User authentication and authorization
- Comprehensive reporting dashboard
- Mobile-responsive design
- Geofencing capabilities

## Technology Stack

- **Backend**: Laravel (PHP)
- **Frontend**: Blade templates with Tailwind CSS and flutter
- **Database**: MySQL/SQLite
- **Additional**: Vite for asset compilation, Filament for admin panel

## Installation

1. Clone the repository
`ash
git clone https://github.com/your-username/ROUTS.git
cd ROUTS
`

2. Install PHP dependencies
`ash
composer install
`

3. Install Node.js dependencies
`ash
npm install
`

4. Set up environment
`ash
cp .env.example .env
php artisan key:generate
`

5. Configure database in .env file
`env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=routs
DB_USERNAME=your_username
DB_PASSWORD=your_password
`

6. Run migrations
`ash
php artisan migrate
`

7. Seed the database (optional)
`ash
php artisan db:seed
`

8. Start the application
`ash
php artisan serve
`

9. Compile assets (in another terminal)
`ash
npm run dev
`

## Usage

1. Access the application at http://localhost:8000
2. Use the admin panel at /admin (if Filament is configured)
3. Create user accounts and manage routes
4. Track vehicles and optimize routes

## Project Structure

- pp/ - Application logic and models
- database/ - Migrations, seeders, and factories
- esources/ - Views, CSS, and JavaScript files
- outes/ - Web and API routes
- public/ - Publicly accessible files

## Attribution

This project was originally developed as part of a graduation project team effort.
- Original repository: https://github.com/Samaelnady11/ROUTS
- Original author: Samael Nady

This repository is for my personal portfolio and continued development.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
