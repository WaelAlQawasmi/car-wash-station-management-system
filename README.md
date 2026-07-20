# Car Stashen ERP

A PHP 8.2+ MVC web application for managing a car wash and oil-change business. The current version is a working prototype focused on authentication, a premium operations dashboard, customer listing, and service catalog views.

## What the application does

The app provides a simple ERP-style experience for staff users:

- Login and logout flow for authenticated access
- Dashboard with KPI cards, charts, and summary sections
- Customer management page with a basic list view
- Service catalog page for available services
- A structured backend flow using controllers, services, repositories, and models

## Main features

- Modern dashboard interface with Bootstrap and Chart.js
- Session-based authentication
- MVC architecture with clear separation of concerns
- Repository and service-layer structure for future expansion
- JSON summary endpoint for dashboard data
- MySQL-friendly schema for customers, vehicles, services, work orders, and audit logging

## How it works

### Front end

The UI is rendered from PHP view templates under the app/Views directory. The shared layout provides the navigation shell, and each page is loaded inside that layout.

### Back end

Requests enter through public/index.php, which initializes the session, registers autoloading, and dispatches routes through the custom router. Controllers receive traffic, services contain business logic, repositories provide data, and views render the final HTML.

## Request and data flow

1. The browser requests a route such as /dashboard, /customers, /services, or /login.
2. public/index.php parses the request and checks for authentication.
3. The router maps the path to a controller method.
4. The controller delegates work to a service layer.
5. The service uses a repository to produce data.
6. The controller passes data into a view template.
7. The view renders the page in the browser.

## Expected behavior

- Unauthenticated users are redirected to /login for protected routes.
- Valid login credentials redirect the user to the dashboard.
- Invalid credentials show an error message.
- The dashboard displays summary metrics and a chart.
- Customers and services pages render their matching data views.

## Architecture overview

- app/Controllers: request handlers
- app/Services: business logic
- app/Repositories: data access layer
- app/Models: domain objects
- app/Views: page templates
- app/Core: routing, view rendering, and database access

## Setup

1. Create a MySQL database named car_stashen.
2. Import database/schema.sql.
3. Update config/app.php with your database credentials.
4. Serve the project from the public directory.

Example:

```bash
php -S 127.0.0.1:8000 -t public
```

Then open:

- http://127.0.0.1:8000/login

## Documentation

A full technical guide covering the front-end behavior, back-end flow, data flow, expected behavior, architecture, and future expansion points is available in [docs/technical-documentation.md](docs/technical-documentation.md).

## Notes

This is a starter implementation. The current version uses demo-style data in several areas and is prepared for future database-backed modules such as work orders, inventory, payments, and reporting.
