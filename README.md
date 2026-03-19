# Institutional Agenda (Agenda Institucional)

We developed an **Institutional Agenda** designed to organize and streamline the management of events and audiences within a public institution. The system allows employees to manage their commitments through a complete CRUD, integrating interactive calendars and data charts for a clear and quick visualization of daily activities.

The technical highlight of this project is the integration of a **Telegram Bot** and scheduled tasks (**Cron Jobs**) on the server. This allows users to receive their daily agenda directly on their mobile phones, ensuring that important information arrives on time, automatically, and without relying solely on the web platform.

## Key Features
- **Event & Audience Management**: Complete CRUD operations for handling institutional commitments.
- **Interactive Calendars**: Visual scheduling and tracking of events.
- **Data Visualization**: Charts and graphs for a clear, quick overview of daily activities.
- **Telegram Bot Integration**: Automated daily agenda notifications sent directly to users' phones.
- **Automated Workflows**: Server-side Cron Jobs handling scheduled tasks and notifications.
- **Blade Templates**: User interface developed leveraging Laravel's Blade engine.
- **File Storage & Seeders**: Efficient file handling (`storage`) and database population (`seeders`).

## Tech Stack
- **Framework**: Laravel 8
- **Language**: PHP 7.4
- **Database**: MySQL (relational database designed from scratch)
- **Deployment (Production)**: Linux (Debian 12) servers using Apache, configured with friendly URLs and institutional domains.

## Quick Start & Local Development

To ensure the project can be quickly set up on any computer (especially given the PHP 7.4 requirement), the local development environment is fully containerized.

### Prerequisites
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### Running the application with Docker

1. **Clone the repository** (if you haven't already):
   ```bash
   git clone <repository-url>
   cd agenda-gob
   ```

2. **Set up Environment Variables**:
   Copy the example environment file and configure your local settings:
   ```bash
   cp .env.example .env
   ```

3. **Start the containers**:
   The project includes a `Dockerfile`, `docker-compose.yaml`, and `docker/nginx.conf` for a quick and standardized setup using Nginx for local development.
   ```bash
   docker-compose up -d --build
   ```

4. **Install Dependencies & Set Up Laravel**:
   Execute the setup commands inside your application container (assuming the service is named `app`):
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate --seed
   docker-compose exec app php artisan storage:link
   ```

5. **Access the application**:
   The application should now be available on your `localhost` (refer to `docker-compose.yaml` for specific ports).

## Production Deployment
In production, the system was successfully deployed on **Linux Debian 12** utilizing the **Apache** web server. The infrastructure ensures a functional and secure environment, significantly facilitating the daily workflow of the secretariat.
