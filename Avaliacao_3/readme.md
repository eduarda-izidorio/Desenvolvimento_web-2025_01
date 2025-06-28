# Sistema de Gerenciamento de Academia de Luta

Este é um sistema web desenvolvido em PHP com MySQL para gerenciar modalidades, professores, alunos e aulas de uma academia de luta. O projeto inclui funcionalidades de cadastro, login, logout e um painel administrativo com operações CRUD (Criar, Ler, Atualizar, Deletar) para as entidades principais, além de controle de acesso baseado no tipo de usuário.

---

## 🚀 Funcionalidades Principais

* **Sistema de Autenticação Seguro**:
    * **Cadastro de Usuário**: Registro de novos usuários com validação de dados (nome completo, e-mail, senha com complexidade mínima, confirmação de senha). Senhas armazenadas com `password_hash()`.
    * **Login**: Autenticação de usuários com verificação de senha via `password_verify()`.
    * **Sessões**: Manutenção do estado de login do usuário.
    * **Logout**: Encerramento seguro da sessão.
* **Dashboard Personalizada**:
    * Painel de controle que redireciona usuários não autenticados para a página de login.
    * Exibe links e funcionalidades diferentes com base no `tipo_usuario` (Admin, Professor, Aluno).
* **Gerenciamento de Entidades (CRUD Completo para Admin)**:
    * **Modalidades**: Adicionar, listar, editar e excluir modalidades de luta.
    * **Professores**: Adicionar (com criação de usuário de login associado), listar, editar e excluir professores (incluindo o usuário de login associado, se houver).
    * **Alunos**: Adicionar (com criação opcional de usuário de login associado), listar, editar e excluir alunos (incluindo matrículas em aulas e usuário de login associado, se houver).
    * **Aulas**: Adicionar (com seleção de modalidade e professor), listar (filtrado por usuário: todas para admin, suas para professor, suas matrículas para aluno), editar e excluir.
    * **Gerenciamento de Matrículas**: Funcionalidade para administradores matricularem/desmatricularem alunos em aulas específicas. Alunos podem se matricular/desmatricular em aulas abertas.

---

## 🛠️ Tecnologias Utilizadas

* **Backend**: PHP
* **Banco de Dados**: MySQL
* **Frontend**: HTML5, CSS3
* **Gerenciamento de Banco de Dados PHP**: PDO (PHP Data Objects) para conexão segura.

---

## ⚙️ Como Rodar o Projeto (Ambiente Local)

Para configurar e rodar este projeto em sua máquina local, siga os passos abaixo:

### Pré-requisitos

* **Servidor Web com PHP e MySQL**: Recomenda-se usar pacotes como **XAMPP**, WAMP Server ou Laragon. Este guia foca no XAMPP.
* **Navegador Web**.

### 1. Instalação e Configuração do XAMPP

1.  **Baixe e Instale o XAMPP**:
    * Acesse [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html) e baixe a versão do XAMPP compatível com seu sistema operacional.
    * Siga as instruções de instalação padrão.

2.  **Inicie os Serviços Apache e MySQL**:
    * Abra o **Painel de Controle do XAMPP**.
    * Clique em `Start` ao lado de **Apache** e **MySQL**. Certifique-se de que ambos estejam rodando (status verde).

### 2. Configuração do Banco de Dados

1.  **Crie o Banco de Dados**:
    * No Painel de Controle do XAMPP, clique em `Admin` ao lado de MySQL para abrir o **phpMyAdmin** no seu navegador (`http://localhost/phpmyadmin`).
    * No phpMyAdmin, clique em `New` ou `Novo` no menu lateral esquerdo.
    * Digite o nome do banco de dados (sugestão: `academia_luta`) e clique em `Create` ou `Criar`.

2.  **Importe o Esquema do Banco de Dados**:
    * Selecione o banco de dados recém-criado no menu lateral esquerdo.
    * Clique na aba `Import` ou `Importar` no topo.
    * Clique em `Choose file` ou `Escolher arquivo` e selecione o arquivo `create_tables_academia.sql` localizado na pasta `sql/` do seu projeto.
    * Role para baixo e clique em `Go` ou `Executar`. As tabelas `usuarios`, `modalidades`, `professores`, `alunos`, `aulas` e `matriculas_aula` serão criadas.

### 3. Copiar os Arquivos do Projeto

1.  **Localize a Pasta `htdocs` do XAMPP**:
    * No Windows, geralmente é `C:\xampp\htdocs\`.
2.  **Copie a Pasta do Projeto**:
    * Copie toda a pasta raiz do seu projeto (a que contém `css/`, `includes/`, `sql/`, `login.php`, `dashboard.php`, etc.), que você nomeou como `Avaliacao 3`, para dentro de `C:\xampp\htdocs\`.
    * A estrutura final será `C:\xampp\htdocs\Avaliacao 3\`.

### 4. Configurar a Conexão com o Banco de Dados

1.  **Edite o Arquivo de Configuração**:
    * Abra o arquivo `includes/db_config.php` no seu editor de código.
    * Atualize as variáveis de conexão com as credenciais do seu MySQL no XAMPP:

    ```php
    <?php
    $host = 'localhost';
    $dbname = 'academia_luta'; // Use o nome do banco de dados que você criou
    $user = 'root';           // Usuário padrão do XAMPP
    $password = '';           // Senha padrão do XAMPP (geralmente vazia)
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
    ?>
    ```

### 5. Acessar a Aplicação

1.  **Abra seu Navegador**:
    * Digite o seguinte endereço na barra de URL: `http://localhost/Avaliacao 3/`
    * Você será redirecionado para `login.php` ou `index.php` (se você criar um `index.php` simples para direcionar).
    * Para acessar diretamente a tela de login: `http://localhost/Avaliacao 3/login.php`

---

## 🔑 Acessando o Painel Administrativo

Para testar as funcionalidades de administrador (Gerenciar Modalidades, Professores, Alunos, Aulas, etc.):

1.  **Cadastre um novo usuário** via `http://localhost/Avaliacao 3/cadastro.php`.
2.  **Acesse o phpMyAdmin** (`http://localhost/phpmyadmin`), vá para o seu banco de dados (`academia_luta`), selecione a tabela `usuarios`.
3.  **Edite o registro** do usuário que você acabou de criar e altere o valor da coluna `tipo_usuario` de `'aluno'` para **`'admin'`**.
4.  Faça login novamente com este usuário. Você verá os links de gerenciamento na dashboard.

---

## 📄 Estrutura do Projeto

projeto_raiz (Avaliacao 3)/
├── css/
│   └── style.css
├── includes/
│   └── db_config.php
│   └── funcoes_crud.php  // Opcional, para funções reutilizáveis
├── sql/
│   └── create_tables_academia.sql
├── admin/
│   └── cadastro_professor.php // Formulário para admins cadastrarem professores
├── alunos/
│   ├── index.php           // Listar, excluir alunos
│   ├── adicionar.php       // Adicionar aluno (com opção de login)
│   └── editar.php          // Editar aluno (com opção de login)
├── professores/
│   ├── index.php           // Listar, excluir professores
│   └── editar.php          // Editar professor (com opção de login)
├── modalidades/
│   ├── index.php           // Listar, excluir modalidades
│   ├── adicionar.php       // Adicionar modalidade
│   └── editar.php          // Editar modalidade
├── aulas/
│   ├── index.php           // Listar aulas (filtrado por tipo de usuário)
│   ├── adicionar.php       // Adicionar aula
│   ├── editar.php          // Editar aula
│   ├── alunos_aula.php     // Gerenciar alunos de uma aula (para admin)
│   └── processar_matricula.php // Lógica de matrícula/desmatrícula para alunos
├── cadastro.php            // Cadastro de novos usuários (padrão 'aluno')
├── dashboard.php           // Painel principal após login
├── index.php               // (Opcional) Página inicial que pode redirecionar para login
├── login.php               // Página de login
└── logout.php              // Página de logout