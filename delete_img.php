<?php
// Include the database connection script
require_once('db_connect.php');

// Extract values from the GET request
extract($_GET);

// Query the database to select a record with the given 'id'
$query = $conn->query("SELECT * FROM music_list where id = '{$id}'");

// Check if there are rows found with the specified 'id'
if ($query->num_rows > 0) {
    // Fetch the first row of the result as an associative array
    $res = $query->fetch_array();

    // Attempt to delete the record with the specified 'id' from the database
    $delete = $conn->query("DELETE FROM `music_list` where id = '{$id}'");

    if ($delete) {
        // If the delete operation is successful, check and delete associated files
        if (!empty($res['image_path']) && is_file(explode("?", $res['image_path'])[0])) {
            // Delete the image file if it exists
            unlink(explode("?", $res['image_path'])[0]);
        }

        if (!empty($res['audio_path']) && is_file(explode("?", $res['audio_path'])[0])) {
            // Delete the audio file if it exists
            unlink(explode("?", $res['audio_path'])[0]);
        }

        // Display a success message and redirect to the home page
        echo '<script>alert("Audio Data successfully deleted."); location.replace("./")</script>';
    } else {
        // Display an error message if the delete operation fails
        echo '<script>alert("Unable to Delete the audio data."); location.replace("./")</script>';
    }
} else {
    // If no rows were found with the specified 'id', display an error message
    echo '<script>alert("Unknown Audio Data ID."); location.replace("./")</script>';
}

