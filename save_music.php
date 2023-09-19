<?php
// Include the database connection file
require_once('db_connect.php');

// Extract POST data into variables
extract($_POST);

// Initialize an empty data string to construct the SQL query
$data = "";

// Loop through POST data to build the SQL update or insert statement
foreach($_POST as $k => $v){
	
	// Exclude the 'id' field from the update or insert
    if(!in_array($k,['id'])){
		 
		 // Sanitize and escape the value to prevent SQL injection
        $v = $conn->real_escape_string($v);
		
		// If data is not empty, add a comma separator
        if(!empty($data)) $data .=", ";
		
		// Append the field and value to the data string
        $data .= "`{$k}` = '{$v}'";
    }
}

// Check if an audio file was uploaded
if(!empty($_FILES['audio']['tmp_name'])){
    $filename = $_FILES['audio']['name'];
    $filename = str_replace(" ","_",$filename);
    $i = -1;
	
	// Generate a unique filename to prevent overwriting existing files
    while(true){
        if($i > -1)
        $filename = $i."_".$filename;
	
	// Check if a file with the same name already exists
        if(is_file('./audio/'.$filename))
        $i++;
        else
        break;
    }
    $file = $_FILES['audio']['tmp_name'];
	
	 // Determine the MIME type of the uploaded file
    $type = mime_content_type($file);
    $type2 =  $_FILES['audio']['type'];
	
	// Check if the MIME type indicates an audio file
    if(strpos($type,"audio/") > -1 || strpos($type2,"audio/") > -1){
        
		// Move the uploaded audio file to the 'audio' directory
		$move = move_uploaded_file($file,'./audio/'.$filename);
        if(!$move){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Audio file updload failed';
            $resp['error'] = $conn->error;
        }else{
			
		// If file upload succeeds, update the 'audio_path' field in the data string	
            $data .= ", `audio_path` = CONCAT('./audio/{$filename}?v=',unix_timestamp(CURRENT_TIMESTAMP))";
        }

    }else{
		 // If the uploaded file is not an audio file, set an error response
        $resp['status'] = 'failed';
        $resp['msg'] = 'Invalid Audio file';
        $resp['error'] = $type;
        $resp['file'] = $_FILES;
    }
}

// If there are no previous errors, proceed with database operations
if(!isset($resp['status'])){
    if(empty($id)){
		 
		 // If 'id' is empty, construct an INSERT SQL statement
        $sql = "INSERT INTO `music_list` set {$data}";
    }else{
		
		// If 'id' is provided, construct an UPDATE SQL statement
        $sql = "UPDATE `music_list` set {$data} where id = '{$id}'";
    }
	
	// Execute the SQL statement to save the data
    $save = $conn->query($sql);
    if($save){
		 // If data is successfully saved, determine the music ID
        $mid = !empty($id) ? $id : $conn->insert_id;
        
		 // Set success response
		$resp['status'] = 'success';
        $resp['msg'] = ' Audio Data successfully saved';
        
		// Check if an image file was uploaded
        if(!empty($_FILES['img']['tmp_name'])){
            $filename = $_FILES['img']['name'];
            $filename = str_replace(" ","_",$filename);
            $file = $_FILES['img']['tmp_name'];
			
			// Determine the MIME type of the uploaded image
            $type = mime_content_type($file);
            $i = -1;
			
			// Generate a unique filename for the image
            while(true){
                if($i > -1)
                $filename = $i."_".$filename;
                if(is_file('./images/'.$filename))
                $i++;
                else
                break;
            }
            if(strpos($type,"image/") > -1){
				// If the MIME type indicates an image, move the image file to the 'images' directory
                
				if ($move) {
                    // If the image upload succeeds, update the 'image_path' field in the database
                    $conn->query("UPDATE `music_list` SET image_path = CONCAT('./images/{$filename}?v=', unix_timestamp(CURRENT_TIMESTAMP)) WHERE id = '{$mid}'");
                } else {
                    // If the image upload fails, update the response message
                    $data['msg'] .= " But Image has failed to upload.";
                }
            }
        }
    } else {
        // If an error occurs while saving the data, set an error response
        $resp['status'] = 'failed';
        $resp['msg'] = 'An error occurred while saving the data.';
        $resp['error'] = $conn->error;
    }
}

// Encode the response as JSON and send it back
echo json_encode($resp);

