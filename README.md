# Car Stashen ERP

A commercial-grade PHP 8.2+ MVC web application for managing a car wash and oil change business.

## Features
- Modern dashboard with premium UI
- Multi-branch ready architecture
- Customer, vehicle, service, and work-order modules
- Repository pattern and service layer design
- REST-friendly routing and JSON responses
- MySQL schema with audit and soft-delete support

## Structure
- app/Controllers
- app/Models
- app/Repositories
- app/Services
- app/Views
- config/
- database/
- public/
- routes/
- tests/

## Setup
1. Create a MySQL database named `car_stashen`.
2. Import [database/schema.sql](database/schema.sql).
3. Update [config/app.php](config/app.php) with your database credentials.
4. Serve the project from the `public` directory.

## Notes
This starter implementation includes a working dashboard route and a scalable foundation for adding the broader ERP modules requested.
