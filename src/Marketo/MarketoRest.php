<?php 
namespace Marketo;

use GuzzleHttp\Client as GuzzleClient;
use Collection as Collection;

// Self classes

class MarketoRest
{
	private $url;
    private $clientId;
    private $clientSecret;
    private $client;
    private $access_token;
    public 	$listId;//id of list to add to
    public 	$leadIds;//array of lead ids to add to list


    public function __construct($credentials)
    {
    	$munchkin_id			= $credentials['munchkin_id'];
        $this->url 				= "https://$munchkin_id.mktorest.com";
        $this->clientId 		= $credentials['client_id'];
        $this->clientSecret 	= $credentials['client_secret'];
        $this->client = new GuzzleClient(array('base_uri' => $this->url));
    }

    /**
     *  Get Marketo API token.
     */
    public function getToken()
    {
       	
        $params = array(
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        );

        $request = $this->client->request('GET', '/identity/oauth/token',array('query' => $params, 'verify' => false));
        $data = $this->jdecode($request);

        if (!isset($data->access_token) || !isset($data->expires_in)) {
            return 'Invalid Marketo credentials response.';
            exit;
        }
        $this->access_token = $data->access_token;
        return array(
            'access_token' => $data->access_token,
            'expires_in' => $data->expires_in
        );
    }

    /*
		Add leads to list.
    */
    public function addLeads($list_id, $leads){
    		$this->getToken();
    		$url = $this->url . "/rest/v1/lists/" . $list_id . "/leads.json?access_token=" . $this->access_token;
    		$requestBody = $this->bodyBuilder($leads);
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$url);
    		curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json','Content-Type: application/json'));
    		curl_setopt($ch, CURLOPT_POST, 1);
    		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
    		curl_getinfo($ch);
    		$response = curl_exec($ch);
            $data = json_decode($response);
    		return $data;
    }


    public function getLeadById($lead_id){
    	$this->getToken();
    	$url = $this->url . "/rest/v1/lead/" . $lead_id . ".json?access_token=" . $this->access_token;
        $response = $this->client->request('GET', $url, [
			    'verify' => false,
		]);
        $data = $this->jdecode($response);
    	return $data;
    }

    public function getMultipleLeadsByFilterType($filterType, $filterValues = array(), $fields  = array(), $nextPageToken = null, $batchSize = null){
    	$this->getToken();
    	$url = $this->url . "/rest/v1/leads.json?access_token=" .  $this->access_token . "&filterType=" . $filterType . "&filterValues=" . $this::csvString($filterValues);
		if (isset($batchSize)){
			$url = $url . "&batchSize=" . $batchSize;
		}
		if (isset($nextPageToken)){
			$url = $url . "&nextPageToken=" . $nextPageToken;
		}
		if(isset($fields)){
			$url = $url . "&fields=" . $this::csvString($fields);
		}
		//$this->pr($url);exit;
		$response = $this->client->request('GET', $url, [
			    'verify' => false,
		]);
        $data = $this->jdecode($response);
    	return $data;
    }

    public function getMultipleLeadsByListId($listId, $fields  = array(), $nextPageToken = null, $batchSize = null){
    	$this->getToken();
    	$url = $this->url . "/rest/v1/list/" . $listId . "/leads.json?access_token=" . $this->access_token;
		if (isset($batchSize)){
			$url = $url . "&batchSize=" . $batchSize;
		}
		if (isset($nextPageToken)){
			$url = $url . "&nextPageToken=" . $nextPageToken;
		}
		if(isset($fields)){
			$url = $url . "&fields=" . $this::csvString($fields);
		}
		//$this->pr($url);exit;
		$response = $this->client->request('GET', $url, [
			    'verify' => false,
		]);
        $data = $this->jdecode($response);
    	return $data;
    }

    public function getMultipleLeadsByProgramId($programId, $fields  = array(), $nextPageToken = null, $batchSize = null){
    	$this->getToken();
    	$url = $this->url . "/rest/v1/leads/programs/" . $programId . ".json?access_token=" . $this->access_token;
		if (isset($batchSize)){
			$url = $url . "&batchSize=" . $batchSize;
		}
		if (isset($nextPageToken)){
			$url = $url . "&nextPageToken=" . $nextPageToken;
		}
		if(isset($fields)){
			$url = $url . "&fields=" . $this::csvString($fields);
		}
		//$this->pr($url);exit;
		$response = $this->client->request('GET', $url, [
			    'verify' => false,
		]);
        $data = $this->jdecode($response);
    	return $data;
    }



    /*********************************/


    private function bodyBuilder($leads){
    		$array = [];
    		foreach($leads as $lead){
    			$member = new \stdClass;
    			$member->id = $lead;
    			array_push($array, $member);
    		}
    		$body = new \stdClass;
    		$body->input = $array;
    		$json = json_encode($body);
    		return $json;
    }

    //private function getToken(){
    //	return $this->access_token;
    //}

    private function jdecode($json_string){
    	return json_decode( (string)  $json_string->getBody() );
    }

    private static function csvString($fields){
		$csvString = "";
		$i = 0;
		foreach($fields as $field){
			if ($i > 0){
				$csvString = $csvString . "," . $field;
			}else if ($i === 0){
				$csvString = $field;
			}	
            $i++;
		}
		return $csvString;
	}

    public function pr($array){
    	print "<pre>";
    	print_r($array);
    	print "</pre>";
    }
    
}