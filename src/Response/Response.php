<?php
	namespace SAC_WebAPI\Response;

	class Response{

		public $data;
		private static $uniqueInstance;
		
		private function __construct(){
		}

		public static function getInstance(){
			if($uniqueInstance == null)
				$uniqueInstance = new Response();			
			$uniqueInstance->changeStatus("200");		
			return $uniqueInstance;
		} 

		public function addInfo($array){
			if($this->$data["body"] == null){
				$this->$data["body"] = [];			
			}
			array_push($this->$data["body"], $array);
		}    


		public function addHeader($name, $content){
			if($this->$data["header"] ==  null){
				$this->$data["header"] = [];
			}
			$this->$data["header"][$name] = $content;
		}


		public function addError($desc){
			if($this->$data["error"] == null){
				$this->$data["error"] = [];
			}
			array_push($this->$data["error"], $desc);
		}

		public function changeStatus($status){
			$this->$data["status"] = $status;
		}

		public function clearData(){
			$this->$data = [];
		}

	}


?>
