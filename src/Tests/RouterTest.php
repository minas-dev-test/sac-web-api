<?php

    use PHPUnit\Framework\TestCase;
    use SAC_WebAPI\Router\Router;

    include_once '../Router/Router.php';

    class RouterTest extends TestCase{
        public function setUp(){
            $this->obj = new Router([0 => "1"]);
        }

        public function testOnRun(){
            $this->obj->on('GET', 'teste', function(){
                return 1;
            });

            $this->obj->on('GET', 'teste3/(\w+)', function($parameters){
                return $parameters;
            });

            $this->obj->on('GET', 'teste4', function($request_data){
                return $request_data[0];
            });

            $value = $this->obj->run('GET', '/teste');
            $this->assertEquals($value, 1);

            $value2 = $this->obj->run('POST', '/teste2');
            $this->assertNull($value2);

            $value3 = $this->obj->run('GET', '/teste3/3');
            $this->assertEquals($value3, "3");

            $value4 = $this->obj->run('GET', '/teste4');
            $this->assertEquals($value4, "1");
        }

    }
?>