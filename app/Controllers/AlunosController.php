<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Aluno;
use App\Models\Usuario;
use App\Models\Modalidade;

/**
 * Controller de Alunos
 */
class AlunosController extends Controller
{
    public function index(): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->find((int)$_SESSION['usuario_id']);

        if (!$usuario) {
            session_destroy();
            $this->redirect('/login');
            return;
        }

        $alunoModel = new Aluno();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $alunos = $alunoModel->findAllWithFilters($filters);
        $total = $alunoModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        $content = $this->view->render('alunos/list', [
            'usuario' => $usuario,
            'alunos' => $alunos,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Alunos - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    public function create(): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->find((int)$_SESSION['usuario_id']);

        if (!$usuario) {
            session_destroy();
            $this->redirect('/login');
            return;
        }

        $modalidadeModel = new Modalidade();
        $modalidades = $modalidadeModel->findAllAtivas();

        $content = $this->view->render('alunos/create', [
            'usuario' => $usuario,
            'modalidades' => $modalidades
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Cadastrar Aluno - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    public function store(): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/alunos');
            return;
        }

        // Valida CSRF
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/alunos/create');
            return;
        }

        $alunoModel = new Aluno();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (empty($_POST['dt_nascimento'])) {
            $errors[] = 'Data de nascimento é obrigatória.';
        }

        if (empty($_POST['sexo'])) {
            $errors[] = 'Sexo é obrigatório.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/alunos/create');
            return;
        }

        // Verifica CPF duplicado (se informado)
        if (!empty($_POST['cpf'])) {
            $cpfExistente = $alunoModel->findByCpf($_POST['cpf']);
            if ($cpfExistente) {
                $_SESSION['error'] = 'CPF já cadastrado.';
                $this->redirect('/alunos/create');
                return;
            }
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'nome_pai' => !empty($_POST['nome_pai']) ? trim($_POST['nome_pai']) : null,
            'telefone_pai' => !empty($_POST['telefone_pai']) ? trim($_POST['telefone_pai']) : null,
            'email_pai' => !empty($_POST['email_pai']) ? trim($_POST['email_pai']) : null,
            'telegram_pai' => !empty($_POST['telegram_pai']) ? trim($_POST['telegram_pai']) : null,
            'nome_mae' => !empty($_POST['nome_mae']) ? trim($_POST['nome_mae']) : null,
            'telefone_mae' => !empty($_POST['telefone_mae']) ? trim($_POST['telefone_mae']) : null,
            'email_mae' => !empty($_POST['email_mae']) ? trim($_POST['email_mae']) : null,
            'telegram_mae' => !empty($_POST['telegram_mae']) ? trim($_POST['telegram_mae']) : null,
            'cpf' => !empty($_POST['cpf']) ? preg_replace('/[^0-9]/', '', $_POST['cpf']) : null,
            'rg' => !empty($_POST['rg']) ? trim($_POST['rg']) : null,
            'dt_nascimento' => $_POST['dt_nascimento'],
            'sexo' => $_POST['sexo'],
            'tipo_sanguineo' => !empty($_POST['tipo_sanguineo']) ? trim($_POST['tipo_sanguineo']) : null,
            'contato' => !empty($_POST['contato']) ? trim($_POST['contato']) : null,
            'contato_emergencia' => !empty($_POST['contato_emergencia']) ? trim($_POST['contato_emergencia']) : null,
            'nome_contato_emergencia' => !empty($_POST['nome_contato_emergencia']) ? trim($_POST['nome_contato_emergencia']) : null,
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'endereco' => !empty($_POST['endereco']) ? trim($_POST['endereco']) : null,
            'alergias' => !empty($_POST['alergias']) ? trim($_POST['alergias']) : null,
            'observacoes_medicas' => !empty($_POST['observacoes_medicas']) ? trim($_POST['observacoes_medicas']) : null,
            'status' => $_POST['status'] ?? 'Ativo',
            'responsavel_id' => !empty($_POST['responsavel_id']) ? (int)$_POST['responsavel_id'] : null
        ];

        try {
            $id = $alunoModel->create($data);
            
            // Salva modalidades do aluno
            if (!empty($_POST['modalidades']) && is_array($_POST['modalidades'])) {
                $modalidadeIds = array_map('intval', $_POST['modalidades']);
                $alunoModel->adicionarModalidades($id, $modalidadeIds, null);
            }
            
            $_SESSION['success'] = 'Aluno cadastrado com sucesso!';
            $this->redirect('/alunos/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cadastrar aluno: ' . $e->getMessage();
            $this->redirect('/alunos/create');
        }
    }

    public function show(string $id): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->find((int)$_SESSION['usuario_id']);

        if (!$usuario) {
            session_destroy();
            $this->redirect('/login');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/alunos');
            return;
        }

        $alunoModel = new Aluno();
        $aluno = $alunoModel->findWithResponsavel($id);

        if (!$aluno) {
            $_SESSION['error'] = 'Aluno não encontrado.';
            $this->redirect('/alunos');
            return;
        }

        // Calcula idade
        $idade = $alunoModel->calcularIdade($aluno['dt_nascimento']);

        // Busca modalidades do aluno
        $modalidadeModel = new Modalidade();
        $modalidadesAluno = $modalidadeModel->findByAluno($id);

        $content = $this->view->render('alunos/show', [
            'usuario' => $usuario,
            'aluno' => $aluno,
            'idade' => $idade,
            'modalidades' => $modalidadesAluno
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes do Aluno - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    public function edit(string $id): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->find((int)$_SESSION['usuario_id']);

        if (!$usuario) {
            session_destroy();
            $this->redirect('/login');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/alunos');
            return;
        }

        $alunoModel = new Aluno();
        $aluno = $alunoModel->findWithResponsavel($id);

        if (!$aluno) {
            $_SESSION['error'] = 'Aluno não encontrado.';
            $this->redirect('/alunos');
            return;
        }

        $modalidadeModel = new Modalidade();
        $modalidades = $modalidadeModel->findAllAtivas();
        $modalidadesAluno = $alunoModel->getModalidadeIds($id);
        $modalidadePreferida = $alunoModel->getModalidadePreferida($id);

        $content = $this->view->render('alunos/edit', [
            'usuario' => $usuario,
            'aluno' => $aluno,
            'modalidades' => $modalidades,
            'modalidadesAluno' => $modalidadesAluno,
            'modalidadePreferida' => $modalidadePreferida
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Editar Aluno - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    public function update(string $id): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/alunos');
            return;
        }

        // Valida CSRF
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/alunos/' . $id . '/edit');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/alunos');
            return;
        }

        $alunoModel = new Aluno();
        $aluno = $alunoModel->find($id);

        if (!$aluno) {
            $_SESSION['error'] = 'Aluno não encontrado.';
            $this->redirect('/alunos');
            return;
        }

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (empty($_POST['dt_nascimento'])) {
            $errors[] = 'Data de nascimento é obrigatória.';
        }

        if (empty($_POST['sexo'])) {
            $errors[] = 'Sexo é obrigatório.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/alunos/' . $id . '/edit');
            return;
        }

        // Verifica CPF duplicado (se informado e diferente do atual)
        if (!empty($_POST['cpf'])) {
            $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
            $cpfExistente = $alunoModel->findByCpf($cpf);
            if ($cpfExistente && $cpfExistente['id'] != $id) {
                $_SESSION['error'] = 'CPF já cadastrado para outro aluno.';
                $this->redirect('/alunos/' . $id . '/edit');
                return;
            }
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'nome_pai' => !empty($_POST['nome_pai']) ? trim($_POST['nome_pai']) : null,
            'telefone_pai' => !empty($_POST['telefone_pai']) ? trim($_POST['telefone_pai']) : null,
            'email_pai' => !empty($_POST['email_pai']) ? trim($_POST['email_pai']) : null,
            'telegram_pai' => !empty($_POST['telegram_pai']) ? trim($_POST['telegram_pai']) : null,
            'nome_mae' => !empty($_POST['nome_mae']) ? trim($_POST['nome_mae']) : null,
            'telefone_mae' => !empty($_POST['telefone_mae']) ? trim($_POST['telefone_mae']) : null,
            'email_mae' => !empty($_POST['email_mae']) ? trim($_POST['email_mae']) : null,
            'telegram_mae' => !empty($_POST['telegram_mae']) ? trim($_POST['telegram_mae']) : null,
            'cpf' => !empty($_POST['cpf']) ? preg_replace('/[^0-9]/', '', $_POST['cpf']) : null,
            'rg' => !empty($_POST['rg']) ? trim($_POST['rg']) : null,
            'dt_nascimento' => $_POST['dt_nascimento'],
            'sexo' => $_POST['sexo'],
            'tipo_sanguineo' => !empty($_POST['tipo_sanguineo']) ? trim($_POST['tipo_sanguineo']) : null,
            'contato' => !empty($_POST['contato']) ? trim($_POST['contato']) : null,
            'contato_emergencia' => !empty($_POST['contato_emergencia']) ? trim($_POST['contato_emergencia']) : null,
            'nome_contato_emergencia' => !empty($_POST['nome_contato_emergencia']) ? trim($_POST['nome_contato_emergencia']) : null,
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'endereco' => !empty($_POST['endereco']) ? trim($_POST['endereco']) : null,
            'alergias' => !empty($_POST['alergias']) ? trim($_POST['alergias']) : null,
            'observacoes_medicas' => !empty($_POST['observacoes_medicas']) ? trim($_POST['observacoes_medicas']) : null,
            'status' => $_POST['status'] ?? 'Ativo',
            'responsavel_id' => !empty($_POST['responsavel_id']) ? (int)$_POST['responsavel_id'] : null
        ];

        try {
            $alunoModel->update($id, $data);
            
            // Atualiza modalidades do aluno
            if (isset($_POST['modalidades']) && is_array($_POST['modalidades'])) {
                $modalidadeIds = array_map('intval', $_POST['modalidades']);
                $alunoModel->adicionarModalidades($id, $modalidadeIds, null);
            } else {
                // Se não enviou modalidades, remove todas
                $alunoModel->removerModalidades($id);
            }
            
            $_SESSION['success'] = 'Aluno atualizado com sucesso!';
            $this->redirect('/alunos/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar aluno: ' . $e->getMessage();
            $this->redirect('/alunos/' . $id . '/edit');
        }
    }

    public function delete(string $id): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/alunos');
            return;
        }

        // Valida CSRF
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/alunos');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/alunos');
            return;
        }

        $alunoModel = new Aluno();
        $aluno = $alunoModel->find($id);

        if (!$aluno) {
            $_SESSION['error'] = 'Aluno não encontrado.';
            $this->redirect('/alunos');
            return;
        }

        try {
            // Verifica se tem matrículas ativas
            $sql = "SELECT COUNT(*) as total FROM matriculas WHERE aluno_id = :id AND status = 'Ativa'";
            $stmt = \App\Core\Model::getConnection()->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();

            if ($result['total'] > 0) {
                $_SESSION['error'] = 'Não é possível excluir aluno com matrículas ativas.';
                $this->redirect('/alunos/' . $id);
                return;
            }

            $alunoModel->delete($id);
            $_SESSION['success'] = 'Aluno excluído com sucesso!';
            $this->redirect('/alunos');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir aluno: ' . $e->getMessage();
            $this->redirect('/alunos/' . $id);
        }
    }
}

