# Documentação do Sistema - Escola de Esportes

**Última atualização:** 2025-01-27  
**Versão do Banco de Dados:** 1 (version_01)  
**Último Patch Aplicado:** 001_0007

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Arquitetura do Sistema](#arquitetura-do-sistema)
3. [Estrutura de Pastas](#estrutura-de-pastas)
4. [Configurações](#configurações)
5. [Banco de Dados](#banco-de-dados)
6. [Rotas e Controllers](#rotas-e-controllers)
7. [Models](#models)
8. [Views](#views)
9. [Sistema de Patches](#sistema-de-patches)
10. [Segurança](#segurança)
11. [Assets e Frontend](#assets-e-frontend)

---

## 🎯 Visão Geral

Sistema Web completo para gestão de uma escola de esportes desenvolvido em **PHP 8.2** com arquitetura **MVC própria**, utilizando:
- **Servidor:** XAMPP (Apache + PHP 8.2 + MySQL 8)
- **Banco de Dados:** MySQL 8.0+ com engine InnoDB
- **Autoloading:** PSR-4 (via Composer ou autoloader próprio)
- **Roteamento:** Front Controller com mod_rewrite
- **Padrões:** PSR-12 (estilo de código), PSR-4 (autoloading)

### Funcionalidades Principais

- ✅ Cadastro de Alunos e Responsáveis
- ✅ Gestão de Professores
- ✅ Modalidades Esportivas
- ✅ Turmas e Horários
- ✅ Matrículas
- ✅ Planos de Mensalidade
- ✅ Módulo Financeiro (Mensalidades e Pagamentos)
- ✅ Configurações Financeiras (Multa e Juros)
- ✅ Sistema de Autenticação
- ✅ Dashboard

---

## 🏗️ Arquitetura do Sistema

### Padrão MVC

O sistema utiliza uma arquitetura MVC (Model-View-Controller) customizada:

```
Request → Router → Controller → Model → Database
                ↓
              View → Response
```

### Componentes Core

#### 1. Router (`app/Core/Router.php`)
- Gerencia rotas e direcionamento de requisições
- Suporta rotas nomeadas com parâmetros: `/alunos/{id}`
- Métodos HTTP: GET, POST
- Resolve rotas e despacha para controllers

#### 2. Controller (`app/Core/Controller.php`)
- Classe base abstrata para todos os controllers
- Funcionalidades:
  - Redirecionamento
  - Respostas JSON
  - Validação CSRF
  - Proteção contra duplo submit
  - Gerenciamento de sessão

#### 3. Model (`app/Core/Model.php`)
- Classe base abstrata para todos os models
- Conexão PDO Singleton
- Operações CRUD básicas:
  - `find($id)` - Busca por ID
  - `all($conditions, $orderBy, $limit)` - Lista registros
  - `create($data)` - Insere registro
  - `update($id, $data)` - Atualiza registro
  - `delete($id)` - Remove registro
- Suporte a transações

#### 4. View (`app/Core/View.php`)
- Gerencia renderização de templates
- Suporta layouts (header + footer)
- Extração de variáveis para escopo da view

---

## 📁 Estrutura de Pastas

```
mensalidade/
├── app/
│   ├── Core/                    # Classes base do MVC
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   └── View.php
│   ├── Controllers/             # Controllers da aplicação
│   │   ├── AlunosController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── FinanceiroController.php
│   │   ├── HomeController.php
│   │   ├── MatriculasController.php
│   │   ├── ModalidadesController.php
│   │   ├── PlanosController.php
│   │   ├── ProfessoresController.php
│   │   └── TurmasController.php
│   ├── Models/                  # Models (camada de dados)
│   │   ├── Aluno.php
│   │   ├── Matricula.php
│   │   ├── Mensalidade.php
│   │   ├── Modalidade.php
│   │   ├── Pagamento.php
│   │   ├── Plano.php
│   │   ├── Professor.php
│   │   ├── Turma.php
│   │   └── Usuario.php
│   ├── Views/                   # Templates PHP
│   │   ├── alunos/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── financeiro/
│   │   ├── home/
│   │   ├── matriculas/
│   │   ├── modalidades/
│   │   ├── planos/
│   │   ├── professores/
│   │   ├── turmas/
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── layout.php
│   │   └── layout-auth.php
│   └── autoload.php             # Autoloader PSR-4 simples
├── config/
│   ├── config.php               # Configurações gerais
│   └── database.php             # Configurações do banco
├── database/
│   ├── escola_esportes_db/
│   │   └── main_db.sql          # Estrutura completa do BD
│   └── patches/
│       └── version_01/           # Patches de atualização
│           ├── patch_001_0001.php
│           ├── patch_001_0001.sql
│           ├── patch_001_0002.php
│           ├── patch_001_0002.sql
│           ├── ... (até patch_001_0006)
├── public/                      # Pasta pública (DocumentRoot)
│   ├── index.php                # Front Controller
│   ├── .htaccess                # Regras mod_rewrite
│   └── assets/
│       ├── css/
│       ├── js/
│       └── datatables/
├── Docs/                        # Documentação
│   └── DOCUMENTACAO_SISTEMA.md  # Este arquivo
├── .htaccess                    # Redireciona para /public
├── index.php                    # Redireciona para /public/index.php
├── patch.php                    # Interface de aplicação de patches
├── autoload.php                 # Autoloader alternativo
├── composer.json                # Dependências e autoload PSR-4
└── .gitignore
```

---

## ⚙️ Configurações

### config/config.php

```php
// Ambiente
ENVIRONMENT = 'development' | 'production'

// URLs
BASE_URL = 'http://localhost/mensalidade'
ASSETS_URL = BASE_URL . '/public/assets'

// Caminhos
ROOT_PATH = dirname(__DIR__)
APP_PATH = ROOT_PATH . '/app'
PUBLIC_PATH = ROOT_PATH . '/public'
STORAGE_PATH = ROOT_PATH . '/storage'
LOG_PATH = STORAGE_PATH . '/logs'

// Segurança
CSRF_TOKEN_NAME = 'csrf_token'
SESSION_LIFETIME = 7200 (2 horas)
```

### config/database.php

```php
[
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'escola_esportes_db',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
]
```

### .htaccess (Raiz)

Redireciona todas as requisições para a pasta `public/`.

### public/.htaccess

- Habilita mod_rewrite
- Redireciona requisições para `index.php` (Front Controller)
- Configurações de segurança (headers HTTP)
- Bloqueia acesso a arquivos sensíveis

---

## 🗄️ Banco de Dados

### Estrutura Geral

**Banco:** `escola_esportes_db`  
**Charset:** utf8mb4  
**Collation:** utf8mb4_unicode_ci  
**Engine:** InnoDB

### Tabelas Principais

#### 1. usuarios
Usuários do sistema (Admin, Financeiro, Professor, Atendente)
- `id`, `nome`, `email`, `senha_hash`, `perfil`, `ativo`, `dt_cadastro`, `dt_atualizacao`

#### 2. responsaveis
Responsáveis pelos alunos
- `id`, `nome`, `cpf`, `contato`, `email`, `dt_cadastro`, `dt_atualizacao`

#### 3. alunos
Cadastro de alunos
- `id`, `nome`, `nome_pai`, `telefone_pai`, `email_pai`, `telegram_pai`
- `nome_mae`, `telefone_mae`, `email_mae`, `telegram_mae`
- `cpf`, `rg`, `cpf_responsavel`, `responsavel_id`
- `dt_nascimento`, `sexo`, `tipo_sanguineo`, `alergias`, `observacoes_medicas`
- `contato`, `contato_emergencia`, `nome_contato_emergencia`
- `email`, `endereco`, `status`, `dt_cadastro`, `dt_atualizacao`

#### 4. professores
Cadastro de professores
- `id`, `nome`, `cpf`, `rg`, `dt_nascimento`, `sexo`
- `registro_cref`, `contato`, `email`, `endereco`
- `formacao_academica`, `certificacoes`, `experiencia_profissional`
- `especialidade`, `valor_hora`
- `banco_nome`, `banco_agencia`, `banco_conta`, `banco_tipo_conta`, `banco_pix`
- `contato_emergencia`, `nome_contato_emergencia`, `observacoes`
- `status`, `dt_cadastro`, `dt_atualizacao`

#### 5. professor_modalidades
Relacionamento N:N entre professores e modalidades
- `id`, `professor_id`, `modalidade_id`, `dt_cadastro`

#### 6. modalidades
Modalidades esportivas (Natação, Lutas, Futebol, etc.)
- `id`, `nome`, `categoria_etaria`, `descricao`, `ativo`, `dt_cadastro`

#### 7. planos
Planos de mensalidade (mensal, trimestral, anual)
- `id`, `nome`, `periodicidade`, `quantidade_meses`, `valor_base`, `descricao`, `ativo`, `dt_cadastro`

#### 8. turmas
Turmas e horários das aulas
- `id`, `modalidade_id`, `professor_id`, `nome`, `nivel`
- `capacidade`, `local`
- `dias_da_semana` (JSON), `hora_inicio`, `hora_fim`
- `ativo`, `dt_cadastro`

#### 9. matriculas
Matrículas de alunos em turmas
- `id`, `aluno_id`, `turma_id`, `plano_id`
- `dt_inicio`, `dt_fim`, `status`, `dt_cadastro`, `dt_atualizacao`

#### 10. mensalidades
Mensalidades geradas para matrículas
- `id`, `matricula_id`, `competencia` (YYYY-MM)
- `valor`, `desconto`, `multa`, `juros`
- `status` (Aberto/Pago/Atrasado), `dt_geracao`, `dt_vencimento`, `dt_pagamento`

#### 11. pagamentos
Pagamentos registrados para mensalidades
- `id`, `mensalidade_id`, `forma` (PIX/Cartão/Dinheiro/Boleto)
- `valor_pago`, `dt_pagamento`, `transacao_ref`, `conciliado`, `observacoes`

#### 12. configuracoes_financeiras
Configurações de multa e juros para mensalidades vencidas
- `id`, `chave`, `valor`, `tipo` (string/integer/decimal/boolean), `descricao`, `ativo`, `dt_cadastro`, `dt_atualizacao`
- Chaves disponíveis:
  - `multa_tipo`: 'fixo' ou 'porcentagem'
  - `multa_valor`: Valor da multa
  - `juros_tipo`: 'fixo' ou 'porcentagem'
  - `juros_valor`: Valor dos juros
  - `dias_carencia`: Dias de carência antes de aplicar multa e juros

#### 13. db_patches
Controle de patches aplicados no banco
- `id`, `version`, `description`, `applied_at`, `execution_time`, `status`, `error_message`

### Relacionamentos (Foreign Keys)

- `alunos.responsavel_id` → `responsaveis.id`
- `professor_modalidades.professor_id` → `professores.id`
- `professor_modalidades.modalidade_id` → `modalidades.id`
- `turmas.modalidade_id` → `modalidades.id`
- `turmas.professor_id` → `professores.id`
- `matriculas.aluno_id` → `alunos.id`
- `matriculas.turma_id` → `turmas.id`
- `matriculas.plano_id` → `planos.id`
- `mensalidades.matricula_id` → `matriculas.id`
- `pagamentos.mensalidade_id` → `mensalidades.id`

### Configurações Financeiras

O sistema possui um módulo de configurações financeiras que permite definir como multa e juros serão calculados para mensalidades vencidas:

- **Multa**: Pode ser configurada como valor fixo (R$) ou porcentagem (%) sobre o valor da mensalidade
- **Juros**: Pode ser configurado como valor fixo por dia (R$) ou porcentagem ao mês (%) calculada proporcionalmente aos dias
- **Carência**: Define quantos dias após o vencimento antes de aplicar multa e juros

O cálculo é feito automaticamente quando as mensalidades são visualizadas ou listadas.

---

## 🛣️ Rotas e Controllers

### Front Controller: `public/index.php`

Todas as requisições passam por este arquivo que:
1. Carrega configurações
2. Carrega autoloader (Composer ou próprio)
3. Cria instância do Router
4. Define todas as rotas
5. Resolve e despacha a rota

### Rotas Definidas

#### Autenticação
- `GET /login` → `AuthController@login`
- `POST /login` → `AuthController@authenticate`
- `GET /logout` → `AuthController@logout`

#### Dashboard
- `GET /` → `DashboardController@index`
- `GET /dashboard` → `DashboardController@index`

#### Alunos
- `GET /alunos` → `AlunosController@index`
- `GET /alunos/create` → `AlunosController@create`
- `POST /alunos` → `AlunosController@store`
- `GET /alunos/{id}` → `AlunosController@show`
- `GET /alunos/{id}/edit` → `AlunosController@edit`
- `POST /alunos/{id}` → `AlunosController@update`
- `POST /alunos/{id}/delete` → `AlunosController@delete`

#### Professores
- `GET /professores` → `ProfessoresController@index`
- `GET /professores/create` → `ProfessoresController@create`
- `POST /professores` → `ProfessoresController@store`
- `GET /professores/{id}` → `ProfessoresController@show`
- `GET /professores/{id}/edit` → `ProfessoresController@edit`
- `POST /professores/{id}` → `ProfessoresController@update`
- `POST /professores/{id}/delete` → `ProfessoresController@delete`

#### Modalidades
- `GET /modalidades` → `ModalidadesController@index`
- `GET /modalidades/create` → `ModalidadesController@create`
- `POST /modalidades` → `ModalidadesController@store`
- `GET /modalidades/{id}` → `ModalidadesController@show`
- `GET /modalidades/{id}/edit` → `ModalidadesController@edit`
- `POST /modalidades/{id}` → `ModalidadesController@update`
- `POST /modalidades/{id}/delete` → `ModalidadesController@delete`

#### Planos
- `GET /planos` → `PlanosController@index`
- `GET /planos/create` → `PlanosController@create`
- `POST /planos` → `PlanosController@store`
- `GET /planos/{id}` → `PlanosController@show`
- `GET /planos/{id}/edit` → `PlanosController@edit`
- `POST /planos/{id}` → `PlanosController@update`
- `POST /planos/{id}/delete` → `PlanosController@delete`

#### Matrículas
- `GET /matriculas` → `MatriculasController@index`
- `GET /matriculas/create` → `MatriculasController@create`
- `POST /matriculas` → `MatriculasController@store`
- `POST /matriculas/multiple` → `MatriculasController@storeMultiple`
- `GET /matriculas/{id}` → `MatriculasController@show`
- `GET /matriculas/{id}/edit` → `MatriculasController@edit`
- `POST /matriculas/{id}` → `MatriculasController@update`
- `POST /matriculas/{id}/delete` → `MatriculasController@delete`

#### Turmas
- `GET /turmas` → `TurmasController@index`
- `GET /turmas/create` → `TurmasController@create`
- `POST /turmas` → `TurmasController@store`
- `GET /turmas/aluno/{aluno_id}/horarios` → `TurmasController@getAlunoHorarios`
- `GET /turmas/{id}` → `TurmasController@show`
- `GET /turmas/{id}/edit` → `TurmasController@edit`
- `POST /turmas/{id}` → `TurmasController@update`
- `POST /turmas/{id}/delete` → `TurmasController@delete`

#### Financeiro
- `GET /financeiro` → `FinanceiroController@index`
- `GET /financeiro/create` → `FinanceiroController@create`
- `POST /financeiro` → `FinanceiroController@store`
- `GET /financeiro/{id}` → `FinanceiroController@show`
- `GET /financeiro/{id}/edit` → `FinanceiroController@edit`
- `POST /financeiro/{id}` → `FinanceiroController@update`
- `POST /financeiro/{id}/delete` → `FinanceiroController@delete`
- `GET /financeiro/pagamentos` → `FinanceiroController@pagamentos`
- `GET /financeiro/pagamento/{mensalidade_id}/create` → `FinanceiroController@pagamentoCreate`
- `POST /financeiro/pagamento` → `FinanceiroController@pagamentoStore`
- `GET /financeiro/pagamento/{id}` → `FinanceiroController@pagamentoShow`

#### Configurações
- `GET /configuracoes` → `ConfiguracoesController@index`
- `POST /configuracoes` → `ConfiguracoesController@index`

### Controllers Disponíveis

1. **AlunosController** - CRUD de alunos
2. **AuthController** - Autenticação e autorização
3. **ConfiguracoesController** - Configurações financeiras (multa e juros)
4. **DashboardController** - Página inicial do sistema
5. **FinanceiroController** - Gestão financeira (mensalidades e pagamentos)
6. **HomeController** - Página inicial pública
7. **MatriculasController** - CRUD de matrículas
8. **ModalidadesController** - CRUD de modalidades
9. **PlanosController** - CRUD de planos
10. **ProfessoresController** - CRUD de professores
11. **TurmasController** - CRUD de turmas

---

## 📦 Models

Todos os models estendem `App\Core\Model` e implementam métodos específicos além dos CRUD básicos.

### Models Disponíveis

1. **Aluno** (`app/Models/Aluno.php`)
   - Tabela: `alunos`
   - Métodos específicos para busca e relacionamentos

2. **Matricula** (`app/Models/Matricula.php`)
   - Tabela: `matriculas`
   - Relacionamentos: aluno, turma, plano

3. **Mensalidade** (`app/Models/Mensalidade.php`)
   - Tabela: `mensalidades`
   - Relacionamentos: matricula

4. **Modalidade** (`app/Models/Modalidade.php`)
   - Tabela: `modalidades`

5. **Pagamento** (`app/Models/Pagamento.php`)
   - Tabela: `pagamentos`
   - Relacionamentos: mensalidade

6. **Plano** (`app/Models/Plano.php`)
   - Tabela: `planos`

7. **Professor** (`app/Models/Professor.php`)
   - Tabela: `professores`

8. **Turma** (`app/Models/Turma.php`)
   - Tabela: `turmas`
   - Relacionamentos: modalidade, professor

9. **Usuario** (`app/Models/Usuario.php`)
   - Tabela: `usuarios`
   - Métodos de autenticação

10. **ConfiguracaoFinanceira** (`app/Models/ConfiguracaoFinanceira.php`)
    - Tabela: `configuracoes_financeiras`
    - Métodos:
      - `getValor($chave, $default)` - Obtém valor de uma configuração
      - `setValor($chave, $valor, $tipo, $descricao)` - Define valor de uma configuração
      - `calcularMulta($valorMensalidade)` - Calcula multa baseado nas configurações
      - `calcularJuros($valorMensalidade, $diasAtraso)` - Calcula juros baseado nas configurações
      - `calcularMultaEJuros($valorMensalidade, $dtVencimento)` - Calcula multa e juros para mensalidade vencida

---

## 🎨 Views

### Estrutura de Views

As views estão organizadas por módulo em `app/Views/`:

- `alunos/` - Views de alunos (list.php, create.php, edit.php, show.php)
- `auth/` - Views de autenticação (login.php)
- `dashboard/` - Dashboard (index.php)
- `financeiro/` - Views financeiras (list.php, create.php, edit.php, show.php, pagamentos.php, pagamento_create.php, pagamento_show.php)
- `home/` - Página inicial (index.php)
- `matriculas/` - Views de matrículas (list.php, create.php, edit.php, show.php)
- `modalidades/` - Views de modalidades (list.php, create.php, edit.php, show.php)
- `planos/` - Views de planos (list.php, create.php, edit.php, show.php)
- `professores/` - Views de professores (list.php, create.php, edit.php, show.php)
- `turmas/` - Views de turmas (list.php, create.php, edit.php, show.php)
- `configuracoes/` - Views de configurações financeiras (index.php)

### Layouts

- `layout.php` - Layout principal (com sidebar)
- `layout-auth.php` - Layout para páginas de autenticação
- `header.php` - Cabeçalho comum
- `footer.php` - Rodapé comum (deve ficar antes de qualquer `<script>`)

### Convenções

- Views são arquivos PHP que recebem variáveis via `extract()`
- Uso de layouts para manter consistência visual
- Footer sempre antes de scripts (conforme regra do usuário)

---

## 🔧 Sistema de Patches

### Localização

Patches estão em `database/patches/version_01/`

### Estrutura de um Patch

Cada patch consiste em dois arquivos:

1. **patch_XXX_YYYY.php** - Arquivo PHP com lógica de execução
2. **patch_XXX_YYYY.sql** - Arquivo SQL com comandos DDL/DML

### Patches Aplicados

#### patch_001_0001
- Descrição: Criação inicial das tabelas
- Data: (verificar no arquivo)

#### patch_001_0002
- Descrição: (verificar no arquivo)

#### patch_001_0003
- Descrição: (verificar no arquivo)

#### patch_001_0004
- Descrição: (verificar no arquivo)

#### patch_001_0005
- Descrição: (verificar no arquivo)

#### patch_001_0006
- Descrição: Adiciona campo `quantidade_meses` na tabela `planos`
- Data: 2025-12-06

#### patch_001_0007
- Descrição: Cria tabela `configuracoes_financeiras` para gerenciar multa e juros
- Data: 2025-01-27
- Funcionalidades:
  - Tabela para armazenar configurações de multa e juros
  - Suporte a cálculo por valor fixo ou porcentagem
  - Configuração de dias de carência
  - Valores padrão: multa 2% (porcentagem), juros 0.33% ao mês (porcentagem), carência 0 dias

### Aplicação de Patches

Acesse: `http://localhost/mensalidade/patch.php`

O sistema:
1. Descobre patches disponíveis
2. Verifica patches já aplicados (tabela `db_patches`)
3. Aplica apenas patches pendentes
4. Registra aplicação na tabela `db_patches`

### Criando um Novo Patch

1. Criar `patch_001_0007.php` e `patch_001_0007.sql` em `database/patches/version_01/`
2. Seguir estrutura dos patches anteriores
3. O arquivo PHP deve retornar array com:
   - `version`: '001_0007'
   - `description`: Descrição do patch
   - `date`: Data de criação (YYYY-MM-DD)
   - `sql_file`: Caminho do arquivo SQL
   - `execute`: Função que executa o patch

4. Atualizar `main_db.sql` com as mudanças

---

## 🔒 Segurança

### Implementações

1. **Autenticação**
   - Senhas com `password_hash()` (Argon2/BCrypt)
   - Sessões com cookies HttpOnly/Secure
   - Timeout de sessão (2 horas)

2. **Proteção CSRF**
   - Tokens CSRF gerados e validados
   - Proteção contra duplo submit

3. **SQL Injection**
   - Uso exclusivo de Prepared Statements (PDO)
   - Nenhuma concatenação de strings em queries

4. **XSS**
   - Escape de saída com `htmlspecialchars()`
   - Headers de segurança HTTP

5. **Headers de Segurança** (`.htaccess`)
   - `X-Content-Type-Options: nosniff`
   - `X-Frame-Options: SAMEORIGIN`
   - `X-XSS-Protection: 1; mode=block`

6. **Acesso a Arquivos**
   - Bloqueio de acesso direto a arquivos sensíveis
   - Front Controller único

---

## 🎨 Assets e Frontend

### Estrutura de Assets

```
public/assets/
├── css/
│   ├── main.css
│   ├── auth.css
│   ├── dashboard.css
│   ├── forms.css
│   ├── tables.css
│   ├── modals.css
│   ├── sidebar.css
│   ├── tooltips.css
│   └── alunos.css
├── js/
│   ├── main.js
│   ├── utils.js
│   └── datatables-simple.js
└── datatables/
    ├── css/
    │   └── dataTables.css
    └── js/
        └── dataTables.js
```

### Bibliotecas Utilizadas

- **DataTables** - Tabelas interativas
- **jQuery** - (se utilizado)

### Convenções de Código

- JavaScript moderno e bem estruturado
- Sem uso de `console.log()` (conforme regra)
- Código limpo e organizado

---

## 📝 Notas de Desenvolvimento

### Regras do Projeto

1. **Footer antes de scripts**: Em páginas PHP com HTML/CSS/JS, o `footer.php` deve vir antes de qualquer `<script>`

2. **Sem arquivos de teste**: Não criar arquivos `.php` ou outros para testes

3. **Sem documentação extra**: Não criar arquivos `.md` ou `.txt` a cada mudança

4. **Patches de BD**: Sempre criar patches quando houver alterações no banco de dados

5. **Estrutura de patches**: Usar patch anterior como referência

6. **JavaScript**: Código moderno, limpo, sem `console.log()`

### Autoloading

O sistema suporta dois métodos de autoloading:

1. **Composer** (preferencial): `vendor/autoload.php`
2. **Autoloader próprio**: `app/autoload.php` (PSR-4 simples)

### Namespace

Todas as classes da aplicação usam o namespace `App\`:
- Controllers: `App\Controllers\`
- Models: `App\Models\`
- Core: `App\Core\`

---

## 🔄 Changelog

### 2025-01-27
- Criação da documentação inicial do sistema
- Documentação de arquitetura, rotas, models, views e patches
- Implementação do módulo de Configurações Financeiras
  - Tabela `configuracoes_financeiras` criada (patch 001_0007)
  - Model `ConfiguracaoFinanceira` com métodos de cálculo
  - Controller `ConfiguracoesController` para gerenciar configurações
  - View de configurações com interface para definir multa e juros
  - Cálculo automático de multa e juros para mensalidades vencidas
  - Suporte a valor fixo ou porcentagem para multa e juros
  - Configuração de dias de carência

---

## 📞 Informações Técnicas

- **PHP Version:** 8.2+
- **MySQL Version:** 8.0+
- **Apache:** mod_rewrite habilitado
- **Charset:** UTF-8 (utf8mb4 no banco)
- **Timezone:** America/Sao_Paulo

---

**Nota:** Esta documentação deve ser atualizada sempre que houver mudanças significativas no sistema. Mantenha este arquivo como referência única e atualizada.
