<?php include '../config/connection.php'; ?>

<?php
if($apiKey!=$expected_api_key){
    $response['code']=100;
    $response['success']=false;
    $response['message']="ACCESS DENIED! You are not authiorized to call this API";
}else{
  
    $query=mysqli_query($conn,"SELECT * FROM setup_gender_tab WHERE gender_id IN (1, 2)")or die (mysqli_error($conn));
    $response['response']=102;
    $response['success']=true;
    while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
        $response['data']=$fetch_query;
    }
    
}	

echo json_encode($response);
?>