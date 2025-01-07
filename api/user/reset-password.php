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
 // Automatically set to 5 minutes from the current time
 $expiration_time = date('Y-m-d H:i:s', strtotime('+4 minutes'));

    if (empty($email_address)){     
    $response=[
        'code' => 105,
        'success' => false,
        'message' => 'ERROR! Email Address is empty',
    ];
    }else{

    if(filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $query = mysqli_prepare($conn, "SELECT * FROM student_tab WHERE email_address = ?");
        mysqli_stmt_bind_param($query, 's', $email_address);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        

        if (mysqli_num_rows($result) > 0) {
            $fetch = mysqli_fetch_array($result);
            $student_id = $fetch['student_id'];
            $fullname = $fetch['fullname'];
            $email_address = $fetch['email_address'];
            $status_id = $fetch['status_id'];

            if ($status_id== 1 ){
                $otp = rand(111111,999999);

                $query = $conn->prepare("UPDATE student_tab SET otp = ?, otp_expiration_time = ? WHERE student_id = ?");
                $query->bind_param("iss", $otp, $expiration_time, $student_id);
                $query->execute();

                $response=[
                    'code' => 107,
                    'success' => true,
                    'student_id' => $student_id,
                    'fullname' =>$fullname,
                    'email_address' =>$email_address,
                    'message' => 'OTP sent successfully',
                ];
            }elseif($status_id == 3){
                $response=[
                    'code' => 107,
                    'success' => false,
                    'message' => 'ERROR! Verify your email to reset password',
                ];
            }else{
                $response=[
                    'code' => 107,
                    'success' => false,
                    'message' => 'ERROR! You have been suspended',
                ];
            }


        }else{
            $response=[
                'code' => 107,
                'success' => false,
                'message' => 'ERROR! email does not exist',
            ];
        }

       
    }else{
        $response=[
            'code' => 107,
            'success' => false,
            'message' => 'ERROR! email is invalid',
        ];
    }



    }









}











echo json_encode($response);
?>