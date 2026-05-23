<?php
/**
 * Database Setup Script
 * This script creates the database and tables for the Task Tracker application
 * Run this script once after cloning the repository to initialize the database
 */

$servername = "localhost";
$username = "root";
$password = "";
$database = "task_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$createDbSql = "CREATE DATABASE IF NOT EXISTS " . $database;
if (!$conn->query($createDbSql)) {
    die("Error creating database: " . $conn->error);
}

// Select the database
if (!$conn->select_db($database)) {
    die("Error selecting database: " . $conn->error);
}

// Read the schema file
$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
    die("Error: schema.sql file not found!");
}

$sql = file_get_contents($schemaFile);

// Split SQL statements and execute them separately
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = true;
$errors = [];

foreach ($statements as $statement) {
    // Remove comment lines from the statement
    $lines = explode("\n", $statement);
    $cleanedLines = array_filter($lines, function($line) {
        $trimmed = trim($line);
        return !empty($trimmed) && substr($trimmed, 0, 2) !== '--';
    });
    
    $cleanStatement = implode("\n", $cleanedLines);
    $cleanStatement = trim($cleanStatement);
    
    // Skip if empty after cleaning
    if (empty($cleanStatement)) {
        continue;
    }
    
    if (!$conn->query($cleanStatement)) {
        $success = false;
        $errors[] = $conn->error;
    }
}

if ($success) {
    echo "<h1 style='color: green;'>✓ Database and tables created successfully!</h1>";
    echo "<p>The Task Tracker database is now ready to use.</p>";
    echo "<p><a href='../public/index.php'>Go to Application</a></p>";
} else {
    echo "<h1 style='color: red;'>✗ Error creating database or tables</h1>";
    foreach ($errors as $error) {
        echo "<p>Error: " . $error . "</p>";
    }
}

$conn->close();
?>
