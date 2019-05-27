<?php
    use SAC_WebAPI\Router\Router;
    use SAC_WebAPI\Controllers\Controller;
    use SAC_WebAPI\DataAccess\DataAccess;

    include_once './Router/Router.php';
    include_once './Models/Ticket.php';
    include_once './DataAccess/DataAccess.php';
    include_once './Controllers/Controller.php';

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

    if(is_array($value)){
        foreach($value as $line){
            foreach($line as $column){
                echo $column;
                echo " | ";
            }
            echo "</br>";
        }
    }

?>