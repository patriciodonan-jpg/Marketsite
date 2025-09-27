# MarketSite

MarketSite is a starter marketing website with a React frontend, PHP backend that saves contact submissions to MongoDB, and a Python analytics script.
This repository includes a Docker Compose setup so you can run everything locally with one command.

## Features
- React frontend (Vite)
- PHP API using mongodb + PHPMailer to save submissions and send email notifications (Gmail SMTP example)
- MongoDB for storing contact submissions
- Python script to generate simple analytics from MongoDB
- Docker Compose for local development (frontend, php-fpm/nginx, mongodb)

## Prerequisites
- Docker & Docker Compose installed on your machine
- An SMTP account (we included a Gmail example). For Gmail, create an App Password (if you use 2FA).

## Quick start (beginner-friendly)
1. Copy `.env.example` to `.env` in the project root and fill in the values (especially SMTP and Mongo URI).
```bash
cp .env.example .env
```

2. Build and run everything with Docker Compose:
```bash
docker compose up --build
```

3. The React frontend will be available at `http://localhost:5173`.
   The PHP API will be served by the `php` service at `http://localhost:8080/api/contact.php`.

4. Submit the contact form on the frontend to verify storage in MongoDB and email notifications.

## Stop & clean
To stop and remove containers:
```bash
docker compose down
```

## Troubleshooting
- If emails don't send, check SMTP credentials in `.env` and ensure your provider allows SMTP (use App Password for Gmail).
- If frontend can't reach backend, confirm `ALLOWED_ORIGIN` is set in `backend-php/.env`.
- If ports are in use, change them in `docker-compose.yml`.

---
