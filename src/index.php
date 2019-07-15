<?php
    use SAC_WebAPI\Router\Router;
    use SAC_WebAPI\Controllers\Controller;
    use SAC_WebAPI\DataAccess\DataAccess;

    include_once './Router/Router.php';
    include_once './Models/Ticket.php';
    include_once './DataAccess/DataAccess.php';
    include_once './Controllers/Controller.php';

    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(200);

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
        $nomeDoUsuario = $data->userName;
        $email = $data->userEmail;
        $telefone = $data->userPhone;
        $mensagem = $data->userMessage;
        return $controller->abrirTicket($nomeDoUsuario, $email, $telefone, $mensagem);
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
    
    $uri = '/'.$_GET['path']; // Recupera o caminho

    $value = $router->run($_SERVER['REQUEST_METHOD'], $uri);

    if($_SERVER['REQUEST_METHOD'] == 'GET'){ // Transforma as informações em JSON
        $outp = [];
        if(is_array($value)){
            foreach($value as $key => $line){
                $outp[$key]["ticketId"] = $line[0];
                $outp[$key]["userName"] = $line[1];
                $outp[$key]["userEmail"] = $line[2];
                $outp[$key]["userPhone"] = $line[3];
                $outp[$key]["userMessage"] = $line[4];
                $outp[$key]["ticketStatus"] = $line[5];
            }
            echo json_encode($outp);
        }else{ // Caso o db esteja vazio ou o caminho está incorreto
            http_response_code(204);
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if($value == 0 ){
            http_response_code(500);    
        }else{
            http_response_code(201);    
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        if($value == 0){
            http_response_code(500);
        }else{
            http_response_code(204);
        }
    }else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        if($value == 0){
            http_response_code(500);
        }else{
            http_response_code(204);            
        }
    }

?>