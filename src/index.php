<?php
    use SAC_WebAPI\Router\Router;
    use SAC_WebAPI\Controllers\Controller;
    use SAC_WebAPI\DataAccess\DataAccess;

    include_once './Router/Router.php';
    include_once './Models/Ticket.php';
    include_once './DataAccess/DataAccess.php';
    include_once './Controllers/Controller.php';

    header("Content-Type: application/json; charset=UTF-8");

    $router = new Router($_REQUEST);

    //Cria as rotas
    $router->on('GET', 'api/sac', function () {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        return $controller->getTodosTickets();
    });
    
    $router->on('POST', 'api/sac', function ($request_data) {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        $nomeDoUsuario = $request_data['nomeDoUsuario'];
        $email = $request_data['email'];
        $telefone = $request_data['telefone'];
        $mensagem = $request_data['mensagem'];
        return $controller->abrirTicket($nomeDoUsuario, $email, $telefone, $mensagem);
    });
    
    $router->on('GET', 'api/sac/(\w+)', function ($parameters) {
        $dataAccess = new DataAccess();
        $controller = new Controller($dataAccess);
        return $controller->fecharTicket($parameters);
    });
    
    //Recupera o caminho
    $uri = '/'.$_GET['path'];

    $value = $router->run($_SERVER['REQUEST_METHOD'], $uri);

    //Transforma as informações retornadas em um array
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
    }

    echo json_encode($outp);

?>