<?php 

	ini_set('mssql.charset', 'UTF-8');
    $connection = mssql_connect('mssqlcon', 'sa', 'Sakorn123');


	$token = "xwIy9YnB1ByZfiFz9dS4Pe82hLw9o5nRnQdmqnXlBBZ";
 

	 

	$query_str = mssql_query(" select top 50 'Sakorp' as DB,RowOrder,CardNO,IsOpenCard,IsUpdateCASAlready from Sakorp.dbo.CustomerCardLog where IsUpdateCASAlready = 0 order by RowOrder asc ");



	$message_notify = "ดำเนินการการ์ด กุญแจ Sakorp \n";


	while ( $result = mssql_fetch_array($query_str) ) {
		
		$status_auto = "";
		
		if ( $result["IsOpenCard"] == "1" ) {
			

			$string  = " perl /var/www/html/schedue/digital/opencard.pl ".$result["CardNO"]." ";

			$exe =  shell_exec( $string );

			$status_auto = "ต่อ";

		}else
		if ( $result["IsOpenCard"] == "0" ) {
			

			$string  = " perl /var/www/html/schedue/digital/cutcard.pl ".$result["CardNO"]." ";

			$exe =  shell_exec( $string );
 			
 			$status_auto = "ตัด";
		}
 

		mssql_query(" update Sakorp.dbo.CustomerCardLog set IsUpdateCASAlready = 1 where CardNO = '".$result["CardNO"]."' ");

		$message_notify .= $result["CardNO"]." ".$status_auto."\n";

	}
 

	#$message = $Status."การ์ด \n หมายเลข : 9980003200006591 \n สถานะ : สำเร็จ";

	if ( mssql_num_rows($query_str) != 0 ) {
		
		notify($message_notify,$token);

	}

 


function notify($message,$token){

			    $lineapi = $token; 
				$mms =  trim($message); 
				date_default_timezone_set("Asia/Bangkok");
				$con = curl_init();
				curl_setopt( $con, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 
				// SSL USE 
				curl_setopt( $con, CURLOPT_SSL_VERIFYHOST, 0); 
				curl_setopt( $con, CURLOPT_SSL_VERIFYPEER, 0); 
				//POST 
				curl_setopt( $con, CURLOPT_POST, 1); 
				curl_setopt( $con, CURLOPT_POSTFIELDS, "message=$mms"); 
				$headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$lineapi.'', );
			    curl_setopt($con, CURLOPT_HTTPHEADER, $headers); 
				curl_setopt( $con, CURLOPT_RETURNTRANSFER, 1); 
				$result = curl_exec( $con ); 

}

 ?>