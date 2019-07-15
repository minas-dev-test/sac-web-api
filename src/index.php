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

    $router = new Router($_REQUEST);

    //Cria as rotas
    $router->on('GET', 'api/sac', function () {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        return $controller->getTodosTickets();
    });
    
    $router->on('POST', 'api/sac', function () {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        $json = file_get_contents('php://input');
        $data = json_decode($json); 
        $nomeDoUsuario = $data->nome;
        $email = $data->email;
        $telefone = $data->telefone;
        $mensagem = $data->mensagem;
        return $controller->abrirTicket($nomeDoUsuario, $email, $telefone, $mensagem);
    });
    
    $router->on('PUT', 'api/sac/(\w+)', function ($parameters) {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        return $controller->fecharTicket($parameters);
    });
    
    //Recupera o caminho
    $uri = '/'.$_GET['path'];

    $value = $router->run($_SERVER['REQUEST_METHOD'], $uri);

    //Transforma as informações retornadas em um array
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $outp = [];
        if(is_array($value)){
            foreach($value as $key => $line){
                $outp[$key]["ticket_id"] = $line[0];
                $outp[$key]["nome"] = $line[1];
                $outp[$key]["email"] = $line[2];
                $outp[$key]["telefone"] = $line[3];
                $outp[$key]["mensagem"] = $line[4];
                $outp[$key]["aberto"] = $line[5];
            }
            echo json_encode($outp);
        }else{ //Caso o db esteja vazio
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
        }
    }

?>