<?php
    use SAC_WebAPI\Router\Router;
    use SAC_WebAPI\Controllers\Controller;
    use SAC_WebAPI\DataAccess\DataAccess;

    include_once './Router/Router.php';
    include_once './Models/Ticket.php';
    include_once './DataAccess/DataAccess.php';
    include_once './Controllers/Controller.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("HTTP/1.1 200 OK");

    $router = new Router();

    // Cria as rotas
    $router->on('GET', 'tickets', function () {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        return $controller->getTodosTickets();
    });
    
    $router->on('POST', 'tickets', function () {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        $json = file_get_contents('php://input'); // Pega o body da requisição como uma string
        $data = json_decode($json); 
        $nome = $data->name;
        $email = $data->email;
        $telefone = $data->phone;
        $mensagem = $data->message;
        $assunto = $data->subject;
        if($nome == NULL || $email == NULL || $telefone == NULL || $mensagem == NULL || $assunto == NULL){
            header("invalidField: Campo(s) invalido(s)");
            return -1;
        }
        return $controller->abrirTicket($nome, $email, $telefone, $mensagem, $assunto);
    });
    
    $router->on('PUT', 'tickets/(\w+)', function ($parameters) {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        return $controller->fecharTicket($parameters);
    });

    $router->on('DELETE', 'tickets/(\w+)', function ($parameters) {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        return $controller->excluirTicket($parameters);
    });
    
    $router->on('OPTIONS', 'tickets', function () {
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Max-Age: 86400");
    });
    

    $uri = '/'.$_GET['path']; // Recupera o caminho

    $value = $router->run($_SERVER['REQUEST_METHOD'], $uri);

    if($_SERVER['REQUEST_METHOD'] == 'GET'){ // Transforma as informações em JSON
        $outp = [];
        $cod = $_GET["cod"];
        $limit = $_GET["limit"];
        $skip = $_GET["skip"];
        if(is_array($value)){
            $counter = 0;
            $write = 1;
            $skip_counter = 0;
            $limit_counter = 0;
            foreach($value as $key => $line){
                if($cod == NULL){ // Retorna todos os tickets abertos
                    if($line[5] == 1){
                        $write = 1;
                    }else{
                        $write = 0;
                    }
                }else if($cod == "all"){ // Retorna todos os tickets 
                    $write = 1;
                }

                if($skip != NULL && $limit != NULL){
                    if($skip > $skip_counter){
                        $write = 0;
                    }else{
                        if($limit <= $limit_counter){
                            $write = 0;
                        }
                    }
                }

                // Testa se há algum parâmetro de busca
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
                    $outp[$counter]["id"] = $line[0];
                    $outp[$counter]["name"] = $line[1];
                    $outp[$counter]["email"] = $line[2];
                    $outp[$counter]["phone"] = $line[3];
                    $outp[$counter]["message"] = $line[4];
                    $outp[$counter]["status"] = $line[5];
                    $outp[$counter]["subject"] = $line[6];
                }

                if($write == 1){
                    $counter++;
                }

                if($skip != NULL && $limit != NULL){
                    if($skip > $skip_counter){
                        $skip_counter++;
                    }else{
                        if($write == 1){
                            $limit_counter++;
                        }
                    }
                }
            }
            echo json_encode($outp);
        }else{
            http_response_code(200);
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($value == 0 ){
            http_response_code(500);    
        }else if($value == -1){
            http_response_code(400);                
        }else{
            http_response_code(201);    
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        if($value == 0){
            http_response_code(500);
        }else{
            http_response_code(200);
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        if($value == 0){
            http_response_code(500);
        }else{
            http_response_code(200);            
        }
    }

?>
