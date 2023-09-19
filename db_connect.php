<?php
// Define database connection parameters
$host = "localhost";       // Hostname where your database server is running
$username = "root";        // Database username
$pw = "";                   // Database password (empty in this case)
$db = "mp_db";             // Database name

// Attempt to establish a connection to the database
$conn = @new mysqli($host, $username, $pw, $db);

// Check if the connection was successful
if (!$conn) {
    // If the connection fails, terminate the script and display an error message
    die("Database Connection Failed. Error: " . $conn->error);
}

// If the connection is successful, you can proceed with database operations here
// For example, executing SQL queries, fetching data, or updating records.

// Don't forget to close the database connection when you're done:
// $conn->close();

