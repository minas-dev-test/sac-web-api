<?php

    namespace SAC_WebAPI\DataAccess;

    use SAC_WebAPI\Model\Ticket;

    include_once './Models/Ticket.php';

    class DataAccess{

        public static $instance;

        private $host;
        private $user;
        private $password;
        private $database;
        private $conn;

        private function __construct(){
        }

        public static function getInstance(){
            if(!isset(self::$instance)){
                self::$instance = new DataAccess();
                self::$instance->connect();
            }

            return self::$instance;
        }

        public function connect(){
            $this->host = $_ENV["SAC_DB_HOST"];
            $this->user = $_ENV["SAC_DB_USER"];
            $this->password = $_ENV["SAC_DB_PASSWORD"];
            $this->database = $_ENV["SAC_DB_NAME"];

            $this->conn = new \mysqli($this->host, $this->user, $this->password, $this->database);
            if ($this->conn->connect_errno) {
		        throw new \Exception($this->conn->connect_error);
            }
        }

        public function abrirTicket($ticket){
            $sql = "INSERT INTO sac_web_api.ticket 
                        (ticket_id,nome,email,telefone,mensagem,assunto)
                    VALUES
                        (?,?,?,?,?,?);";
            
            $stmt = $this->conn->prepare($sql);

            if(!$stmt){
                throw new \Exception("db stmt preparation failed");
                return;
            }

            $stmt->bind_param("ssssss",$ticket->ticketId,$ticket->nome,$ticket->email,$ticket->telefone,$ticket->mensagem,$ticket->assunto);
            $stmt->execute();
            $stmt->close();

        }

        public function getTodosTickets(){
            $sql = "SELECT
                        ticket_id TicketId,
                        nome NomeDeUsuario,
                        email Email,
                        telefone Telefone,
                        mensagem Mensagem,
                        aberto Aberto,
                        assunto Assunto
                    FROM
                        sac_web_api.ticket;";

            $result = $this->conn->query($sql);

            if(!$result){
                throw new \Exception("db query failed");
                $this->conn->close();
                return;
            }

	        $result = $result->fetch_all();
            $this->conn->close();
            return $result;
        }

        public function fecharTicket($id){
            $sql = "UPDATE sac_web_api.ticket SET aberto = '0' WHERE ticket_id = ?";

            $stmt = $this->conn->prepare($sql);
        
            if(!$stmt){
                throw new \Exception("db stmt preparation failed");
                return;
            }

            $stmt->bind_param("s",$id);
            $stmt->execute();
            $stmt->close();

            return $id;
        }

        public function excluirTicket($id){
            $sql = "DELETE FROM sac_web_api.ticket WHERE ticket_id = ?";

            $stmt = $this->conn->prepare($sql);

            if(!$stmt){
                throw new \Exception("db stmt preparation failed");
                return;
            }
            
            $stmt->bind_param("s",$id);
            $stmt->execute();
            $stmt->close();

            return $id;         
        }

        public function getTickets(){
            $sql = "SELECT
                        ticket_id TicketId,
                        nome NomeDeUsuario,
                        email Email,
                        telefone Telefone,
                        mensagem Mensagem,
                        aberto Aberto,
                        assunto Assunto
                    FROM
                        sac_web_api.ticket";

            $cod = mysqli_real_escape_string($this->conn, $_GET["cod"]);
            $limit = mysqli_real_escape_string($this->conn, $_GET["limit"]);
            $skip = mysqli_real_escape_string($this->conn, $_GET["skip"]);
            $pag_sql = "";

            if($limit != null && $skip != null){
                $pag_sql = $pag_sql." LIMIT $limit";
                $pag_sql = $pag_sql." OFFSET $skip";

            }

            $sql = $sql.$pag_sql;

            $search_sql = [];

            $id = mysqli_real_escape_string($this->conn, $_GET["id"]);
            if($id != null){
                $search_sql[] = "ticket_id='$id'"; 
            }

            $nome = mysqli_real_escape_string($this->conn, $_GET["name"]);
            if($nome != null){
                $search_sql[] = "nome='$nome'";
            }

            $email = mysqli_real_escape_string($this->conn, $_GET["email"]);
            if($email != null){
                $search_sql[] = "email='$email'";
            }

            $phone = mysqli_real_escape_string($this->conn, $_GET["phone"]);
            if($phone != null){
                $search_sql[] = "telefone='$phone'";
            }

            $message = mysqli_real_escape_string($this->conn, $_GET["message"]);
            if($message != null){
                $search_sql[] = "mensagem='$message'";
            }

            $status = mysqli_real_escape_string($this->conn, $_GET["status"]);
            if($status != null){
                $search_sql[] = "aberto=$status";
            }else if($cod != "all"){
                $search_sql[] = "aberto=1";
            }

            $subject = mysqli_real_escape_string($this->conn, $_GET["subject"]);
            if($subject != null){
                $search_sql[] = "assunto='$subject'";
            }

            
            if(!empty($search_sql) && $limit == null && $skip == null){
                $aux = array_pop($search_sql);
                $sql = $sql." WHERE $aux";
                foreach($search_sql as $value){
                    $sql = $sql." AND $value";
                }
            }            

            $sql = $sql.";";

            $result = $this->conn->query($sql);

            if(!$result){
                throw new \Exception("db query failed");
                $this->conn->close();
                return;
            }

	        $result = $result->fetch_all();        
            $this->conn->close();
			return $result;

        }

    }

?>
