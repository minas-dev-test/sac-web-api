<?php
    namespace SAC_WebAPI\Exceptions;
    
    class InvalidTicketException extends \Exception{

        private $data;

        public function setData($data){
            $this->data = $data;
        }

        public function getData(){
            return $this->data;
        }

    }


?>