<?php 


	   ini_set('mssql.charset', 'UTF-8'); 


      ////////////////////////// begin cut out //////////////////////////////

      $a = mssql_connect('mssqlcon', 'sa', 'Sakorn123');
       

      $b = mssql_connect('mssqlconcas', 'check', 'Sakorn123');
  
        


      $Query =  mssql_query(" SELECT top 2 [ID]
      ,[CardNO]
      ,[Telephone]
      ,[IsSuccess]
    ,[SyncDate]
    FROM [LineSakorn].[dbo].[NoEntitlement] where IsSuccess = 0 and SyncDate = convert(date,getdate()) order by [ID] asc ",$a);



      while ($Result = mssql_fetch_array($Query)) {
       

        $Check = mssql_num_rows(mssql_query(" SELECT * FROM [CAS].[dbo].[Card2Platform] where CardNO = '".trim($Result["CardNO"])."' ",$b));

        $status = "";

        if ($Check == "1") {
          

          echo $Result["CardNO"]." ".$Check."\n";


          #$Cut  = " perl /var/www/html/schedue/digital/cutcard.pl ".$Result["CardNO"]." ";

          #shell_exec( $Cut );
          mssql_query(" exec dbo.sp_Card_Stop '".$Result["CardNO"]."',null ",$b);
          

          sleep(5);


          #$Open  = " perl /var/www/html/schedue/digital/opencard.pl ".$Result["CardNO"]." ";

          #shell_exec( $Open );
          mssql_query(" exec dbo.sp_Card_Restart '".$Result["CardNO"]."',null ",$b);

          $status = "ย้ำสัญญาณการ์ดสำเร็จ";
 

        }else{



          $status = "ย้ำสัญญาณการ์ดไม่สำเร็จ";
 

        }
          


          mssql_query(" update [LineSakorn].[dbo].[NoEntitlement] set IsSuccess = 1 where ID = '".$Result["ID"]."' ",$a);

          notify("ย้ำสัญญาณการ์ด\n".$Result["CardNO"]."\nหมายเลขโทรศัพท์ ".$Result["Telephone"]."\n".$status,"X3Ns5J0u2UhKkoirOm20GIvRyFlNtA3R7LJEizfhGQN");


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