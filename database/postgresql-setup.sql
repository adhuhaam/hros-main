-- PostgreSQL Database Setup Script for HROS
-- Run this script as PostgreSQL superuser

-- Create database
CREATE DATABASE hros_db
WITH
    OWNER = postgres ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8' TABLESPACE = pg_default CONNECTION
LIMIT = -1;

-- Connect to the database
\c hros_db;

-- Create user (optional)
CREATE USER hros_user WITH PASSWORD 'hros_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE hros_db TO hros_user;

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO hros_user;

GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO hros_user;

-- Set default privileges for future tables
ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT ALL ON TABLES TO hros_user;

ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT ALL ON SEQUENCES TO hros_user;

-- Enable necessary extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE EXTENSION IF NOT EXISTS "pg_trgm";

-- Optimize PostgreSQL settings for HROS
-- These can be added to postgresql.conf for permanent changes

-- Performance settings (adjust based on your system)
-- shared_buffers = 256MB
-- effective_cache_size = 1GB
-- work_mem = 4MB
-- maintenance_work_mem = 64MB

-- Connection settings
-- max_connections = 100

-- Logging settings (for development)
-- log_statement = 'all'
-- log_duration = on

COMMENT ON DATABASE hros_db IS 'Human Resource Operating System Database';

-- Create schema for application if needed
-- CREATE SCHEMA IF NOT EXISTS hros;

-- Show database info
SELECT
    'Database created successfully' as status,
    current_database () as database_name,
    current_user as current_user,
    version() as postgresql_version;