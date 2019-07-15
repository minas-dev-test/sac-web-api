<?php

    namespace SAC_WebAPI\DataAccess;

    use SAC_WebAPI\Model\Ticket;

    include_once './Models/Ticket.php';

    class DataAccess{

        private $host;
        private $user;
        private $password;
        private $database;
        private $conn;

        function __construct(){
            // Recupera as variáveis de ambiente criadas no arquivo docker-compose
            $explode_result = explode(";", $_ENV["ConnectionString"]);
            $env_variables = [];
            foreach($explode_result as $variable){
                $variable_explode = explode("=", $variable);
                $env_variables[$variable_explode[0]] = $variable_explode[1];
            }

            $this->host = $env_variables["Server"];
            $this->user = $env_variables["Uid"];
            $this->password = $env_variables["Pwd"];
            $this->database = $env_variables["Database"];

            //Cria a conexão com o bd
            $this->conn = new \mysqli($this->host, $this->user, $this->password, $this->database);
            
            //Testa se a conexão foi bem sucedida
            if ($this->conn->connect_errno) {
                echo "Failed to connect to MySQL: " . $this->conn->connect_error;
            }
        }

        public function abrirTicket($ticket){
            $sql = "INSERT INTO sac_web_api.ticket 
                        (ticket_id,nome,email,telefone,mensagem)
                    VALUES
                        (?,?,?,?,?);";
            
            //Prepara statement
            $stmt = $this->conn->prepare($sql);

            if(!$stmt){
                return 0;
            }

            //Bind parametros
            $stmt->bind_param("sssss",$ticket->ticketId,$ticket->nome,$ticket->email,$ticket->telefone,$ticket->mensagem);

            //Executa statement
            $stmt->execute();

            $stmt->close();

            return 1;
        }

        public function getTodosTickets(){
            $sql = "SELECT
                        ticket_id TicketId,
                        nome NomeDeUsuario,
                        email Email,
                        telefone Telefone,
                        mensagem Mensagem,
                        aberto Aberto
                    FROM
                        sac_web_api.ticket;";

            $result = $this->conn->query($sql);
            $result = $result->fetch_all();
            
            $this->conn->close();
            if($result == NULL){
                return 0;
            }
            return $result;
        }

        public function fecharTicket($id){
            $sql = "UPDATE sac_web_api.ticket SET aberto = '0' WHERE ticket_id = ?";

            $stmt = $this->conn->prepare($sql);
        
            if(!$stmt){
                return 0;
            }

            $stmt->bind_param("s",$id);

            $stmt->execute();

            $stmt->close();

            return $id;
        }

    }

?>
