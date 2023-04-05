<?php  
	#DateTime::createFromFormat('Y-m-d', $dateString);
    #$orgDate = "2019-09-15";  

    $orgDate = DateTime::createFromFormat('d/m/Y H:i:s', "21/02/2019 13:30:37");
    $newDate = $orgDate->format('dMY H:i:s');  
    echo "New date format is: ".$newDate;
?>  
