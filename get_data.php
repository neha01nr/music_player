<?php
// Include the database connection script
require_once('db_connect.php');

// Extract values from the POST request
extract($_POST);

// Query the database to select a record with the given 'id'
$query = $conn->query("SELECT * FROM music_list where id = '{$id}'");

// Initialize a response array
$resp = array();

// Check if there are rows found with the specified 'id'
if ($query->num_rows > 0) {
    // If there are rows, set the 'status' key in the response array to 'success'
    $resp['status'] = 'success';

    // Fetch the first row of the result as an associative array
    $res = $query->fetch_array();

    // Check if 'image_path' is empty or if the file doesn't exist; if so, set a default image path
    if (empty($res['image_path']) || (!empty($res['image_path']) && !is_file(explode("?", $res['image_path'])[0]))) {
        $res['image_path'] = "./images/music-logo.jpg";
    }

    // Set the 'data' key in the response array with the fetched data
    $resp['data'] = $res;
} else {
    // If no rows were found with the specified 'id', set the 'status' key to 'failed' and provide an error message
    $resp['status'] = 'failed';
    $resp['error'] = 'Unknown Audio ID';
}

// Encode the response array as JSON and echo it
echo json_encode($resp);
