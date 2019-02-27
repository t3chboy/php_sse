<?php


header('Content-Type: text/event-stream'); //since sse works on this header.
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
 
/**
 * [sendMsg Used to send stream messages to client]
 * @return [type] [description]
 */
function sendMsg() {
  session_start();
  
  $searchSessionId = $_SERVER['QUERY_STRING']; //can be used where session are stored in Datastore like redis
  
  $urlListArray = $_SESSION['urllist'];

  //used to close connection after data is fetched from all url
  $totalUrlCount = count( $urlListArray );
  
  $mh = curl_multi_init();
  
  foreach( $urlListArray as $key => $url ){
  	
  	$ch[$key] = curl_init();
      curl_setopt($ch[$key], CURLOPT_URL,$url);
      curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, false);// to fetch data from ssl website
      curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER,false);// to fetch data from ssl website
      curl_setopt($ch[$key], CURLOPT_HEADER,0);
      curl_setopt($ch[$key], CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch[$key], CURLINFO_REDIRECT_URL);
      curl_setopt($ch[$key], CURLOPT_CONNECTTIMEOUT,8);//timeout curl connection with corrseponding url host if data not fetched in 8sec. 
      curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER,1);
   curl_multi_add_handle($mh,$ch[$key]);//add multiple handles.
  
  }
  
  $running=null;
  do {
	  curl_multi_exec($mh, $running);
	  curl_multi_select($mh);
	} while ($running > 0);
 
$i = 0;
foreach($ch as $key => $urlValue){
    
    $i++;
    $pageTitle = get_html_title( curl_multi_getcontent($ch[$key]) );
    echo "id: $i" . PHP_EOL;
    echo "data:".$pageTitle. PHP_EOL;
    echo PHP_EOL; // this will end the message
    ob_flush(); // flushes data to internal php buffers
    flush(); // flushes the php buffer data to web server
    curl_multi_remove_handle($mh, $ch[$key]);

    sleep(5);// delay added for testing
    
    if( $i == $totalUrlCount){
      echo "data:END". PHP_EOL; //END string used to ask client close stream connection forever.
      echo PHP_EOL;
      ob_flush();
      flush();
    }
}	
    
}
sendMsg();

/**
 * [get_html_title To fetch title tag's data from html page]
 * @param  [String] $html [Complete html page]
 * @return [String]       [data fetched from title tag]
 */
function get_html_title($html){
    preg_match("/\<title.*\>(.*)\<\/title\>/isU", $html, $matches);
    return $matches[1];
}

?> 