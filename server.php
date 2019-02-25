<?php
/**
 * [getuserQuery Used to store user query in session for future use]
 * @return [type] [description]
 */
function getuserQuery() {
	session_start();
	$_SESSION['urllist'] = $_POST;
	echo session_id()
}
 
getuserQuery();

?> 