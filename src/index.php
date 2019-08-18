<?php
    use SAC_WebAPI\Controllers\Controller;
    use SAC_WebAPI\DataAccess\DataAccess;
    use SAC_WebAPI\Response\Response;

    include_once './Models/Ticket.php';
    include_once './DataAccess/DataAccess.php';
    include_once './Controllers/Controller.php';
    include_once './Response/Response.php';

    require __DIR__ . '/vendor/autoload.php';

    $router = new \Bramus\Router\Router();
    	
    $router->get('/tickets', function () {
		$response = new Response();	

		try{
			$dataAccess = new DataAccess();
			$controller = new Controller($dataAccess);
			$response->error("Erro critico A")->error("Erro critico B");
			$response->send($controller->getTodosTickets());
		}catch(Exception $e){		
			$response->status(500)->error($e->getMessage())->send();		
		}

    });
    
    $router->post('/tickets', function () {
		$response = new Response();
		
		try{
        	$dataAccess = new DataAccess();
			$controller = new Controller($dataAccess);
			$json = file_get_contents('php://input'); // Pega o body da requisição como uma string
			$data = json_decode($json);
			$nome = $data->name;
			$email = $data->email;
			$telefone = $data->phone;
			$mensagem = $data->message;
			$assunto = $data->subject;
	
			$valid = [];
			
			if($nome == null){
				$valid["name"] = "invalid name";
			}
			if($email == null){
				$valid["email"] = "invalid email";
			}
			if($telefone == null){
				$valid["phone"] = "invalid phone";
			}
			if($mensagem == null){
				$valid["message"] = "invalid message";
			}
			if($assunto == null){
				$valid["subject"] = "invalid subject";
			}

			if(!empty($valid)){
				$valid["invalidField"] = "null field not supported";
				$response->status(400)->send($valid);			
				return;
			}
			
			$controller->abrirTicket($nome, $email, $telefone, $mensagem, $assunto);
			$response->status(201)->send();
		}catch(Exception $e){
			$response->status(500)->error($e->getMessage())->send();
		}

    });
    
    $router->put('/tickets/(\w+)', function ($parameters) {
		$response = new Response();
		try{
			$dataAccess = new DataAccess();
			$dataAccess->fecharTicket($parameters);
			$response->send();
		}catch(Exception $e){
			$response->status(500)->error($e->getMessage())->send();
			return;
		}  
    });

    $router->delete('/tickets/(\w+)', function ($parameters) {
		$response = new Response();
		try{
			$dataAccess = new DataAccess();
			$dataAccess->excluirTicket($parameters);
			$response->send();
		}catch(Exception $e){
			$response->status(500)->error($e->getMessage())->send();
			return;
		}  
			
	});
		
	$router->options('/tickets', function () {
		$response = new Response();  		
		$response->header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
		$response->header("Access-Control-Max-Age: 86400")->send();
	});

	$router->run();
?>
