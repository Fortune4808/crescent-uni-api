<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];

}else{
    $me=trim($_POST['me']);
   
    $response = ($me == 1) ? (
        // $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            // Process each row (e.g., push to an array, modify data, etc.)
            $data[] = $row;
        }
        [
            'code' => 200,
            'success' => true,
            'message' => 'SUCCESS! Fetch successful',
            'data' => $data,
        ];
    
    )
    : [
        'code' => 108,'success' => false,'message' => 'its not valid',
        ];
    
    
}











echo json_encode($response);
?>