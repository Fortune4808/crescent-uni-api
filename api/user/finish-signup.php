<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];
}else{
    $fullname=trim(strtoupper($_POST['fullname']));
    $email_address= trim($_POST['email_address']);
    $mobileno= trim($_POST['mobileno']);
    $gender_id=trim($_POST['gender_id']);
    $dob=trim($_POST['dob']);
    $department_id=trim($_POST['department_id']);
    $entry_id=trim($_POST['entry_id']);
    $otp = trim($_POST['otp']);
    $email_address= trim($_POST['email_address']);
    $level_id=trim($_POST['level_id']);
    $status_id=1;
    $current_time = date('Y-m-d H:i:s');
    $currentYear = date("Y");
    $passport='friends.png';
 

    $otp_stmt = mysqli_prepare($conn, "SELECT * FROM email_verification WHERE email_address= ? AND otp= ?  ")or die(mysqli_error($conn));
    mysqli_stmt_bind_param($otp_stmt, 'si', $email_address, $otp);
    mysqli_stmt_execute($otp_stmt);
    $result = mysqli_stmt_get_result($otp_stmt);

    $student_otp = mysqli_num_rows($result);

    
    if ($student_otp > 0){
        $fetch = mysqli_fetch_array($result);
        $otp_expiration_time = $fetch['otp_expiration_time'];
        if (strtotime($current_time) > strtotime($otp_expiration_time)) {
            $response = [
                'code' => 109,
                'success' => false,
                'message' => 'OTP has expired, please request a new one',
            ];
        }elseif (!is_numeric($otp)){
            $response = [
                'code' => 107,
                'success' => false,
                'message' => 'No letter is allowed',
            ];
        }else{
            
      

        $sequence = $callclass->_getSequenceCount($conn, 'STU');
        $array = json_decode($sequence, true);
        $no = $array[0]['no'];


        $student_id = 'STU' . $no . date("Ymdhis");
        $hash_password = password_hash($student_id, PASSWORD_BCRYPT);
            
        if ($entry_id==1){
            $stmt = $conn->prepare("INSERT INTO student_tab (student_id,`password`,fullname, email_address, mobileno,dob, gender_id, department_id, entry_id, status_id, entry_year,level_id,passport, created_time) VALUES (?,?,?, ?, ?, ?, ?, ?, ?, ?,?,'1',?, NOW())");
            $stmt->bind_param("ssssssiiiiis",$student_id,$hash_password, $fullname, $email_address, $mobileno, $dob, $gender_id, $department_id, $entry_id, $status_id,$currentYear,$passport);
            $stmt->execute() or die($stmt->error); 

            $query = $conn->prepare("DELETE FROM email_verification WHERE email_address = ? ")or die(mysqli_error($conn));
            $query->bind_param("s", $email_address);
            $query->execute();

            $response=[
                'code' => 107,
                'success' => true,
                'message' => 'Signup sucessful!',
                'instruction'=>'Your default password is your student id',
            ];
            
        }else{
            $stmt = $conn->prepare("INSERT INTO student_tab (student_id,`password`,fullname, email_address, mobileno,dob, gender_id, department_id, entry_id, status_id, entry_year,level_id,passport,created_time) VALUES (?,?,?, ?, ?, ?, ?, ?, ?, ?,?,'2',?, NOW())");
            $stmt->bind_param("ssssssiiiiis",$student_id,$hash_password, $fullname, $email_address, $mobileno, $dob, $gender_id, $department_id, $entry_id, $status_id,$currentYear,$passport);
            $stmt->execute() or die($stmt->error); 

            $query = $conn->prepare("DELETE FROM email_verification WHERE email_address = ? ")or die(mysqli_error($conn));
            $query->bind_param("s", $email_address);
            $query->execute();

            $response=[
                'code' => 107,
                'success' => true,
                'message' => 'Signup sucessful!',
                'instruction'=>'Your default password is your student id',
            ];
        }
    }
}else{
    $response=[
        'code' => 107,
        'success' => false,
        'message' => 'Incorrect OTP',
    ];
    }
}
    

















echo json_encode($response);
?>