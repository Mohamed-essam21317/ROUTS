# ROUTS

A comprehensive route management system developed as part of my graduation project. This application provides efficient route planning, optimization, and management capabilities for transportation and logistics companies.

## Features

- **Route Planning & Optimization**: Advanced algorithms for efficient route calculation and optimization
- **Real-time Vehicle Tracking**: Live GPS tracking with real-time location updates
- **Driver Management**: Complete driver profile management and assignment system
- **User Authentication & Authorization**: Secure login system with role-based access control
- **Comprehensive Reporting Dashboard**: Analytics and reporting for business insights
- **Mobile-responsive Design**: Cross-platform compatibility for all devices
- **Geofencing Capabilities**: Virtual boundary management with automated alerts and notifications
- **Payment Integration**: Seamless payment processing with Paymob integration
- **Fleet Management**: Complete fleet monitoring and maintenance tracking
- **Notification System**: Real-time alerts for drivers, customers, and administrators

## Technology Stack

- **Backend**: Laravel (PHP 8.x)
- **Frontend**: Blade templates with Tailwind CSS and Flutter
- **Database**: MySQL/SQLite
- **Payment Gateway**: Paymob integration
- **Additional**: Vite for asset compilation, Filament for admin panel
- **APIs**: RESTful API for mobile applications

## Key Integrations

### Geofencing
- **Virtual Boundaries**: Create and manage geofenced areas
- **Automated Alerts**: Real-time notifications when vehicles enter/exit zones
- **Custom Rules**: Set up specific actions based on geofence events
- **Analytics**: Track geofence violations and compliance

### Payment Integration (Paymob)
- **Multiple Payment Methods**: Credit cards, mobile wallets, and bank transfers
- **Secure Transactions**: PCI DSS compliant payment processing
- **Real-time Processing**: Instant payment confirmation and status updates
- **Refund Management**: Automated refund processing and tracking
- **Payment Analytics**: Comprehensive payment reporting and insights

## Installation

1. **Clone the repository**
`ash
git clone https://github.com/Mohamed-essam21317/ROUTS.git
cd ROUTS
`

2. **Install PHP dependencies**
`ash
composer install
`

3. **Install Node.js dependencies**
`ash
npm install
`

4. **Set up environment**
`ash
cp .env.example .env
php artisan key:generate
`

5. **Configure database in .env file**
`env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=routs
DB_USERNAME=your_username
DB_PASSWORD=your_password
`

6. **Configure Paymob settings in .env file**
`env
PAYMOB_API_KEY=your_paymob_api_key
PAYMOB_INTEGRATION_ID=your_integration_id
PAYMOB_HMAC_SECRET=your_hmac_secret
PAYMOB_IFRAME_ID=your_iframe_id
`

7. **Run migrations**
`ash
php artisan migrate
`

8. **Seed the database (optional)**
`ash
php artisan db:seed
`

9. **Start the application**
`ash
php artisan serve
`

10. **Compile assets (in another terminal)**
`ash
npm run dev
`

## Usage

### Basic Usage
1. Access the application at http://localhost:8000
2. Use the admin panel at /admin (if Filament is configured)
3. Create user accounts and manage routes
4. Track vehicles and optimize routes

### Geofencing Setup
1. Navigate to the Geofencing section in the admin panel
2. Create virtual boundaries by drawing on the map
3. Set up alert rules and notification preferences
4. Assign geofences to specific routes or vehicles

### Payment Configuration
1. Set up your Paymob account and obtain API credentials
2. Configure payment methods in the admin panel
3. Test payments using Paymob's sandbox environment
4. Monitor transactions through the payment dashboard

## API Endpoints

### Geofencing
- POST /api/geofences - Create new geofence
- GET /api/geofences - List all geofences
- PUT /api/geofences/{id} - Update geofence
- DELETE /api/geofences/{id} - Delete geofence

### Payments
- POST /api/pay - Process payment with Paymob
- POST /api/pay/charge-saved-card - Charge a saved card
- POST /api/paymob/webhook - Handle Paymob webhook notifications

## Project Structure

- app/ - Application logic and models
- database/ - Migrations, seeders, and factories
- resources/ - Views, CSS, and JavaScript files
- routes/ - Web and API routes
- public/ - Publicly accessible files
- config/ - Configuration files including Paymob settings

## Testing

### Geofencing Testing
Refer to the GEOFENCING_TEST_GUIDE.md file for comprehensive testing instructions.

### Payment Testing
1. Use Paymob's sandbox environment for testing
2. Test different payment methods and scenarios
3. Verify webhook handling and callback processing

## Attribution

This project was originally developed as part of a graduation project team effort.
- Original repository: https://github.com/Samaelnady11/ROUTS
- Original author: Samael Nady

This repository is for my personal portfolio and continued development.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
