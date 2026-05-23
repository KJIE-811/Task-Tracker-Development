<?php
/**
 * Database Setup Script
 * This script creates the database and tables for the Task Tracker application
 * Run this script once after cloning the repository to initialize the database
 */

$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the schema file
$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
    die("Error: schema.sql file not found!");
}

$sql = file_get_contents($schemaFile);

// Execute the schema
if ($conn->multi_query($sql)) {
    echo "<h1 style='color: green;'>✓ Database and tables created successfully!</h1>";
    echo "<p>The Task Tracker database is now ready to use.</p>";
    echo "<p><a href='../public/index.php'>Go to Application</a></p>";
} else {
    echo "<h1 style='color: red;'>✗ Error creating database or tables</h1>";
    echo "<p>Error: " . $conn->error . "</p>";
}

$conn->close();
?>
