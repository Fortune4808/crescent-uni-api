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
    $expiration_time = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $otp = rand(111111,999999);

    $query = $conn->prepare("UPDATE email_verification SET  otp=?, otp_expiration_time = ? WHERE email_address = ?")or die(mysqli_error($conn));
    $query->bind_param("iss", $otp, $expiration_time, $email_address);
    $query->execute();


    $response=[
        'code' => 107,
        'success' => true,
        'message' => 'OTP resent successful',
    ];

}




echo json_encode($response);
?>