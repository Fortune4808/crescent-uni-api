<?php
    $apiKey = isset($_SERVER['HTTP_APIKEY']) ? $_SERVER['HTTP_APIKEY'] : null;
    $expected_api_key = '43411331-74e2-46fd-8cb5-9dc4742dd15c';

    $ip_address=$_SERVER['REMOTE_ADDR']; //ip used
    $system_name=gethostname();//computer used

    // $row['documentStoragePath'] = "$documentStoragePath/staff_picture";
    $documentStoragePath="http://localhost/crescent_university-api/api/uploaded-files/picture/student_picture/";
?>