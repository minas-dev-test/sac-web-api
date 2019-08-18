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

        function __construct(){
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
			$fetch = $this->dataAccess->getTodosTickets();

			if(!$fetch)
				return null;
			
			$info = [];
			$resp = [];
			$cod = $_GET["cod"];
			$limit = $_GET["limit"];
			$skip = $_GET["skip"];
			$write = 1; // 1 -> Adiciona informação para retorno/ 0 -> Não
			$skip_counter = 0;
			$limit_counter = 0;
			foreach($fetch as $key => $line){ // Percorre todos os tickets
				$skipped = 0; // 1 -> true, 0 -> false
				if($cod == NULL){ // Retorna todos os tickets abertos
					if($line[5] == 1){ // Se ticket está aberto
						$write = 1;
					}else{
						$write = 0;
					}
				}else if($cod == "all"){ // Retorna todos os tickets 
					$write = 1;
				}

				if($_GET["id"] != NULL && $_GET["id"] != $line[0]){
					$write = 0;
				}

				if($_GET["name"] != NULL && $_GET["name"] != $line[1]){
					$write = 0;
				}

				if($_GET["email"] != NULL && $_GET["email"] != $line[2]){
					$write = 0;
				}

				if($_GET["phone"] != NULL && $_GET["phone"] != $line[3]){
					$write = 0;
				}

				if($_GET["message"] != NULL && $_GET["message"] != $line[4]){
					$write = 0;
				}

				if($_GET["status"] != NULL && $_GET["status"] != $line[5]){
					$write = 0;
				}

				if($_GET["subject"] != NULL && $_GET["subject"] != $line[6]){
					$write = 0;
				}
				
				if($write == 1){
					if($skip != NULL && $limit != NULL){
						if($skip > $skip_counter){
							$write = 0;
						$skipped = 1;
						}else{
							if($limit <= $limit_counter){
								$write = 0;
							}
						}
					}
				}
				
				if($write == 1){
					$info["id"] = $line[0];
					$info["name"] = $line[1];
					$info["email"] = $line[2];
					$info["phone"] = $line[3];
					$info["message"] = $line[4];
					$info["status"] = $line[5];
					$info["subject"] = $line[6];
					$resp[] = $info;
				}

				if($skipped == 1){
					$skip_counter++;
				}           
				if($write == 1){ // Se um ticket foi adicionado na resposta
					$limit_counter++;
				}
			
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
