<?php
// Database connection configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'task_tracker';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$db_name = "task_tracker";
$sql = "CREATE DATABASE IF NOT EXISTS " . $db_name;
if ($conn->query($sql) === TRUE) {
    // Database created successfully or already exists
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($db_name);

// Import SQL file
$sql_file = file_get_contents(__DIR__ . '/../database/schema.sql');
if ($conn->multi_query($sql_file) === TRUE) {
    // Tables created successfully
} else {
    echo "Error creating tables: " . $conn->error;
}
?>
