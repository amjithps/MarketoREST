<?php 

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Marketo\MarketoRest as Marketo;

$client = new Marketo(array(
								    'client_id' => 'df3f3acc-ecf9-4836-8566-e3fba3a33281',
								    'client_secret' => 'Sq0o3nJmHaUnG7mHuyTiORzQ3cO7hF3e',
								    'munchkin_id' => '181-JTR-121'
					));

//$client->getMultipleLeadsByFilterType("email", array("parroyo@alienvault.com"), array("email", "firstName", "lastName"));
//$client->getMultipleLeadsByListId(7165, array("email", "firstName", "lastName"));
//$client->getMultipleLeadsByProgramId("1011")
pr();




function pr($array){
	print "<pre>";
	print_r($array);
	print "</pre>";
}