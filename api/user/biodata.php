<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];
}else{
  

    $access_key=trim($_GET['access_key']);
    ///////////auth/////////////////////////////////////////
    $fetch=$callclass->_validate_accesskey($conn, $access_key);
    $array = json_decode($fetch, true);
    $check=$array[0]['check'];
    $login_student_id=$array[0]['student_id'];
    ////////////////////////////////////////////////////////
    $response['check']=$check;

    if($check==0){
        $response['response']=101; 
        $response['success']=false;
        $response['message']='Invalid Access Key. Please LogIn Again.'; 
    }else{

        $student_id=trim($_POST['student_id']);
        $state_of_origin_id=trim(strtoupper($_POST['state_of_origin_id']));
        $local_gov_id= trim($_POST['local_gov_id']);
        $address= trim($_POST['address']);
        $marital_status_id=trim($_POST['marital_status_id']);
        $parent_name=trim($_POST['parent_name']);
        $parent_mobileno=trim($_POST['parent_mobileno']);
        $religion_id=trim($_POST['religion_id']);
        $passport=($_FILES['passport']['name']);

        if (empty($state_of_origin_id)){
            $response=[
                'code' => 108,
                'success' => false,
                'message' => 'state of origin is required',
            ];   
        }elseif(empty($local_gov_id)){
            $response=[
                'code' => 105,
                'success' => false,
                'message' => 'Lga is required',
            ];   
        }elseif(empty($marital_status_id)){
            $response=[
                'code' => 105,
                'success' => false,
                'message' => 'Marital status is required',
            ];   
        }elseif(empty($address)){
            $response=[
                'code' => 105,
                'success' => false,
                'message' => 'address is required',
            ];   
        }elseif(empty($parent_name)){
            $response=[
                'code' => 105,
                'success' => false,
                'message' => 'parent name is required',
            ];   
        }elseif(empty($parent_mobileno)){
            $response=[
                'code' => 105,
                'success' => false,
                'message' => 'parent number is required',
            ];   
        }elseif(empty($religion_id)){
            $response=[
                'code' => 105,
                'success' => false,
                'message' => 'religion is required',
            ];   
        }elseif(empty($passport)){
            $response=[
                'code' => 105,
                'success' => false,
                'message' => 'passport is required',
            ];   
        }else{

            $allowedExts = array("jpg", "jpeg", "JPEG", "JPG", "gif", "png","PNG","GIF","webp","WEBP");
            $extension = pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION);
    
            // Define maximum file size (30KB)
            $maxFileSize = 30 * 1024; // 30KB
    
            // Check if file size exceeds the limit
            if ($_FILES['passport']['size'] > $maxFileSize) {
                $response['response'] = 102; 
                $response['success'] = false;
                $response['message'] = "Maximum allowed size is 30KB."; 
            } elseif (in_array($extension, $allowedExts)) {
    
                $datetime = date("Ymdhi");
                $passport = $student_id . '' . $datetime . '' . $passport;
                $uploadPath = '../uploaded-files/picture/student_picture/' . $passport;
    
                if (move_uploaded_file($_FILES["passport"]["tmp_name"], $uploadPath)) {
                    
                    $student_array = $callclass->_get_student($conn, $student_id);
                    $student_detail_array = json_decode($student_array, true);
                    $db_passport = $student_detail_array[0]['passport'];
            
                    if ($db_passport != 'friends.png') {
                        unlink('../uploaded-files/picture/student_picture/' . $db_passport);
                    }
                    
                    $stmt = $conn->prepare("UPDATE student_tab SET marital_status_id=?, state_of_origin_id = ?, local_gov_id = ?, `address` = ?, parent_name = ?, parent_mobileno = ?, religion_id = ?, passport = ? WHERE student_id = ?");
                    $stmt->bind_param("iiisssiss", $marital_status_id, $state_of_origin_id, $local_gov_id, $address, $parent_name, $parent_mobileno, $religion_id, $passport, $student_id);
                    $stmt->execute();
        
    
                    $response['response'] = 200; 
                    $response['success'] = true;
                    $response['message'] = "Biodata successful!";
                } else {
                    $response['response'] = 103; 
                    $response['success'] = false;
                    $response['message'] = "PICTURE UPLOAD ERROR! Contact your Engineer For Help"; 
                }
            } else {
                $response['response'] = 104; 
                $response['success'] = false;
                $response['message'] = "INVALID PICTURE FORMAT!"; 
            }
        }

        







        }



    }



echo json_encode($response);
?>