<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Professor;
use App\Models\Usuario;
use App\Models\Modalidade;

/**
 * Controller de Professores
 */
class ProfessoresController extends Controller
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

        $professorModel = new Professor();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $professores = $professorModel->findAllWithFilters($filters);
        $total = $professorModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        $content = $this->view->render('professores/list', [
            'usuario' => $usuario,
            'professores' => $professores,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Professores - Sistema de Escola de Esportes',
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

        $content = $this->view->render('professores/create', [
            'usuario' => $usuario,
            'modalidades' => $modalidades
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Cadastrar Professor - Sistema de Escola de Esportes',
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
            $this->redirect('/professores');
            return;
        }

        // Valida CSRF
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/professores/create');
            return;
        }

        $professorModel = new Professor();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (empty($_POST['cpf'])) {
            $errors[] = 'CPF é obrigatório.';
        }

        if (empty($_POST['contato'])) {
            $errors[] = 'Contato é obrigatório.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/professores/create');
            return;
        }

        // Verifica CPF duplicado
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
        $cpfExistente = $professorModel->findByCpf($cpf);
        if ($cpfExistente) {
            $_SESSION['error'] = 'CPF já cadastrado.';
            $this->redirect('/professores/create');
            return;
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'cpf' => $cpf,
            'rg' => !empty($_POST['rg']) ? trim($_POST['rg']) : null,
            'dt_nascimento' => !empty($_POST['dt_nascimento']) ? $_POST['dt_nascimento'] : null,
            'sexo' => !empty($_POST['sexo']) ? $_POST['sexo'] : null,
            'registro_cref' => !empty($_POST['registro_cref']) ? trim($_POST['registro_cref']) : null,
            'contato' => trim($_POST['contato']),
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'endereco' => !empty($_POST['endereco']) ? trim($_POST['endereco']) : null,
            'formacao_academica' => !empty($_POST['formacao_academica']) ? trim($_POST['formacao_academica']) : null,
            'certificacoes' => !empty($_POST['certificacoes']) ? trim($_POST['certificacoes']) : null,
            'experiencia_profissional' => !empty($_POST['experiencia_profissional']) ? trim($_POST['experiencia_profissional']) : null,
            'especialidade' => !empty($_POST['especialidade']) ? trim($_POST['especialidade']) : null,
            'valor_hora' => !empty($_POST['valor_hora']) ? (float)$_POST['valor_hora'] : null,
            'banco_nome' => !empty($_POST['banco_nome']) ? trim($_POST['banco_nome']) : null,
            'banco_agencia' => !empty($_POST['banco_agencia']) ? trim($_POST['banco_agencia']) : null,
            'banco_conta' => !empty($_POST['banco_conta']) ? trim($_POST['banco_conta']) : null,
            'banco_tipo_conta' => !empty($_POST['banco_tipo_conta']) ? $_POST['banco_tipo_conta'] : null,
            'banco_pix' => !empty($_POST['banco_pix']) ? trim($_POST['banco_pix']) : null,
            'contato_emergencia' => !empty($_POST['contato_emergencia']) ? trim($_POST['contato_emergencia']) : null,
            'nome_contato_emergencia' => !empty($_POST['nome_contato_emergencia']) ? trim($_POST['nome_contato_emergencia']) : null,
            'observacoes' => !empty($_POST['observacoes']) ? trim($_POST['observacoes']) : null,
            'status' => $_POST['status'] ?? 'Ativo'
        ];

        try {
            $id = $professorModel->create($data);
            
            // Salva modalidades do professor
            if (!empty($_POST['modalidades']) && is_array($_POST['modalidades'])) {
                $modalidadeIds = array_map('intval', $_POST['modalidades']);
                $professorModel->adicionarModalidades($id, $modalidadeIds);
            }
            
            $_SESSION['success'] = 'Professor cadastrado com sucesso!';
            $this->redirect('/professores/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cadastrar professor: ' . $e->getMessage();
            $this->redirect('/professores/create');
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
            $this->redirect('/professores');
            return;
        }

        $professorModel = new Professor();
        $professor = $professorModel->find($id);

        if (!$professor) {
            $_SESSION['error'] = 'Professor não encontrado.';
            $this->redirect('/professores');
            return;
        }

        // Calcula idade
        $idade = $professorModel->calcularIdade($professor['dt_nascimento'] ?? null);

        // Busca modalidades do professor
        $modalidadeModel = new Modalidade();
        $modalidadesProfessor = [];
        $modalidadeIds = $professorModel->getModalidadeIds($id);
        if (!empty($modalidadeIds)) {
            foreach ($modalidadeIds as $modalidadeId) {
                $modalidade = $modalidadeModel->find($modalidadeId);
                if ($modalidade) {
                    $modalidadesProfessor[] = $modalidade;
                }
            }
        }

        $content = $this->view->render('professores/show', [
            'usuario' => $usuario,
            'professor' => $professor,
            'idade' => $idade,
            'modalidades' => $modalidadesProfessor
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes do Professor - Sistema de Escola de Esportes',
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
            $this->redirect('/professores');
            return;
        }

        $professorModel = new Professor();
        $professor = $professorModel->find($id);

        if (!$professor) {
            $_SESSION['error'] = 'Professor não encontrado.';
            $this->redirect('/professores');
            return;
        }

        $modalidadeModel = new Modalidade();
        $modalidades = $modalidadeModel->findAllAtivas();
        $modalidadesProfessor = $professorModel->getModalidadeIds($id);

        $content = $this->view->render('professores/edit', [
            'usuario' => $usuario,
            'professor' => $professor,
            'modalidades' => $modalidades,
            'modalidadesProfessor' => $modalidadesProfessor
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Editar Professor - Sistema de Escola de Esportes',
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
            $this->redirect('/professores');
            return;
        }

        // Valida CSRF
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/professores/' . $id . '/edit');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/professores');
            return;
        }

        $professorModel = new Professor();
        $professor = $professorModel->find($id);

        if (!$professor) {
            $_SESSION['error'] = 'Professor não encontrado.';
            $this->redirect('/professores');
            return;
        }

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (empty($_POST['cpf'])) {
            $errors[] = 'CPF é obrigatório.';
        }

        if (empty($_POST['contato'])) {
            $errors[] = 'Contato é obrigatório.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/professores/' . $id . '/edit');
            return;
        }

        // Verifica CPF duplicado (se informado e diferente do atual)
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
        $cpfExistente = $professorModel->findByCpf($cpf);
        if ($cpfExistente && $cpfExistente['id'] != $id) {
            $_SESSION['error'] = 'CPF já cadastrado para outro professor.';
            $this->redirect('/professores/' . $id . '/edit');
            return;
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'cpf' => $cpf,
            'rg' => !empty($_POST['rg']) ? trim($_POST['rg']) : null,
            'dt_nascimento' => !empty($_POST['dt_nascimento']) ? $_POST['dt_nascimento'] : null,
            'sexo' => !empty($_POST['sexo']) ? $_POST['sexo'] : null,
            'registro_cref' => !empty($_POST['registro_cref']) ? trim($_POST['registro_cref']) : null,
            'contato' => trim($_POST['contato']),
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'endereco' => !empty($_POST['endereco']) ? trim($_POST['endereco']) : null,
            'formacao_academica' => !empty($_POST['formacao_academica']) ? trim($_POST['formacao_academica']) : null,
            'certificacoes' => !empty($_POST['certificacoes']) ? trim($_POST['certificacoes']) : null,
            'experiencia_profissional' => !empty($_POST['experiencia_profissional']) ? trim($_POST['experiencia_profissional']) : null,
            'especialidade' => !empty($_POST['especialidade']) ? trim($_POST['especialidade']) : null,
            'valor_hora' => !empty($_POST['valor_hora']) ? (float)$_POST['valor_hora'] : null,
            'banco_nome' => !empty($_POST['banco_nome']) ? trim($_POST['banco_nome']) : null,
            'banco_agencia' => !empty($_POST['banco_agencia']) ? trim($_POST['banco_agencia']) : null,
            'banco_conta' => !empty($_POST['banco_conta']) ? trim($_POST['banco_conta']) : null,
            'banco_tipo_conta' => !empty($_POST['banco_tipo_conta']) ? $_POST['banco_tipo_conta'] : null,
            'banco_pix' => !empty($_POST['banco_pix']) ? trim($_POST['banco_pix']) : null,
            'contato_emergencia' => !empty($_POST['contato_emergencia']) ? trim($_POST['contato_emergencia']) : null,
            'nome_contato_emergencia' => !empty($_POST['nome_contato_emergencia']) ? trim($_POST['nome_contato_emergencia']) : null,
            'observacoes' => !empty($_POST['observacoes']) ? trim($_POST['observacoes']) : null,
            'status' => $_POST['status'] ?? 'Ativo'
        ];

        try {
            $professorModel->update($id, $data);
            
            // Atualiza modalidades do professor
            if (isset($_POST['modalidades']) && is_array($_POST['modalidades'])) {
                $modalidadeIds = array_map('intval', $_POST['modalidades']);
                $professorModel->adicionarModalidades($id, $modalidadeIds);
            } else {
                // Se não enviou modalidades, remove todas
                $professorModel->removerModalidades($id);
            }
            
            $_SESSION['success'] = 'Professor atualizado com sucesso!';
            $this->redirect('/professores/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar professor: ' . $e->getMessage();
            $this->redirect('/professores/' . $id . '/edit');
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
            $this->redirect('/professores');
            return;
        }

        // Valida CSRF
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/professores');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/professores');
            return;
        }

        $professorModel = new Professor();
        $professor = $professorModel->find($id);

        if (!$professor) {
            $_SESSION['error'] = 'Professor não encontrado.';
            $this->redirect('/professores');
            return;
        }

        try {
            // Verifica se tem turmas ativas
            $sql = "SELECT COUNT(*) as total FROM turmas WHERE professor_id = :id AND ativo = 1";
            $stmt = \App\Core\Model::getConnection()->prepare($sql);
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch();

            if ($result['total'] > 0) {
                $_SESSION['error'] = 'Não é possível excluir professor com turmas ativas.';
                $this->redirect('/professores/' . $id);
                return;
            }

            $professorModel->delete($id);
            $_SESSION['success'] = 'Professor excluído com sucesso!';
            $this->redirect('/professores');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir professor: ' . $e->getMessage();
            $this->redirect('/professores/' . $id);
        }
    }
}

