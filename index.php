<?php

?>

<!DOCTYPE html>
<html>
<head>
	<title>Fetch Url Data</title>
</head>
<body>
<form id="urlform" name="urlform">
	<div>
		<label id="labelurl">Add multiple urls below</label>
	</div>
	<div id="urlbox">
		<input id="texturl" name="texturl1" type="text" value="http://www.google.com">	
	 </div>
	 <div>
	 	<input type="button" value="addmore" name="addmorebutton" id="addmorebutton">
	 </div>
	 <div>
	 	<input type="button" value="submit" id="submiturl">
	 </div>
</form>
<div id="stream">
	
</div>
<div id="streamdata">
	

</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script language="javascript">

$("#submiturl").click( () => {
	$.ajax({
  url: "server.php",
  method: "POST",
  data: $("#urlform").serialize(),
  success :function( response ){
  	$("#stream").append("<br>Requesting Server to stream data");
  	startFetching( response )
  },
  dataType: "text"
});
});	

var cloneCount = 1;
$("#addmorebutton").click(  () => {	
	$("#texturl").clone().attr('id', 'texturl'+ cloneCount++).attr('name', 'texturl'+ cloneCount++).insertAfter("#texturl");
	$('<br>').insertAfter("#texturl");
});

function startFetching( response ){	
	var stream = new EventSource('datastream.php/?searchID='+response);

	stream.addEventListener('message', function (event) {
	    console.log(event.data);
	    $("#streamdata").append("<p>Stream Data Received:</p>");
	    if( event.data === 'END' ){ //close the stream connection.
	    	stream.close();
	    }else if(event.data == ""){
	    	$("#streamdata").append("<br/>NA");
	    }
	    else{
	    	$("#streamdata").append("<br/>"+event.data);	
	    }
	});
}

</script>

</html>