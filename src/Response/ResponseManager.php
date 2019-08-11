<?php
	namespace SAC_WebAPI\ResponseManager;

	class ResponseManager{	
	
		public function send($response){
			http_response_code($response->$data["status"]);
			foreach($response->$data["header"] as $key => $value){
				header("$key: $value");
			}
			if($response->$data["body"] != null)
				echo json_encode($response->$data["body"]);			
		}
		
	}

?>
