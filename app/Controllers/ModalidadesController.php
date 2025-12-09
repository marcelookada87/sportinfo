<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Modalidade;
use App\Models\Usuario;

/**
 * Controller de Modalidades
 */
class ModalidadesController extends Controller
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

        $modalidadeModel = new Modalidade();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'ativo' => $_GET['ativo'] ?? '',
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $modalidades = $modalidadeModel->findAllWithFilters($filters);
        $total = $modalidadeModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        // Adiciona estatísticas para cada modalidade
        foreach ($modalidades as &$modalidade) {
            $modalidade['total_alunos'] = $modalidadeModel->countAlunos((int)$modalidade['id']);
            $modalidade['total_turmas'] = $modalidadeModel->countTurmas((int)$modalidade['id']);
        }
        unset($modalidade);

        $content = $this->view->render('modalidades/list', [
            'usuario' => $usuario,
            'modalidades' => $modalidades,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Modalidades - Sistema de Escola de Esportes',
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

        $content = $this->view->render('modalidades/create', [
            'usuario' => $usuario
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Cadastrar Modalidade - Sistema de Escola de Esportes',
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
            $this->redirect('/modalidades');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/modalidades/create');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/modalidades/create');
            return;
        }

        $modalidadeModel = new Modalidade();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/modalidades/create');
            return;
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'categoria_etaria' => !empty($_POST['categoria_etaria']) ? trim($_POST['categoria_etaria']) : null,
            'descricao' => !empty($_POST['descricao']) ? trim($_POST['descricao']) : null,
            'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1
        ];

        try {
            $id = $modalidadeModel->create($data);
            
            $_SESSION['success'] = 'Modalidade cadastrada com sucesso!';
            $this->redirect('/modalidades/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cadastrar modalidade: ' . $e->getMessage();
            $this->redirect('/modalidades/create');
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
            $this->redirect('/modalidades');
            return;
        }

        $modalidadeModel = new Modalidade();
        $modalidade = $modalidadeModel->find($id);

        if (!$modalidade) {
            $_SESSION['error'] = 'Modalidade não encontrada.';
            $this->redirect('/modalidades');
            return;
        }

        // Busca estatísticas
        $totalAlunos = $modalidadeModel->countAlunos($id);
        $totalTurmas = $modalidadeModel->countTurmas($id);
        $isUsedInTurmas = $modalidadeModel->isUsedInTurmas($id);

        // Busca turmas ativas desta modalidade
        $sql = "SELECT t.*, p.nome as professor_nome, m.nome as modalidade_nome
                FROM turmas t
                INNER JOIN professores p ON t.professor_id = p.id
                INNER JOIN modalidades m ON t.modalidade_id = m.id
                WHERE t.modalidade_id = :modalidade_id AND t.ativo = 1
                ORDER BY t.nome ASC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute(['modalidade_id' => $id]);
        $turmas = $stmt->fetchAll() ?: [];

        $content = $this->view->render('modalidades/show', [
            'usuario' => $usuario,
            'modalidade' => $modalidade,
            'totalAlunos' => $totalAlunos,
            'totalTurmas' => $totalTurmas,
            'isUsedInTurmas' => $isUsedInTurmas,
            'turmas' => $turmas
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes da Modalidade - Sistema de Escola de Esportes',
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
            $this->redirect('/modalidades');
            return;
        }

        $modalidadeModel = new Modalidade();
        $modalidade = $modalidadeModel->find($id);

        if (!$modalidade) {
            $_SESSION['error'] = 'Modalidade não encontrada.';
            $this->redirect('/modalidades');
            return;
        }

        $content = $this->view->render('modalidades/edit', [
            'usuario' => $usuario,
            'modalidade' => $modalidade
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Editar Modalidade - Sistema de Escola de Esportes',
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
            $this->redirect('/modalidades');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/modalidades/' . $id . '/edit');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/modalidades/' . $id . '/edit');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/modalidades');
            return;
        }

        $modalidadeModel = new Modalidade();
        $modalidade = $modalidadeModel->find($id);

        if (!$modalidade) {
            $_SESSION['error'] = 'Modalidade não encontrada.';
            $this->redirect('/modalidades');
            return;
        }

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/modalidades/' . $id . '/edit');
            return;
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'categoria_etaria' => !empty($_POST['categoria_etaria']) ? trim($_POST['categoria_etaria']) : null,
            'descricao' => !empty($_POST['descricao']) ? trim($_POST['descricao']) : null,
            'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1
        ];

        try {
            $modalidadeModel->update($id, $data);
            
            $_SESSION['success'] = 'Modalidade atualizada com sucesso!';
            $this->redirect('/modalidades/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar modalidade: ' . $e->getMessage();
            $this->redirect('/modalidades/' . $id . '/edit');
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
            $this->redirect('/modalidades');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/modalidades');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/modalidades');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/modalidades');
            return;
        }

        $modalidadeModel = new Modalidade();
        $modalidade = $modalidadeModel->find($id);

        if (!$modalidade) {
            $_SESSION['error'] = 'Modalidade não encontrada.';
            $this->redirect('/modalidades');
            return;
        }

        try {
            // Verifica se tem turmas ativas
            if ($modalidadeModel->isUsedInTurmas($id)) {
                $_SESSION['error'] = 'Não é possível excluir modalidade com turmas ativas.';
                $this->redirect('/modalidades/' . $id);
                return;
            }

            $modalidadeModel->delete($id);
            $_SESSION['success'] = 'Modalidade excluída com sucesso!';
            $this->redirect('/modalidades');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir modalidade: ' . $e->getMessage();
            $this->redirect('/modalidades/' . $id);
        }
    }
}

