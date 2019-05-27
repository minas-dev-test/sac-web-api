<?php

    use PHPUnit\Framework\TestCase;
    use SAC_WebAPI\Controllers\Controller;
    use SAC_WebAPI\DataAccess\DataAccess;
    use SAC_WebAPI\Model\Ticket;

    include_once '../Models/Ticket.php';
    include_once '../Controllers/Controller.php';
    include_once '../DataAccess/DataAccess.php';

    class ControllerTest extends TestCase{
        public function setUp(){
            $this->dataAccess = new DataAccess();
            $this->obj = new Controller($this->dataAccess);
        }

        public function testAbrirTicket(){
            $result = $this->obj->abrirTicket("joao", "email@gmail.com", "7777777", "hey");
            $this->assertEquals($result, 1);
            $tickets = $this->obj->getTodosTickets();
            if($tickets != NULL){
                if($tickets[0] != NULL){
                    $this->assertCount(6, $tickets[0]);
                }
            }
        }

        public function testFecharTicket(){
            $result1 = $this->obj->fecharTicket("1");
            $this->assertEquals($result1, "1");
        }

        public function testGetTodosTickets(){
            $result = $this->obj->getTodosTickets();
            if($result != NULL){
                if($result[0] != NULL){
                    $this->assertCount(6, $result[0]);
                }
            }
        }
    }

//Como testar funções que se complementam como abrirTicket e getTicket
?>

