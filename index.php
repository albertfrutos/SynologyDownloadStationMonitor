<html>

<head>

<title>Download Station Monitor</title>
<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<link type="text/css" rel="stylesheet" href="stylesheet.css"/>


<meta http-equiv="refresh" content="180">	<!-- Auto-refresh every 3 minutes (180 seconds) -->


</head>

<body bgcolor="#C5E5FF">

<div align="right">This page refreshes automatically every 3 minutes<br>Page last time loaded at 
	<script>
		var d = new Date();
		document.write(d.getHours()+":"+d.getMinutes()+":"+d.getSeconds());
	</script>
	
	<br>
</div>

<h1 align="center">Download Station Monitor</h1>

<?php

	//Connection details. MUST MODIFY THEM WITH YOUR OWN VALUES.
	
	$address='192.168.1.44';  	//Replace with the IP where Download Station is running
	$port=5000;					//Replace with the PORT where Download Station is running
	$user=user;					//Replace with your DiskStation USER with which you are downloading files
	$pass=pass;					//Replace with your DiskStation PASSWORD with which you are downloading files
	
	//Constants to convert bytes into Megabytes and Gygabytes (and viceversa)
	
	$mega=1048576;      // MB constant
	$giga=1073741824;	// GB constant


	//Step 1: Get API Information

	$url1='http://'.$address.':'.$port.'/webapi/query.cgi?api=SYNO.API.Info&version=1&method=query&query=SYNO.API.Auth,SYNO.DownloadStation.Task';
	$getinfo=file_get_contents($url1);
	
	//Step 2: Session Login
	
	$url2='http://'.$address.':'.$port.'/webapi/auth.cgi?api=SYNO.API.Auth&version=2&method=login&account='.$user.'&passwd='.$pass.'&session=DownloadStation&format=cookie';
	$login=file_get_contents($url2);
	$decodedlogin=json_decode($login,true);
		
	$sid=$decodedlogin['data'][sid];  //We'll have to send through the URL the Session ID (SID) in each request, or we won't we allowed to acces the data.

	//Step 4: Retrieve general info.

	$url3='http://'.$address.':'.$port.'/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=list&_sid='.$sid.''; //The SID is sent in the las parameter.
	$request=file_get_contents($url3);   				//Request list of downloads 
	$decodedrequest=json_decode($request,true);
	$totaldownloads=$decodedrequest['data']['total'];  //get total number of downloads (for statistics)
	
	//Start building the table with all downloads and details
		
	echo '<table id="maintable" align="center">
	<tr>
	<th>File name</th>
	<th>Size</th>
	<th>Downloaded</th>
	<th>Uploaded</th>
	<th>Progress</th>
	<th>Ratio</th> 	
	<th>Down Speed</th>
	<th>Up Speed</th>
	<th>ETA</th>
	<th>Status</th>
	</tr>';  
	
			/*To show user, type & ID uncomment corresponding lines in step 5 and add:
			<th>Leechers Con.</th>
			<th>Seeders Con.</th>
			<th>Peers Tot.</th>
			<th>Prioridad</th>
			<th>Tipo</th> 
			<th>Usuario</th>
			<th>ID</th>
			after <th>Estado</th> */
	
	//Step 5: Retrieve detailed info for each ID
	
	foreach ($decodedrequest['data']['tasks'] as $theparameter) {  //Get details for each download ID
		
		$id=$theparameter['id'];   		//Get each download ID to get details for each download
		
		echo "<tr>";
		
		$url4='http://'.$address.':'.$port.'/webapi/DownloadStation/task.cgi?api=SYNO.DownloadStation.Task&version=1&method=getinfo&id='.$id.'&additional=detail,transfer&_sid='.$sid.'';  // SID is in the last parameter
				
		$detail=file_get_contents($url4); 			//Request list of download details (for the ID we have selected) 
		$decodeddetail=json_decode($detail,true);
		
		foreach($decodeddetail['data']['tasks'] as $theparameter2){   //Get all the details (the most unuseful ones are disabled, but you can get them by just uncommenting the line for the desired parameter.
			
			//$type=$theparameter2['type'];
			//$username=$theparameter2['username'];
			$title=$theparameter2['title'];
			$size=(float)$theparameter2['size'];
			$status=$theparameter2['status'];
			//$connected_leechers=$theparameter2['additional']['detail']['connected_leechers'];
			//$connected_seeders=$theparameter2['additional']['detail']['connected_seeders'];
			//$priority=$theparameter2['additional']['detail']['priority'];
			//$total_peers=$theparameter2['additional']['detail']['total_peers'];
			$size_downloaded=(float)$theparameter2['additional']['transfer']['size_downloaded'];
			$size_uploaded=(float)$theparameter2['additional']['transfer']['size_uploaded'];
			$speed_download=(float)$theparameter2['additional']['transfer']['speed_download'];
			$speed_upload=(float)$theparameter2['additional']['transfer']['speed_upload'];
			$ratio=round($size_uploaded/$size_downloaded,2);
			
			//Calculate estimate arrival time (ETA) in hours, minutes and seconds.
			
			$etah=(int)((($size-$size_downloaded)/$speed_download)/3600);
			$etam=(int)(((($size-$size_downloaded)/$speed_download)/3600-$etah)*60);
			
			//Transform bytes into MB or GB and round to 2 decimals
					
			$size=round($size/$giga,2);
			$size_downloaded=round($size_downloaded/$giga,2);
			$size_uploaded=round($size_uploaded/$giga,2);
			$speed_download=round($speed_download/$mega,2);
			$speed_upload=round($speed_upload/$mega,2);
		
			
			$progress=$size_downloaded/$size*100;				    //Calculate progress (%) of the download, show title, size, downladeded and uploaded amount of data. Show progress bar, ratio, download and upload speeds, ETA (estimated time of arrival), and status (icon). Most unuseful parameters are disabled, but you can show them by uncommenting the lines corresponding to the desired parameter (also remember to uncomment the lines above, or you'll get an error, as the parameter/varibale won't exist).
			
			//PROGRESS BAR USES HTML5
			
			echo '<td>'.$title.'</td>  								
			<td id="alignedright">'.$size.' GB</td> 
			<td id="alignedright">'.$size_downloaded.' GB</td> 
			<td id="alignedright">'.$size_uploaded.' GB</td>
			
			<td>'.$progress.'% <progress value="'.$size_downloaded.'" max="'.$size.'"></progress></td>
			
			<td id="alignedright">'.$ratio.'</td>
			
			<td id="alignedright">'.$speed_download.' MB/s</td> 
			<td id="alignedright">'.$speed_upload.' MB/s</td>
			<td id="alignedright">'.$etah.'h '.$etam.'m';
			
			//Choose icon for the status and show it

			if($status=="downloading") 			echo '<td align=center><img src="images_status/downloading.png" width=15 height=15 align="center"></td>';
			if($status=="seeding") 				echo '<td align=center><img src="images_status/seeding.png" width=15 height=15 align="center"></td>';
			if($status=="waiting") 				echo '<td align=center><img src="images_status/waiting.png" width=15 height=15 align="center"></td>';
			if($status=="paused") 				echo '<td align=center><img src="images_status/paused.png" width=15 height=15 align="center"></td>';
			if($status=="finishing") 			echo '<td align=center><img src="images_status/finishing.png" width=15 height=15 align="center"></td>';
			if($status=="finished") 			echo '<td align=center><img src="images_status/finished.png" width=15 height=15 align="center"></td>';
			if($status=="hash_checking") 		echo '<td align=center><img src="images_status/hash_checking.png" width=15 height=15 align="center"></td>';
			if($status=="filehosting_waiting") 	echo '<td align=center><img src="images_status/filehosting_waiting.png" width=15 height=15 align="center"></td>';
			if($status=="extracting") 			echo '<td align=center><img src="images_status/extracting.png" width=15 height=15 align="center"></td>';
			if($status=="error") 				echo '<td align=center><img src="images_status/error.png" width=15 height=15 align="center"></td>';


			/*To show user, type & ID uncomment corresponding above lines and add the following lines after the status:
			<td>$connected_leechers</td> 
			<td>$connected_seeders</td> 
			<td>$total_peers</td> 
			<td>$priority</td>
			<td>$type</td> 
			<td>$username</td>
			<td>$id</td>
			 */
			
			
		}
		
		echo "</tr>";

	}
	
	echo "</table>";  //Finish building table
	
	//Request total upload and download speeds (it's no correct to add the ones given by the API, as Download Station has processes running in the background).
	
	$url4='http://'.$address.':'.$port.'/webapi/DownloadStation/statistic.cgi?api=SYNO.DownloadStation.Statistic&version=1&method=getinfo&_sid='.$sid.'';
	$speeds=file_get_contents($url4);       //Get speed data
	$decodedspeeds=json_decode($speeds,true);
	$totaldownspeed=$decodedspeeds['data']['speed_download']; $totaldownspeed=$totaldownspeed/$mega;	//Get download data and convert from bytes/second to MB/s
	$totalupspeed=$decodedspeeds['data']['speed_upload'];  $totalupspeed=$totalupspeed/$mega;			//Get download data and convert from bytes/second to MB/s
	
	echo '<font size="2"> * Speeds may be different than the ones given by the Synology DSM Download Station GUI because of internal parameters that can\'t be accessed through the API actually. </font>';
	
	
	echo "<br><br>";  //Just some breaks between tables to improve the GUI appearance
	
	
	//Start building table of statistics
	//Show total downloads, total down and up speeds.
	
		echo '
		<table id="maintable" align="center">
		<th colspan="4">Statistics</th>
			<tr>
				<td id="alignedright"><b>Total downloads:</b></td> <td id="alignedright">'.$totaldownloads.'</td>			
				<td id="alignedright"><b>Down speed:</b></td> <td id="alignedright">'.round($totaldownspeed,2).' MB/s</td>	
			</tr>
			<tr>
				<td id="alignedright"><b>Up speed:</b></td> <td id="alignedright">'.round($totalupspeed,2).' MB/s</td>
				<td></td><td></td>
			</tr>
		</table>';  //Finish building table
			
?>



<br><br>
</body>


<footer>

The PHP code and CSS style have been developed by (and are property of) Albert Frutos Gonzalo.<br>
Sabadell (Barcelona), November 2013.<br>
E-mail: <a href="mailto:albertfg89@gmail.com">albertfg89@gmail.com</a> - Twitter: <a href="http://twitter.com/albertfrutos">@albertfrutos</a>
LinkedIn: <a href="http://es.linkedin.com/in/albertfrutos/en">Albert Frutos (LinkedIn)</a>
<br>
Feel free to use this, but please mention the original developer of the code and CSS style.

<br><br>
</footer>


</html>