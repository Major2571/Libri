# 📚 Libri

Sistema de gerenciamento de biblioteca desenvolvido para facilitar o controle de livros, autores, gêneros, usuários e empréstimos.

O projeto está sendo desenvolvido com **PHP, MySQL, JavaScript e Bootstrap**, seguindo uma estrutura organizada entre modelos, controladores e views.

## 🎯 Objetivo

O **Libri** tem como objetivo fornecer uma aplicação web para gerenciamento de uma biblioteca, permitindo controlar os principais dados do acervo e, posteriormente, as operações de empréstimo e devolução de livros.

## 🚀 Funcionalidades

### Cadastros

* [x] Cadastro de gêneros
* [x] Hierarquia entre gêneros através de gênero pai
* [ ] Cadastro de autores
* [ ] Cadastro de livros
* [ ] Cadastro de usuários

### Gerenciamento

* [x] Listagem de gêneros
* [x] Edição de gêneros
* [x] Exclusão de gêneros
* [ ] Listagem de autores
* [ ] Edição de autores
* [ ] Exclusão de autores
* [ ] Associação de livros a autores e gêneros
* [ ] Controle de disponibilidade dos livros

### Empréstimos

* [ ] Registro de empréstimos
* [ ] Registro de devoluções
* [ ] Controle de livros disponíveis
* [ ] Histórico de empréstimos

> As funcionalidades marcadas como pendentes fazem parte das próximas etapas de desenvolvimento do projeto.

## 🛠️ Tecnologias

* **PHP** — Back-end
* **MySQL** — Banco de dados
* **JavaScript** — Interações e funcionalidades do front-end
* **jQuery** — Manipulação do DOM e requisições
* **Bootstrap** — Interface e responsividade
* **HTML5 / CSS3** — Estrutura e estilização
* **Gulp** — Automação e compilação de assets
## 🛠️ Instalação

### Pré-requisitos

Antes de iniciar o projeto, certifique-se de possuir:

* PHP 8.x ou superior
* MySQL ou MariaDB
* Apache
* Node.js e npm
* Git

### 1. Clonar o projeto

Clone o repositório:

```bash
git clone https://github.com/Major2571/Libri.git
```

Acesse a pasta do projeto:

```bash
cd Libri
```

### 2. Configurar as variáveis de ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

No Windows, o arquivo também pode ser copiado manualmente.

Configure no `.env` as informações de conexão com o banco de dados:

```bash
BD_ENDERECO="localhost"
BD_BASE="libri"
BD_PORTA=3306
BD_USUARIO=root
BD_SENHA=""
```

Também é necessário ajustar as variáveis `DIR`, `DIR_RAIZ` e `URL` conforme o diretório em que o projeto foi instalado.
.

### 3. Configurar o banco de dados

O projeto disponibiliza o arquivo SQL em:

```text
database/libri.sql
```

Esse arquivo já contém a criação do banco de dados `libri`, além da estrutura necessária para o funcionamento do projeto.

#### Pelo phpMyAdmin

1. Acesse o phpMyAdmin.
2. Acesse **Importar**.
3. Selecione o arquivo:

```text
database/libri.sql
```

4. Execute a importação.
5. Após a importação, o banco `libri` e suas tabelas estarão disponíveis.

### 4. Instalar as dependências

Na raiz do projeto, execute:

```bash
npm install
```
ou

```bash
npm i
```

### 5. Compilar os assets

Execute:

```bash
gulp
```

### 6. Executar o projeto

Com o Apache e o MySQL em execução, acesse o projeto pelo navegador:

```text
http://localhost/Libri
```

O sistema estará disponível para utilização.


### 📌 Estrutura do banco

O arquivo `database/libri.sql` contém a estrutura necessária para executar o projeto, permitindo que novos ambientes sejam configurados sem a necessidade de criar as tabelas manualmente.

## 👩‍💻 Desenvolvimento

O projeto utiliza uma organização baseada na separação de responsabilidades entre:

* **Models** — comunicação e operações relacionadas ao banco de dados.
* **Controllers** — processamento das requisições e regras da aplicação.
* **Views** — apresentação das informações ao usuário.

Essa organização facilita a manutenção, evolução e reutilização dos componentes do sistema.

## 📌 Status

🚧 **Em desenvolvimento**

O projeto está sendo desenvolvido de forma incremental, com cada etapa adicionando novas funcionalidades ao sistema.

## 📄 Licença

Este projeto foi desenvolvido para fins acadêmicos.
