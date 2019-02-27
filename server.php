<?php
/**
 * [getuserQuery Used to store user query in session for future use]
 * 
 */
function getuserQuery() {
	session_start();
	$_SESSION['urllist'] = $_POST;
}
 
getuserQuery();

?> 