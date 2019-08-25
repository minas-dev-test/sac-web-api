<?php

    namespace SAC_WebAPI\Controllers;

	use SAC_WebAPI\Exceptions\InvalidTicketException;
    use SAC_WebAPI\Model\Ticket;
    use SAC_WebAPI\DataAccess\DataAccess;

    include_once './Exceptions/InvalidTicketException.php';
    include_once './Models/Ticket.php';
    include_once './DataAccess/DataAccess.php';

    class Controller{
        private $dataAccess;

        function __construct($dataAccess){
            $this->dataAccess = $dataAccess;            
        }
        
        public function abrirTicket($nomeDoUsuario, $email, $telefone, $mensagem, $assunto){
            $ticket = new Ticket();
            $ticket->ticketId = uniqid();
            $ticket->nome = $nomeDoUsuario;
            $ticket->email = $email;
            $ticket->telefone = $telefone;
	    	$ticket->telefone = preg_replace("/[^0-9]/", "", $ticket->telefone); // Testar com telefone nulo
            $ticket->mensagem = $mensagem;
            $ticket->assunto = $assunto;

			try{
				$this->validaTicket($ticket);
			}catch(InvalidTicketException $e){
				$exception = new InvalidTicketException();
				$exception->setData($e->getData());
				throw $exception;
				return;
			}

			$this->dataAccess->abrirTicket($ticket);
        }
		
		public function getTodosTickets(){
			$fetch = $this->dataAccess->getTickets();
			
			$resp = [];

			foreach($fetch as $key => $line){
				$info["id"] = $line[0];
				$info["name"] = $line[1];
				$info["email"] = $line[2];
				$info["phone"] = (int) $line[3];
				$info["message"] = $line[4];
				$info["status"] = (int) $line[5];
				$info["subject"] = $line[6];
				$resp[] = $info;
			}

			return $resp;
		}

		public function validaTicket($ticket){
			$valid = [];
			
			if($ticket->nome == null){
				$valid["name"] = "invalid name";
			}
			if($ticket->email == null){
				$valid["email"] = "invalid email";
			}
			if($ticket->telefone == null){
				$valid["phone"] = "invalid phone";
			}
			if($ticket->mensagem == null){
				$valid["message"] = "invalid message";
			}
			if($ticket->assunto == null){
				$valid["subject"] = "invalid subject";
			}

			if(!empty($valid)){
				$valid["invalidField"] = "null field not supported";
				$exception = new InvalidTicketException();
				$exception->setData($valid);
				throw $exception;
			}
		}
	}

?>
