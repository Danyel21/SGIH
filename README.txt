SGIH - Sistema de Gestão de Inventário Hospitalar

Nome do projeto: SGIH - Sistema de Gestão de Inventário Hospitalar

Nome do estudante: Pedro Daniel Oliveira

Número do estudante: 1201707

Instruções para instalação e execução:
- Requisitos: PHP >= 7.4, MySQL/MariaDB, servidor web (Laragon, XAMPP, WAMP).
- Copie a pasta `SGIH` para o diretório público do seu servidor (ex.: C:\laragon\www\SGIH).
- Configure a ligação à base de dados em [SGIH/private/includes/db_connect.php](SGIH/private/includes/db_connect.php#L1): atualize `DB_HOST`, `DB_NAME`, `DB_PORT`, `DB_USER`, `DB_PASS` conforme o seu ambiente.
- Crie a base de dados MySQL com o nome definido em `DB_NAME` e importe o schema se tiver um ficheiro de dump (.sql). Se não houver dump, crie as tabelas conforme o modelo do projeto (contacte o autor se necessário).
- Inicie o Apache/NGINX e o MySQL.
- Abra o navegador e aceda a: `http://localhost/sibdas/1201707/SGIH/public/login.php`.

Instruções para realização dos principais testes:
1. Login: efetue o login com as credenciais abaixo.
2. Testes administração (perfil Administrador): gerir utilizadores, fornecedores, ver relatórios, aceder a `private/dashboard/dashboard_admin.php`.

Credenciais de acesso (contas de exemplo):
- Administrador:
  - Email: danyel@ise.com
  - Password: 1234!
- Funcionário:
  - Email: txus@gmail.com
  - Password: TxusvoAFzgsh!


Nota: se estas contas não existirem na sua base de dados, crie-as através da interface `Utilizadores` (menu interno) ou insira-as diretamente na tabela `UTILIZADOR`. Exemplo de template SQL (substitua `<PASSWORD_HASH>` pelo hash gerado por `password_hash()` em PHP):

INSERT INTO UTILIZADOR (nome, email, funcao, departamento, password_hash, ativo) VALUES
('Administrador', 'admin@example.com', 'Administrador', 'Admin', '<PASSWORD_HASH>', 1),
('Técnico', 'tecnico@example.com', 'Técnico', 'Técnicos', '<PASSWORD_HASH>', 1),
('Rececionista', 'rececionista@example.com', 'Rececionista', 'Receção', '<PASSWORD_HASH>', 1),
('Funcionário', 'funcionario@example.com', 'Funcionário', 'Geral', '<PASSWORD_HASH>', 1);

Como gerar o hash da password em PHP (exemplo):
<?php
echo password_hash('Admin123!', PASSWORD_DEFAULT);
?>

Informação adicional relevante para avaliação:
- O ficheiro de configuração da base de dados encontra-se em [SGIH/private/includes/db_connect.php](SGIH/private/includes/db_connect.php#L1). Atualize-o antes de iniciar a aplicação.
- Páginas de login/controlo de sessão: [SGIH/public/login.php](SGIH/public/login.php#L1) e [SGIH/public/logout.php](SGIH/public/logout.php#L1).
