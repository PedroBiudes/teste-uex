# Sistema de Cadastro de Contatos (Teste Técnico)

Este projeto é um sistema web básico para gerenciamento de cadastro de contatos, desenvolvido em Laravel. Ele permite cadastrar, listar, visualizar, editar e excluir informações de contatos.


Para executar este projeto, você precisará ter instalado em sua máquina:
* **PHP:** Versão 7.4
* **Composer:**
* **Vue.js:** 
* **Banco de Dados:** MySQL

Para configurar e executar o projeto localmente, siga os passos abaixo:

1.  **Clone o Repositório:**
    ```bash
    git clone https://github.com/PedroBiudes/teste-uex
    cd teste-uex
    ```

2.  **Instale as Dependências do Composer:**
    composer install

3.  **Copie o Arquivo de Variáveis de Ambiente:**
    cp .env.example .env

4.  **Gere a Chave da Aplicação:**
    php artisan key:generate

5.  **Configure o Banco de Dados:**
    Abra o arquivo `.env` e configure as credenciais do seu banco de dados.

    **Exemplo para MySQL:**
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=seu_banco_de_dados
    DB_USERNAME=seu_usuario
    DB_PASSWORD=sua_senha
    (Crie o banco de dados `seu_banco_de_dados` manualmente no MySQL)

6.  **Execute as Migrações do Banco de Dados:**
    Este comando criará as tabelas necessárias no seu banco de dados.
    ```bash
    php artisan migrate
    ```

7.  **(Opcional) Popule o Banco de Dados com Dados de Teste:**
   
    php artisan db:seed

8.  **Inicie o Servidor de Desenvolvimento Laravel:**
    ```bash
    php -S localhost:8000 -t public
    ```