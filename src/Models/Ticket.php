<?php

namespace SAC_WebAPI\Model;

class Ticket{

    public $ticketId;
    public $nome;
    public $email;
    public $telefone;
    public $mensagem;
    public $aberto;
    public $assunto;

    public function setTicketId($ticketId){
        $this->ticketId = $ticketId;
    }

    public function getTicketId(){
        return $this->ticketId;
    }

    public function setNomeDeUsuario($nome){
        $this->nome = $nome;
    }

    public function getNomeDeUsuario(){
        return $this->nome;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setTelefone($telefone){
        $this->telefone = $telefone;
    }

    public function getTelefone(){
        return $this->telefone;
    }

    public function setMensagem($mensagem){
        $this->mensagem = $mensagem;
    }

    public function getMensagem(){
        return $this->mensagem;
    }

    public function setAberto($aberto){
        $this->aberto = $aberto;
    }

    public function getAberto(){
        return $this->aberto;
    }    


}


?>