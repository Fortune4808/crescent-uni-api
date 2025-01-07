<?php include "../config/connection.php" ?>

<?php 
if ($apiKey!= $expected_api_key){

    $response=[
        'code' => 99,
        'success' => false,
        'message' => 'ACCESS DENIED! You are not authorized to call this API',
    ];

}else{

    $query = mysqli_query($conn,"SELECT * FROM setup_religion_tab")or die (mysqli_error($conn));
    while ($fetch_query = mysqli_fetch_all($query, MYSQLI_ASSOC)){
        $response = [
            'code' => 200,
            'success' => true,
            'message' => 'SUCCESS! Fetch successful',
            'data' => $fetch_query,
        ];
    }



    }













echo json_encode($response);
?>