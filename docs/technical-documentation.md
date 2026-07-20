# Car Stashen ERP Documentation

## 1. Overview

Car Stashen ERP is a PHP-based MVC web application designed to support the daily operations of a car wash and oil-change business. The current implementation is a solid starter foundation for an ERP-style dashboard with authentication, a dashboard overview, customer listing, and service catalog views.

The application is intentionally organized around a modular architecture so future modules such as work orders, inventory, appointments, payments, and reporting can be added more easily.

## 2. Goals and Scope

The project aims to provide:

- A clean, browser-based operations dashboard
- Session-based authentication for staff users
- A structured MVC architecture with controllers, services, repositories, and models
- A future-ready database schema for ERP-style business workflows
- An easy-to-follow front-end and back-end request flow

## 3. Current Status

The implementation is currently a working prototype with these available features:

- Login and logout flow
- Dashboard overview with KPI-style cards and charts
- Customer list view
- Service catalog view
- JSON summary endpoint for dashboard data
- Database schema prepared for future persistence

The data shown in the current UI is mostly demo/static content, while the database layer is structured for future real persistence.

## 4. Technology Stack

### Front end

- HTML5 and PHP-rendered views
- Bootstrap 5 for layout and styling
- Bootstrap Icons for UI actions
- Chart.js for the dashboard chart

### Back end

- PHP 8.2+
- Custom MVC framework components
- PDO for database access
- Session-based authentication
- PSR-4 style autoloading

## 5. Application Architecture

The project uses a lightweight MVC pattern:

- Controllers handle requests and prepare data for views
- Services contain business logic
- Repositories interact with data sources
- Models represent business entities
- Views render HTML output
- Core classes provide shared infrastructure such as routing, view rendering, and database access

### Main directories

- app/Controllers: request handlers
- app/Services: business logic
- app/Repositories: data access abstractions
- app/Models: domain objects
- app/Views: HTML templates
- app/Core: shared framework classes
- config: environment and app configuration
- database: SQL schema
- public: web entry point and asset hosting
- tests: basic smoke and unit-style tests

## 6. Request Lifecycle

When a user opens the application, the flow is as follows:

1. The browser requests a page through the public entry point in public/index.php.
2. The application starts a PHP session and registers autoloading for App classes.
3. The current request URL is parsed.
4. The application checks whether the user is authenticated.
   - If the user is not logged in and is not visiting /login or /logout, they are redirected to /login.
5. The router matches the request path and method to a defined route.
6. The corresponding controller method is called.
7. The controller uses a service layer to retrieve or process data.
8. The service uses a repository or static data source to provide the result.
9. The controller passes the data to a view, which renders the final HTML response.

## 7. Front-End Behavior

### Login screen

- The login page is served at /login.
- It displays a simple authentication form.
- The current demo credentials are:
  - Email: admin@carstashen.com
  - Password: password123
- On successful login, the user is stored in the session and redirected to the dashboard.
- On failure, an error message is displayed and the user remains on the login page.

### Dashboard

- The dashboard is the default landing page after authentication.
- It displays a set of summary cards for:
  - Daily revenue
  - Monthly revenue
  - Today’s cars
  - Pending orders
- It also renders:
  - A revenue trend chart
  - A top services section
  - A quick actions area
  - Best customers and top employees lists

### Customers page

- The Customers page lists sample customer records.
- Each row contains the customer name, phone, email, loyalty points, and membership type.
- The page is intended to be the starting point for customer management features in the future.

### Services page

- The Services page shows a service catalog.
- Each service includes its name, price, and duration.
- This is currently a static list, but it is designed to evolve into a real catalog management module.

### Layout and navigation

- The shared layout file provides the sidebar navigation and global page shell.
- The active navigation item changes based on the current content template.
- A logout action is available in the top-right area of the layout.

## 8. Back-End Behavior

### Authentication flow

- The AuthController handles login, login validation, and logout.
- The authenticate action reads POST data from the login form.
- If the credentials match the demo values, a user session is created.
- The session stores:
  - Name
  - Email
  - Role
- The logout action destroys the session and redirects the user back to the login page.

### Routing behavior

Routes are registered in public/index.php using the custom router class.

Available routes include:

- GET /login
- POST /login
- GET /logout
- GET /dashboard
- GET /
- GET /api/dashboard/summary
- GET /customers
- GET /services

If no route matches, the application returns a 404 response.

### Controller responsibilities

- AuthController: login and authentication handling
- DashboardController: prepares dashboard data and the dashboard summary JSON endpoint
- CustomerController: loads customer data for the customer page
- ServiceController: provides service catalog data for the services page

### Service layer responsibilities

The service layer is used to encapsulate business logic and keep controllers thin.

- DashboardService delegates dashboard summary retrieval to the repository
- CustomerService retrieves customer data through the repository layer

### Repository layer responsibilities

Repositories act as a bridge between application services and the underlying data source.

- DashboardRepository currently returns a static summary payload for the dashboard
- CustomerRepository returns sample customer objects in memory

## 9. Data Flow

### Dashboard data flow

1. A user requests /dashboard or /api/dashboard/summary.
2. DashboardController receives the request.
3. DashboardController calls DashboardService.
4. DashboardService calls DashboardRepository.
5. DashboardRepository returns a summary array containing metrics such as revenue, orders, services, employees, and customer performance.
6. The controller passes the summary to the dashboard view.
7. The view renders cards, charts, and lists based on that data.

### Customer data flow

1. A user requests /customers.
2. CustomerController receives the request.
3. CustomerController calls CustomerService.
4. CustomerService calls CustomerRepository.
5. CustomerRepository returns Customer model objects.
6. The view renders them in a table.

### Service data flow

1. A user requests /services.
2. ServiceController prepares a simple array of service definitions.
3. The service catalog view renders those records as cards.

## 10. Database Design

The database schema in database/schema.sql defines the foundation for a real ERP workflow.

### Core tables

- users: staff and admin accounts
- branches: business locations
- customers: customer profile information
- vehicles: vehicle details linked to customers
- services: catalog of offered services
- work_orders: service jobs and their current status
- audit_logs: activity tracking for important actions

This schema supports future features such as:

- Staff authentication and role-based access
- Multi-branch operations
- Vehicle-based service history
- Queue and bay management
- Audit logging
- Soft-deletion and historical tracking

## 11. Expected Behavior

### When the application starts

- The app loads the main entry point from public/index.php.
- A session is created automatically.
- If the user is not authenticated, they are redirected to the login page for protected routes.

### When a user logs in successfully

- The session is populated with user data.
- The user is redirected to the dashboard.
- The dashboard shows the summary metrics and interface elements.

### When a user logs in with invalid credentials

- The application stores an error message in the session.
- The user is returned to the login form.
- The error is shown in an alert box.

### When a user logs out

- The session is destroyed.
- The user is sent back to the login page.

### When the dashboard endpoint is requested

- The application returns the dashboard HTML page by default.
- The JSON endpoint /api/dashboard/summary returns a machine-readable summary of the dashboard metrics.

## 12. Current Implementation Notes

The current version is a prototype and uses demo data in several places. This means:

- Dashboard statistics are not yet pulled from a real database query.
- Customer lists are mocked from in-memory repository data.
- Authentication is demo-based rather than using password hashing and real user storage.
- The application is prepared for real database-backed expansion, but that layer is not fully wired yet.

## 13. Setup Instructions

### Requirements

- PHP 8.2 or newer
- MySQL database server
- A web server or PHP built-in server

### Steps

1. Create a MySQL database named car_stashen.
2. Import the SQL schema from database/schema.sql.
3. Update config/app.php with your database connection values.
4. Serve the project from the public directory.

### Example local run

```bash
php -S 127.0.0.1:8000 -t public
```

Then open:

- http://127.0.0.1:8000/login

## 14. Testing

The tests folder contains basic checks for the dashboard repository and a smoke test that exercises the customer page through the public entry point.

## 15. Recommended Next Steps

To evolve this prototype into a full ERP system, the next priorities should be:

- Replace demo data with real database-backed queries
- Implement secure authentication with password hashing
- Add CRUD operations for customers, services, vehicles, and work orders
- Introduce role-based permissions and staff management
- Add inventory, payments, reports, and audit features
- Improve routing and API support
