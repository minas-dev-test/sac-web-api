<?php

    namespace SAC_WebAPI\Controllers;

    use SAC_WebAPI\Model\Ticket;
    use SAC_WebAPI\DataAccess\DataAccess;

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
            $ticket->mensagem = $mensagem;
            $ticket->assunto = $assunto;

            return $this->dataAccess->abrirTicket($ticket);
        }

        public function getTodosTickets(){
            return $this->dataAccess->getTodosTickets();
        }

        public function fecharTicket($id){
            return $this->dataAccess->fecharTicket($id);
        }

        public function excluirTicket($id){
            return $this->dataAccess->excluirTicket($id);
        }
    }


?>