<?php
class genClass{

	function _getSequenceCount($conn, $count_id){
		$count = mysqli_fetch_array(mysqli_query($conn, "SELECT count_value FROM setup_master_count WHERE count_id = '$count_id' FOR UPDATE"));
		$num = $count[0] + 1;
		mysqli_query($conn, "UPDATE `setup_master_count` SET `count_value` = '$num' WHERE count_id = '$count_id'") or die(mysqli_error($conn));
		if ($num < 10) {
			$no = '000' . $num;
		} elseif ($num >= 10 && $num < 100) {
			$no = '00' . $num;
		} elseif ($num >= 100 && $num < 1000) {
			$no = '0' . $num;
		} else {
			$no = $num;
		}
		return '[{"num":"' . $num . '","no":"' . $no . '"}]';
	}



	function _get_student($conn, $student_id){
		$query=mysqli_query($conn,"SELECT * FROM student_tab WHERE student_id = '$student_id'");
		$fetch_query=mysqli_fetch_array($query);
		$student_id=$fetch_query['student_id'];
		$fullname=$fetch_query['fullname'];
		$email_address=$fetch_query['email_address'];
		$phone_number=$fetch_query['phone_number'];
		$status_id=$fetch_query['status_id'];
		$password=$fetch_query['password'];
		$otp=$fetch_query['otp'];
		$date=$fetch_query['date'];
		$last_login=$fetch_query['last_login'];
		
		return '[{"student_id":"'.$student_id.'","fullname":"'.$fullname.'","email_address":"'.$email_address.'","mobileno":"'.$mobileno.'","status_id":"'.$status_id.'","password":"'.$password.'","otp":"'.$otp.'","date":"'.$date.'","last_login":"'.$last_login.'","passport":"'.$passport.'"}]';
	}



	function _validate_accesskey($conn,$access_key){
		$query = mysqli_query($conn,"SELECT * FROM student_tab WHERE access_key='$access_key' AND status_id=1;")or die (mysqli_error($conn));
		$count = mysqli_num_rows($query);
			if ($count > 0){
				$fetch_query=mysqli_fetch_array($query);
				$student_id=$fetch_query['student_id'];
	
				
				$check=1; 
			}else{
				$check=0;
			}
			return '[{"student_id":"'.$student_id.'","check":"'.$check.'"}]';
		}

}

$callclass = new genClass();


