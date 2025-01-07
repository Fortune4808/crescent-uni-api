
<?php include "../config/connection.php" ?>
<?php

if ($apiKey != $expected_api_key) {
    $response['code'] = 100;
    $response['success'] = false;
    $response['message'] = "ACCESS DENIED! You are not authorized to call this API";
} else {
    $state_of_origin_id = trim($_POST['state_of_origin_id']);

    $query = mysqli_query($conn, "SELECT * FROM local_gov_tab WHERE state_of_origin_id ='$state_of_origin_id'") or die(mysqli_error($conn));

    $response['code'] = 102;
    $response['success'] = true;

    $fetch_query = mysqli_fetch_all($query, MYSQLI_ASSOC);
    $response['data'] = $fetch_query;
}

echo json_encode($response);
?>
