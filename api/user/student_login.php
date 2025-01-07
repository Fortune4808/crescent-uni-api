<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];
}else{
    $email_address = trim($_POST['email_address']);
    $password = trim($_POST['password']);

    if (empty($email_address)){
        $response=[
            'code' => 108,
            'success' => false,
            'message' => 'Email address is required',
        ];

    }elseif (empty($password)){
        $response=[
            'code' => 108,
            'success' => false,
            'message' => 'Password is required',
        ];
    }else{

        if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
            $response=[
                'code' => 108,
                'success' => false,
                'message' => 'Enter a valid email address',
            ];         
        }else{
            $query= mysqli_query($conn, "SELECT * FROM student_tab WHERE email_address='$email_address'")or die(mysqli_error($conn));
            $count = mysqli_num_rows($query);

            if ($count > 0 ){       
                $fetch = mysqli_fetch_array($query);
                $status_id = $fetch['status_id'];
                $hashedpassword = $fetch['password'];
                $student_id = $fetch['student_id'];
                $fullname = $fetch['fullname'];
                $entry_id = $fetch['entry_id'];
                $department_id = $fetch['department_id'];

                if(!password_verify($password, $hashedpassword)){                  
                    $response=[
                        'code' => 118,
                        'success' => false,
                        'message' => 'Incorrect password',
                    ];        
                }else{
                    if ($status_id == 1){
                        $access_key = md5($staff_id.date("Ymdhis"));
        
                        $update_accesskey = $conn->prepare("UPDATE student_tab SET access_key=?, last_login=NOW() WHERE student_id = ?");
                        $update_accesskey->bind_param("ss", $access_key,$student_id);
                        $update_accesskey->execute();
                        

                       

                        $query= mysqli_prepare($conn, "SELECT * FROM student_tab WHERE student_id=?")or die(mysqli_error($conn));
                        mysqli_stmt_bind_param($query, 's', $student_id);
                        mysqli_stmt_execute($query);
                        $result = mysqli_stmt_get_result($query);
                        $user_detail = mysqli_fetch_assoc($result);
                        
                        $response=[
                            'code' => 108,
                            'success' => true,
                            'message' => 'Login Successful',
                            'student_id' => $student_id,
                            'access_key' => $access_key,
                          
                           
                        ];         
                    }elseif ($status_id==3){
                        $response=[
                            'code' => 108,
                            'success' => false,
                            'message' => 'Verify your email address',
                        ];        
                    }else{
                        $response=[
                            'code' => 108,
                            'success' => false,
                            'message' => 'Account Suspended',
                        ];        
                    }
                }
               
            }else{
                $response=[
                    'code' => 108,
                    'success' => false,
                    'message' => 'Email does not exist',
                ];        
            }
        }
    }






}














echo json_encode($response);
?>