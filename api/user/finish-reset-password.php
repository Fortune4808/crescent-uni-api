<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];
}else{
    $otp = trim($_POST['otp']);
    $password = trim($_POST['password']);
    $student_id = trim($_POST['student_id']);

    if (empty($otp)){
        $response=[
            'code' => 108,
            'success' => false,
            'message' => 'ERROR! OTP is required',
        ];

    }elseif (empty($password)){
        $response=[
            'code' => 108,
            'success' => false,
            'message' => 'ERROR! password is required',
        ];

    } else{
        $current_time = date('Y-m-d H:i:s');

        $otp_stmt = mysqli_prepare($conn, "SELECT * FROM student_tab WHERE student_id= ? AND otp= ?  ")or die(mysqli_error($conn));
        mysqli_stmt_bind_param($otp_stmt, 'si', $student_id, $otp);
        mysqli_stmt_execute($otp_stmt);
        $result = mysqli_stmt_get_result($otp_stmt);
       
        

        $student_otp = mysqli_num_rows($result);

        $response = [
            'code' => 107,
            'success' => false,
            'message' => 'ERROR! password already exist',
        ];
        $hash_password = password_hash($password, PASSWORD_BCRYPT);

        if (!is_numeric($otp)){
            $response = [
                'code' => 107,
                'success' => false,
                'message' => 'ERROR! Enter numbers only',
            ];
        }else{
            
            if ($student_otp > 0){
                $fetch = mysqli_fetch_array($result);
                $otp_expiration_time = $fetch['otp_expiration_time'];
                if (strtotime($current_time) > strtotime($otp_expiration_time)) {
                    $response = [
                        'code' => 109,
                        'success' => false,
                        'message' => 'ERROR! OTP has expired, please request a new one',
                    ];
                }else{

                $query = $conn->prepare("UPDATE student_tab SET `password`= ? WHERE student_id = ? ")or die(mysqli_error($conn));
                $query->bind_param("ss", $hash_password, $student_id);
                $query->execute();

                $response=[
                    'code' => 107,
                    'success' => true,
                    'message' => 'Password reset successful',
                ];
            }
            }else{
                $response=[
                    'code' => 107,
                    'success' => false,
                    'message' => 'Incorrect OTP',
                ];
            }
        }











    }
} 
















echo json_encode($response);
?>