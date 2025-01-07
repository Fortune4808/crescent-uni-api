<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];
}else{
    $student_id = trim($_POST['student_id']);
    $expiration_time = date('Y-m-d H:i:s', strtotime('+3 minutes'));

    $student_array = $callclass->_get_student($conn, $student_id);
    $student_detail_array = json_decode($student_array, true);
    $fullname = $student_detail_array[0]['fullname'];
    $email_address = $student_detail_array[0]['email_address'];
    
    $otp = rand(111111,999999);

    $query = $conn->prepare("UPDATE student_tab SET  otp=?, otp_expiration_time = ? WHERE student_id = ?")or die(mysqli_error($conn));
    $query->bind_param("iss", $otp, $expiration_time, $student_id);
    $query->execute();


    $response=[
        'code' => 107,
        'success' => true,
        'message' => 'OTP resent successful',
    ];

}




echo json_encode($response);
?>