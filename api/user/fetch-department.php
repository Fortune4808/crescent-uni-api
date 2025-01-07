
<?php include "../config/connection.php" ?>
<?php

if ($apiKey != $expected_api_key) {
    $response['code'] = 100;
    $response['success'] = false;
    $response['message'] = "ACCESS DENIED! You are not authorized to call this API";
} else {
    $faculty_id = trim($_POST['faculty_id']);

    $query = mysqli_query($conn, "SELECT * FROM department_tab WHERE faculty_id = '$faculty_id'") or die(mysqli_error($conn));

    $response['code'] = 102;
    $response['success'] = true;

    $fetch_query = mysqli_fetch_all($query, MYSQLI_ASSOC);
    $response['data'] = $fetch_query;
}

echo json_encode($response);
?>
