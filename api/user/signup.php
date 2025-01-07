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
    $default_status=3;
    $expiration_time = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    $otp = rand(111111,999999);
    $passport='friends.png';


    if (empty($fullname)){
        $response=[
            'code' => 108,
            'success' => false,
            'message' => 'fullname is required',
        ];   
    }elseif(empty($email_address)){
        $response=[
            'code' => 105,
            'success' => false,
            'message' => 'email is required',
        ];   
    }elseif(empty($mobileno)){
        $response=[
            'code' => 107,
            'success' => false,
            'message' => 'Phone number is required',
        ];   
    } elseif(empty($gender_id)){
        $response=[
            'code' => 101,
            'success' => false,
            'message' => 'gender is required',
        ];   
    }elseif(empty($dob)){
        $response=[
            'code' => 100,
            'success' => false,
            'message' => 'Date of birth is required',
        ];   
    }elseif(empty($department_id)){
        $response=[
            'code' => 106,
            'success' => false,
            'message' => 'Department is required',
        ];   
    }elseif(empty($entry_id)){
        $response=[
            'code' => 106,
            'success' => false,
            'message' => 'entry mode is required',
        ];   
    }else{
        $dob_format = 'Y-m-d';  
        $dob_object = DateTime::createFromFormat($dob_format, $dob);
       


        if (!$dob_object || $dob_object->format($dob_format) !== $dob) {
            $response = [
                'code' => 109,
                'success' => false,
                'message' => 'Invalid Date of Birth format. Please use YYYY-MM-DD',
            ];
        } else {
        
            list($year, $month, $day) = explode('-', $dob);
            if (!checkdate($month, $day, $year)) {
                $response = [
                    'code' => 110,
                    'success' => false,
                    'message' => 'Invalid Date of Birth. Please provide a valid date',
                ];
            } else {
               
                $current_date = new DateTime();
                if ($dob_object > $current_date) {
                    $response = [
                        'code' => 111,
                        'success' => false,
                        'message' => 'Date of Birth cannot be in the future',
                    ];
                } else {
                
                    
                    $age = $current_date->diff($dob_object)->y; 

                    if ($age <= 16) {
                        $response = [
                            'code' => 112,
                            'success' => false,
                            'message' => '16 years Above is allowed',
                        ];
                    }else{
                        if(!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                        $response=[
                            'code' => 108,
                            'success' => false,
                            'message' => 'Enter a valid email address',
                        ];
                    
                        }else{
                            
                            $query= mysqli_prepare($conn, "SELECT * FROM student_tab WHERE email_address= ?")or die(mysqli_error($conn));
                            mysqli_stmt_bind_param($query, 's', $email_address);
                            mysqli_stmt_execute($query);
                            $answer = mysqli_stmt_get_result($query);
                            if ((mysqli_num_rows($answer)) > 0 ){
                                
                                $response=[
                                    'code' => 105,
                                    'success' => false,
                                    'message' => 'Email Addresss already exist',
                                ];   
                            }else{

                            $query= mysqli_prepare($conn, "SELECT * FROM email_verification WHERE email_address= ?")or die(mysqli_error($conn));
                            mysqli_stmt_bind_param($query, 's', $email_address);
                            mysqli_stmt_execute($query);
                            $result = mysqli_stmt_get_result($query);

                
                            if ((mysqli_num_rows($result)) > 0 ){
                                                                                                                              
                                $query = $conn->prepare("UPDATE email_verification SET otp = ?, otp_expiration_time= ? WHERE email_address = ?");
                                $query->bind_param("iss", $otp, $expiration_time,$email_address);
                                $query->execute();

                                $response=[
                                    'code' => 108,
                                    'success' => true,
                                    'fullname' =>$fullname,
                                    'email_address' =>$email_address,
                                    'message' => 'Check email to confirm otp!',
                                ];
                              
                            }else{
                                $stmt = $conn->prepare("INSERT INTO email_verification (email_address, otp, otp_expiration_time, status_id, created_time) VALUES (?, ?, ?,?, NOW())");
                                $stmt->bind_param("sisi", $email_address, $otp, $expiration_time, $default_status);
                                $stmt->execute() or die($stmt->error); 

                                $response=[
                                    'code' => 108,
                                    'success' => true,
                                    'fullname' =>$fullname,
                                    'email_address' =>$email_address,
                                    'message' => 'Check email to confirm otp!',
                                ];
                            }
                        }
                        
                            
                            
                        }
                    }






                }
            }
        }






    }




















}


echo json_encode($response);
?>