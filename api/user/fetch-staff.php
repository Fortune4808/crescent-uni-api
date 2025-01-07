<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];
}else{
    $access_key = trim($_GET['access_key']);

    // Validate access key
    $fetch = $callclass->_validate_accesskey($conn, $access_key);
    $array = json_decode($fetch, true);
    $check = $array[0]['check'];
    $login_staff_id = $array[0]['staff_id'];
    $login_role_id = $array[0]['role_id'];

    $response['check'] = $check;

    if ($check== 0){
        $response['response'] = 101; 
        $response['success'] = false;
        $response['message'] = 'Invalid Access Token. Please Log In Again.';
    }else{
        $staff_id= trim($_POST['staff_id']);
        $status_id=($_POST['status_id']);
        $search_txt=($_POST['search_txt']);

        if(empty($staff_id)){

            $search_like="(staff_id like '%$search_txt%' OR 
            fullname like '%$search_txt%' OR
            email_address like '%$search_txt%' OR
            phone_number like '%$search_txt%')";

            $query = mysqli_query($conn, "SELECT a.*, b.status_name, c.role_name FROM staff_tab a, setup_status_tab b, setup_role_tab c WHERE a.status_id=b.status_id  AND a.role_id=c.role_id AND a.status_id LIKE '%$status_id%' AND a.role_id<'$login_role_id' AND $search_like") or die(mysqli_error($conn));
            $count= mysqli_num_rows($query);

            if ($count > 0){
                while($fetch_data = mysqli_fetch_all($query, MYSQLI_ASSOC)){
                $response = [
                    'code' => 150,
                    'success' => true,
                    'message' => 'SUCCESS! Fetch successfully',
                    'data' => $fetch_data,
                ];
                }
            }else{
                $response = [
                    'code' => 150,
                    'success' => false,
                    'message' => 'ERROR! No record found',
                ];
            }
        }else{
            $fetch_staff = mysqli_query($conn, "SELECT a.*, b.status_name, c.role_name FROM staff_tab a, setup_status_tab b, setup_role_tab c WHERE a.staff_id='$staff_id' AND a.status_id=b.status_id  AND a.role_id=c.role_id") or die(mysqli_error($conn));
            $count = mysqli_num_rows($fetch_staff);

            if ($count > 0){
                while($fetch_staff_id = mysqli_fetch_assoc($fetch_staff)){
                $response = [
                    'code' => 150,
                    'success' => true,
                    'message' => 'SUCCESS! Fetch successfully',
                    'data' => $fetch_staff_id,
                ];
                }
            }
            else{
                $response = [
                    'code' => 152,
                    'success' => false,
                    'message' => 'ERROR! Staff_id does not exist',
                ];
            }

        }


    }
}
    







echo json_encode($response);
?>