<?php
define('_VALID_ACCESS',1);
require_once('include/data_dir.php');
if(!file_exists(DATA_DIR.'/config.php'))
	die();

if(!is_writable(DATA_DIR))
	die();

require_once('include/config.php');
require_once('include/database.php');

if(defined('CID')) {
	if(constant('CID')!==false) die('alert(\'Invalid update script defined custom CID. Please try to refresh site manually.\');');
} else
	define('CID',false); //i know that i won't access $_SESSION['client']

require_once('include/session.php');

$client_id = isset($_SESSION['num_of_clients'])?$_SESSION['num_of_clients']:0;
$client_id_next = $client_id+1;
if($client_id_next==5) $client_id_next=0;
$_SESSION['num_of_clients'] = $client_id_next;
DB::Execute('DELETE FROM session_client WHERE session_name=%s AND client_id=%d',array(session_id(),$client_id));
session_commit();

?>Epesi.init(<?php print($client_id); ?>,'<?php print(rtrim(str_replace('\\','/',dirname($_SERVER['PHP_SELF'])),'/').'/process.php'); ?>','<?php print(http_build_query($_GET));?>');
