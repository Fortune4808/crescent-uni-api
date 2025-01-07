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

        $student_id=($_POST['student_id']);
        $status_id=($_POST['status_id']);
        $search_txt=($_POST['search_txt']);
        
        $search_like="(student_id like '%$search_txt%' OR 
        fullname like '%$search_txt%' OR
        email_address like '%$search_txt%' OR
        mobileno like '%$search_txt%')";
        
        if ($student_id== ''){
            $query = mysqli_query($conn, "SELECT a.*, b.status_name, c.entry_code FROM student_tab a, setup_status_tab b, entry_tab c WHERE a.status_id=b.status_id  AND a.entry_id=c.entry_id AND a.status_id LIKE '%$status_id%' AND $search_like") or die(mysqli_error($conn));
        
            if (mysqli_num_rows($query) > 0) {
                $response['response'] = 102;
                $response['success'] = true;
                $response['data'] = []; // Initialize the data array
                
                while ($row = mysqli_fetch_assoc($query)) {
                    $row['documentStoragePath'] = "$documentStoragePath/staff_picture";
                    $response['data'][] = $row; // Append each row to the data array
                }
            } else {
                $response['response'] = 103;
                $response['success'] = false;
                $response['message'] = "NO RECORD FOUND!!!";
            }
            
        } else {
            $query = mysqli_query($conn, "SELECT a.*, b.status_name, c.entry_code,d.faculty_name,e.department_name,f.gender_name,g.level_code FROM student_tab a, setup_status_tab b, entry_tab c,faculty_tab d, department_tab e,setup_gender_tab f,setup_level_tab g WHERE a.status_id=b.status_id AND g.level_id=a.level_id  AND f.gender_id=a.gender_id AND d.faculty_id= e.faculty_id AND e.department_id = a.department_id AND a.entry_id=c.entry_id AND a.status_id AND student_id='$student_id'") or die(mysqli_error($conn));
        
            if (mysqli_num_rows($query) > 0) {
                $response['response']=104;
                $response['success'] = true;
                while($row=mysqli_fetch_assoc($query)){
                    $row['documentStoragePath'] = "$documentStoragePath";
                    $response['data'] = $row;
                }
    
            } else {
                $response['response']=105;
                $response['success'] = false;
                $response['message'] = "NO RECORD FOUND!!!";
            }
        }

    }
}
    
  








echo json_encode($response);
?>