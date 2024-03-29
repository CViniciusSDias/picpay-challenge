# Projeto de solução do desafio PicPay

Projeto solucionando o [desafio proposto pela equipe do PicPay](https://github.com/PicPay/picpay-desafio-backend).

## Subir a aplicação

```shell
make
```

## Executar os testes

```shell
make test
```

## Interromper a aplicação

```shell
make stop
```

## Endpoints

Todos os endpoints (com valores de exemplo) estão definidos no arquivo `test.http`. A seguir estão os mesmos exemplos.

### Novo usuário comum

```http request
POST http://localhost:8123/users
Content-Type: application/json

{
    "type": "common",
    "fullName": "Usuário comum 1",
    "documentNumber": "12345678910",
    "email": "comum@example.org",
    "password": "senha a ser ignorada",
    "initialBalance": 30000
}
```

### Novo usuário lojista

```http request
POST http://localhost:8123/users
Content-Type: application/json

{
    "type": "merchant",
    "fullName": "Usuário lojista 1",
    "documentNumber": "12345678000190",
    "email": "lojista@example.org",
    "password": "senha a ser ignorada",
    "initialBalance": 30000
}
```

### Listagem de usuários

```http request
GET http://localhost:8123/users
Accept: application/json

```

### Realizar transação

```http request
POST http://localhost:8123/transaction
Content-Type: application/json

{
    "value": 100.0,
    "payer": "ID do usuário pagador",
    "payee": "ID do usuário recebedor"
}
```