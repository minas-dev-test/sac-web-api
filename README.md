# SAC API

API de Serviço de atendimento ao cosumidor usando Docker

## Instalação

Tenha em sua máquina instalado o Docker e o Docker Compose. Para iniciar o projeto execute:

    docker-compose build
    docker-compose up

Os serviços de banco de dados e sac-api irão ser inicializados.
Na primeira inicialização, o serviço de banco de dados irá criar o banco e os dados iniciais e irá demorar para inicializar, estando pronto ao omitir a mensagem `MySQL init process done. Ready for start up.`. Nas próximas vezes o processo é instantâneo.

Por padrão o projeto é inicializado na porta 5000 (<http://localhost:5000/tickets>)

## Rotas

- `/tickets`
  - `GET`: Obtém todos os tickets de SAC
  - `POST`: Cadastra um novo ticket passando os campos `userName`, `userEmail`,`userPhone`, `userMessage`
  - `PUT`: Atualiza o status de um ticket passando o `id`
  - `DELETE`: Exclui um ticket passando o `id`