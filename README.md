# SAC API

API de Serviço de atendimento ao cosumidor usando Docker

## Instalação

Tenha em sua máquina instalado o Docker e o Docker Compose. Para iniciar o projeto execute:

    docker-compose up

Por padrão o projeto é inicializado na porta 5000 (<http://localhost:5000/tickets>)

## Rotas

- `/tickets`
  - `GET`: Retorna tickets abertos por padrão
    - Parâmetros (todos podem ser usados juntos):
        - `cod=all` -> Retorna todos os tickets
        - Devem ser usados juntos
        - `skip=x` -> pula uma quantidade `x` de tickets
        - `limits=y` -> retorna uma quantidade `y` de tickets
  - `POST`: Cadastra um novo ticket passando os campos `name`, `email`,`phone`, `message`, `subject` em JSON
    {
        "name":"String",
        "email":"String",
        "phone":"Number",
        "message":"String",
        "subject":"String"
    }
  - `OPTIONS`: Atualmente retorna 200 OK para qualquer requisição e
    - access-control-allow-methods: POST, GET, OPTIONS, PUT, DELETE
    - access-control-allow-origin: *
    - access-control-max-age: 86400
- `/tickets/:id`
  - `PUT`: Atualiza o status de um ticket para 0 passando o `id`
  - `DELETE`: Exclui um ticket passando o `id`
