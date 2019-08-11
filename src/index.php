<?php
    use SAC_WebAPI\Controllers\Controller;
    use SAC_WebAPI\DataAccess\DataAccess;
    use SAC_WebAPI\Response\Response;
    use SAC_WebAPI\ResponseManager\ResponseManager;

    include_once './Models/Ticket.php';
    include_once './DataAccess/DataAccess.php';
    include_once './Controllers/Controller.php';
    include_once './Response/Response.php';
    include_once './Response/ResponseManager.php';

    require __DIR__ . '/vendor/autoload.php';

    $router = new \Bramus\Router\Router();
    	
    //=============== Rotas ===============
    $router->get('/tickets', function () {
	//===== Inicia uma nova resposta =====
	$response = Response::getInstance();
	$response->clearData();
	$response->addHeader("Access-Control-Allow-Origin", "*");
	$response->addHeader("Content-Type", "application/json; charset=UTF-8");
	$response->changeStatus("200");
	$respManager = new ResponseManager();
	//====================================	
	try{
        	$dataAccess = new DataAccess();
	}catch(Exception $e){
		$response->changeStatus(500);
		$aux = $e->getMessage();
		$response->addError("$aux");
		$respManager->send($response);
		return;
	}        
	$controller = new Controller($dataAccess);
        $value = $controller->getTodosTickets(); // Acesso ao bd

	//======= Recupera informações =======
	$info = [];
        $cod = $_GET["cod"];
        $limit = $_GET["limit"];
        $skip = $_GET["skip"];
	//====================================
	if($value === 0){ // Problema de comunicação com o bd
	    $response->changeStatus(500);
            $response->addError("db comunnication problem");
	}else if(is_array($value)){ // Acesso ao bd foi bem sucedido
            $write = 1; // 1 -> Adiciona informação para retorno/ 0 -> Não
            $skip_counter = 0;
            $limit_counter = 0;
            foreach($value as $key => $line){ // Percorre todos os tickets
		$skipped = 0; // 1 -> true, 0 -> false
                if($cod == NULL){ // Retorna todos os tickets abertos
                    if($line[5] == 1){ // Se ticket está aberto
                        $write = 1;
                    }else{
                        $write = 0;
                    }
                }else if($cod == "all"){ // Retorna todos os tickets 
                    $write = 1;
                }

                //====== Testa se há algum parâmetro de pesquisa ======
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
		//=====================================================
		
		if($write == 1){
		        if($skip != NULL && $limit != NULL){
		            if($skip > $skip_counter){ // Se ainda 
		                $write = 0;
				$skipped = 1;
		            }else{
		                if($limit <= $limit_counter){
		                    $write = 0;
		                }
		            }
		        }
		}
		
		//============ Monta formato da informação ============
                if($write == 1){
                    $info["id"] = $line[0];
                    $info["name"] = $line[1];
                    $info["email"] = $line[2];
                    $info["phone"] = $line[3];
                    $info["message"] = $line[4];
                    $info["status"] = $line[5];
                    $info["subject"] = $line[6];
		    $response->addInfo($info);
                }
		//=====================================================
		if($skipped == 1){
			$skip_counter++;
		}           
		if($write == 1){ // Se um ticket foi adicionado na resposta
			$limit_counter++;
		}
		
            }
	    $respManager->send($response);
        }
    });
    
    $router->post('/tickets', function () {
	//===== Inicia uma nova resposta =====
	$response = Response::getInstance();
	$response->clearData();
	$response->addHeader("Access-Control-Allow-Origin", "*");
	$response->addHeader("Content-Type", "application/json; charset=UTF-8");
	$response->changeStatus("200");
	$respManager = new ResponseManager(); 
	//====================================
        try{
        	$dataAccess = new DataAccess();
	}catch(Exception $e){
		$response->changeStatus(500);
		$aux = $e->getMessage();
		$response->addError("$aux");
		$respManager->send($response);
		return;
	}    
        $controller = new Controller($dataAccess);
        $json = file_get_contents('php://input'); // Pega o body da requisição como uma string
        $data = json_decode($json);
	//======= Recupera informações =======
        $nome = $data->name;
        $email = $data->email;
        $telefone = $data->phone;
        $mensagem = $data->message;
        $assunto = $data->subject;
	//====================================
	//======== Verifica se algum parâmetro recebido é inválido ========
	if($nome == null || $email == null || $telefone == null || $mensagem == null || $assunto == null){
            $response->addHeader("invalidField", "null field not supported");
      	    if($nome == null){
	    	$response->addHeader("name", "invalid name");
	    }
	    if($email == null){
	    	$response->addHeader("email", "invalid email");
	    }
	    if($telefone == null){
	    	$response->addHeader("phone", "invalid phone");
	    }
	    if($mensagem == null){
	    	$response->addHeader("message", "invalid message");
	    }
	    if($assunto == null){
		$response->addHeader("subject", "invalid subject");
	    }
	    $response->changeStatus(400);
	    $respManager->send($response);
 	    return;
        }
	//=================================================================
        $value = $controller->abrirTicket($nome, $email, $telefone, $mensagem, $assunto); // Acesso ao bd
	if($value === 0){ // Se houve um erro interno no processamento da req 
            $response->changeStatus(500);
	    $response->addError("db statement cannot be prepared");
        }else{ // Se foi bem sucedido
            $response->changeStatus(201);    
        }
	$respManager->send($response);
    });
    
    $router->put('/tickets/(\w+)', function ($parameters) {
	//===== Inicia uma nova resposta =====	
	$response = Response::getInstance();
	$response->clearData();
	$response->addHeader("Access-Control-Allow-Origin", "*");
	$response->addHeader("Content-Type", "application/json; charset=UTF-8");
	$response->changeStatus("200");
	$respManager = new ResponseManager();
	//====================================        
	try{
        	$dataAccess = new DataAccess();
	}catch(Exception $e){
		$response->changeStatus(500);
		$aux = $e->getMessage();
		$response->addError("$aux");
		$respManager->send($response);
		return;
	}  
        $controller = new Controller($dataAccess);
        $value = $controller->fecharTicket($parameters); // Acesso ao bd
        if($value === 0){ // Se houve um erro interno no processamento da req
            $response->changeStatus(500);
        }else{ // Se foi bem sucedido
            $response->changeStatus(200);
        }
    });

    $router->delete('/tickets/(\w+)', function ($parameters) {
	//===== Inicia uma nova resposta =====	
	$response = Response::getInstance();
	$response->clearData();
	$response->addHeader("Access-Control-Allow-Origin", "*");
	$response->addHeader("Content-Type", "application/json; charset=UTF-8");
	$response->changeStatus("200");
	$respManager = new ResponseManager();
	//====================================        
	try{
        	$dataAccess = new DataAccess();
	}catch(Exception $e){
		$response->changeStatus(500);
		$aux = $e->getMessage();
		$response->addError("$aux");
		$respManager->send($response);
		return;
	}  
        $controller = new Controller($dataAccess);
        $value = $controller->excluirTicket($parameters);
	if($value === 0){ // Se houve erro interno no processamento da req
            $response->changeStatus(500);
	    $response->addError("db statement cannot be prepared");
        }else{ // Se foi bem sucedido
            $response->changeStatus(200);           
        }
    });
    
    $router->options('/tickets', function () {
	//===== Inicia uma nova resposta =====	
	$response = Response::getInstance();
	$response->clearData();
	$response->addHeader("Access-Control-Allow-Origin", "*");
	$response->addHeader("Content-Type", "application/json; charset=UTF-8");
	$response->changeStatus("200");
	$respManager = new ResponseManager();     		
	$response->addHeader("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE");
        $response->addHeader("Access-Control-Max-Age", "86400");
	//====================================		
    });
    //=====================================
    $router->run();
?>
