const mysql = require('mysql2/promise');

// Database configuration - same as legacy HROS
const dbConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USERNAME || 'rccmgvfd_hros_user',
    password: process.env.DB_PASSWORD || 'Ompl@65482*',
    database: process.env.DB_NAME || 'rccmgvfd_hros',
    charset: 'utf8mb4',
    timezone: '+05:00', // Maldivian timezone
    connectionLimit: 10,
    acquireTimeout: 60000,
    timeout: 60000,
    reconnect: true
};

// Create connection pool
const pool = mysql.createPool(dbConfig);

// Test database connection
async function testConnection() {
    try {
        const connection = await pool.getConnection();
        console.log('Database connected successfully');
        connection.release();
        return true;
    } catch (error) {
        console.error('Database connection failed:', error.message);
        return false;
    }
}

// Execute query with error handling
async function executeQuery(sql, params = []) {
    try {
        const [rows] = await pool.execute(sql, params);
        return { success: true, data: rows };
    } catch (error) {
        console.error('Query execution error:', error);
        return { success: false, error: error.message };
    }
}

// Execute transaction
async function executeTransaction(queries) {
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();
        
        const results = [];
        for (const query of queries) {
            const [rows] = await connection.execute(query.sql, query.params || []);
            results.push(rows);
        }
        
        await connection.commit();
        return { success: true, data: results };
    } catch (error) {
        await connection.rollback();
        console.error('Transaction error:', error);
        return { success: false, error: error.message };
    } finally {
        connection.release();
    }
}

// Initialize database connection
testConnection();

module.exports = {
    pool,
    executeQuery,
    executeTransaction,
    testConnection
};