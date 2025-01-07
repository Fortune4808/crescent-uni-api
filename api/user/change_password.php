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
        $password=trim($_POST['password']);
        $new_password=trim($_POST['new_password']);
        $confirm_new_password=trim($_POST['confirm_new_password']);

        if (empty($password)){
            $response=[
                'code' => 107,
                'success' => false,
                'message' => 'password is required',
            ];
    
        }elseif (empty($new_password)){
            $response=[
                'code' => 109,
                'success' => false,
                'message' => 'New password is required',
            ];
        }elseif (empty($confirm_new_password)){
            $response=[
                'code' => 118,
                'success' => false,
                'message' => 'confirm Password is required',
            ];
        }elseif ($new_password != $confirm_new_password ){
            $response=[
                'code' => 198,
                'success' => false,
                'message' => 'Password mismatch',
            ];
        }else{
        
            $query = mysqli_prepare($conn, "SELECT * FROM student_tab WHERE student_id = ?");
            mysqli_stmt_bind_param($query, 's', $student_id);
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
     
        if (mysqli_num_rows($result) > 0 ){    
            $fetch = mysqli_fetch_array($result);
            $hashedpassword = $fetch['password'];
            
                
            if(!password_verify($password, $hashedpassword)){                  
                $response=[
                    'code' => 118,
                    'success' => false,
                    'message' => 'Incorrect password',
                ];        
            }elseif(password_verify($new_password, $hashedpassword)){
                $response=[
                    'code' => 128,
                    'success' => false,
                    'message' => 'Old password cannot be used',
                ];      
            }else{
                $new_hash_password = password_hash($new_password, PASSWORD_BCRYPT);


                $update_password = $conn->prepare("UPDATE student_tab SET `password`=? WHERE student_id = ?");
                $update_password->bind_param("ss", $new_hash_password,$student_id);
                $update_password->execute();

                $response=[
                    'code' => 198,
                    'success' => true,
                    'message' => ' changed successfully',
                ];        
            }
            
        }else{
            $response=[
                'code' => 177,
                'success' => false,
                'message' => 'student id does not exist',
            ];        
        }
    }
}
  


}





echo json_encode($response);
?>