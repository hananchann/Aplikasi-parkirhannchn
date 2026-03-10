# Deployment Guide: Sistem Parkir on Vercel

This project is prepared for deployment on Vercel using the official PHP runtime.

## Prerequisites

1.  **Vercel Account**: Sign up at [vercel.com](https://vercel.com).
2.  **External MySQL Database**: Vercel does not host databases. You need a remote MySQL database. You can use:
    - [Aiven](https://aiven.io/) (Free tier available)
    - [PlanetScale](https://planetscale.com/)
    - [Clever Cloud](https://www.clever-cloud.com/)

## Step 1: Database Setup

1.  Create a MySQL database on your chosen provider.
2.  Import the database schema using the `parkir/db_parkir.sql` file.
    - You can use a tool like phpMyAdmin, DBeaver, or the command line to import the SQL file.

## Step 2: Vercel Configuration

1.  Push your code to a GitHub, GitLab, or Bitbucket repository.
2.  Import the project into Vercel.
3.  In the **Environment Variables** section, add the following variables:
    - `DB_HOST`: Your database host (e.g., `mysql-xxx.aivencloud.com`)
    - `DB_USER`: Your database username (e.g., `avnadmin`)
    - `DB_PASS`: Your database password
    - `DB_NAME`: `defaultdb` (or your specific database name)
    - `DB_PORT`: Set this to your Aiven port (e.g., `24176`). **IMPORTANT**: Aiven does not use the default `3306`.
    - `DB_SSL`: `true` (Aiven and most cloud providers require SSL)

## Step 3: Deployment

1.  Click **Deploy**.
2.  Vercel will build the project using the settings in `vercel.json`.
3.  Once finished, your app will be available at a `.vercel.app` URL.

## Local Development

Your project still works locally with XAMPP. The `config/koneksi.php` file will prioritize environment variables (on Vercel) and fallback to local settings if they are not set.

---
Prepared by Antigravity AI
