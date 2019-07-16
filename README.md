# SAC API

API de Serviço de atendimento ao cosumidor usando Docker

## Instalação

Tenha em sua máquina instalado o Docker e o Docker Compose. Para iniciar o projeto execute:

Caso seja a primeira vez:

    docker-compose build

Para iniciar:

    docker-compose up

Por padrão o projeto é inicializado na porta 5000 (<http://localhost:5000/tickets>)

## Rotas

- `/tickets`
  - `GET`: Obtém todos os tickets em JSON
  - `POST`: Cadastra um novo ticket passando os campos `userName`, `userEmail`,`userPhone`, `userMessage` em JSON
  - `OPTIONS`: Atualmente retorna 200 OK para qualquer requisição e
    - access-control-allow-methods: POST, GET, OPTIONS, PUT, DELETE
    - access-control-allow-origin: *
    - access-control-max-age: 86400
- `/tickets/:id`
  - `PUT`: Atualiza o status de um ticket para 0 passando o `id`
  - `DELETE`: Exclui um ticket passando o `id`