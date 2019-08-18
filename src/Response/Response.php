<?php
	namespace SAC_WebAPI\Response;

	class Response{

		private $body, $status, $header, $error; 
		
		public function __construct(){
			$this->body = [];
			$this->data = [];
			$this->error["error"] = [];
			$this->header = [];
			$this->status = 200;
			
			$this->header[] = "Access-Control-Allow-Origin: *";
			$this->header[] = "Content-Type: application/json; charset=UTF-8";
		}

		public function body($body){
			$this->body[] = $body;
			return $this;
		}    


		public function header($header){ // Tem que aceitar array?
			$this->header[] = $header;
			return $this;			
		}


		public function error($error){
			$this->error["error"][] = $error;
			return $this;
		}

		public function status($status){
			$this->status = $status;
			return $this;
		}

		public function send($send = null){
			if($send != null) // Deve substituir o conteudo do body?
				$this->body = $send;

			http_response_code($this->status);
			foreach($this->header as $value){
				header($value);
			}
			
			if(!empty($this->error["error"]))
				$this->body[] = $this->error;

			echo json_encode($this->body);
		}	
	}


?>
