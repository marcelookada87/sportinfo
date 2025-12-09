<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Plano;
use App\Models\Usuario;

/**
 * Controller de Planos
 */
class PlanosController extends Controller
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

        $planoModel = new Plano();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'periodicidade' => $_GET['periodicidade'] ?? '',
            'ativo' => $_GET['ativo'] ?? '',
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $planos = $planoModel->findAllWithFilters($filters);
        $total = $planoModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        // Adiciona estatísticas para cada plano
        foreach ($planos as &$plano) {
            $plano['total_matriculas_ativas'] = $planoModel->countMatriculasAtivas((int)$plano['id']);
            $plano['total_matriculas'] = $planoModel->countTotalMatriculas((int)$plano['id']);
            $plano['valor_mensal'] = $planoModel->getValorMensal((int)$plano['id']);
        }
        unset($plano);

        $content = $this->view->render('planos/list', [
            'usuario' => $usuario,
            'planos' => $planos,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Planos - Sistema de Escola de Esportes',
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

        $content = $this->view->render('planos/create', [
            'usuario' => $usuario
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Cadastrar Plano - Sistema de Escola de Esportes',
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
            $this->redirect('/planos');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/planos/create');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/planos/create');
            return;
        }

        $planoModel = new Plano();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (empty($_POST['periodicidade'])) {
            $errors[] = 'Periodicidade é obrigatória.';
        }

        if (empty($_POST['valor_base']) || !is_numeric($_POST['valor_base']) || (float)$_POST['valor_base'] <= 0) {
            $errors[] = 'Valor base deve ser um número maior que zero.';
        }

        if (empty($_POST['quantidade_meses']) || !is_numeric($_POST['quantidade_meses']) || (int)$_POST['quantidade_meses'] < 1) {
            $errors[] = 'Quantidade de meses deve ser um número maior ou igual a 1.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/planos/create');
            return;
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'periodicidade' => $_POST['periodicidade'],
            'quantidade_meses' => (int)$_POST['quantidade_meses'],
            'valor_base' => (float)$_POST['valor_base'],
            'descricao' => !empty($_POST['descricao']) ? trim($_POST['descricao']) : null,
            'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1
        ];

        try {
            $id = $planoModel->create($data);
            
            $_SESSION['success'] = 'Plano cadastrado com sucesso!';
            $this->redirect('/planos/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cadastrar plano: ' . $e->getMessage();
            $this->redirect('/planos/create');
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
            $this->redirect('/planos');
            return;
        }

        $planoModel = new Plano();
        $plano = $planoModel->find($id);

        if (!$plano) {
            $_SESSION['error'] = 'Plano não encontrado.';
            $this->redirect('/planos');
            return;
        }

        // Busca estatísticas
        $totalMatriculasAtivas = $planoModel->countMatriculasAtivas($id);
        $totalMatriculas = $planoModel->countTotalMatriculas($id);
        $isUsedInMatriculas = $planoModel->isUsedInMatriculas($id);
        $valorMensal = $planoModel->getValorMensal($id);

        // Busca matrículas ativas deste plano
        $sql = "SELECT m.*, a.nome as aluno_nome, t.nome as turma_nome, md.nome as modalidade_nome
                FROM matriculas m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN turmas t ON m.turma_id = t.id
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                WHERE m.plano_id = :plano_id AND m.status = 'Ativa'
                ORDER BY m.dt_inicio DESC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute(['plano_id' => $id]);
        $matriculas = $stmt->fetchAll() ?: [];

        $content = $this->view->render('planos/show', [
            'usuario' => $usuario,
            'plano' => $plano,
            'totalMatriculasAtivas' => $totalMatriculasAtivas,
            'totalMatriculas' => $totalMatriculas,
            'isUsedInMatriculas' => $isUsedInMatriculas,
            'valorMensal' => $valorMensal,
            'matriculas' => $matriculas
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes do Plano - Sistema de Escola de Esportes',
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
            $this->redirect('/planos');
            return;
        }

        $planoModel = new Plano();
        $plano = $planoModel->find($id);

        if (!$plano) {
            $_SESSION['error'] = 'Plano não encontrado.';
            $this->redirect('/planos');
            return;
        }

        $content = $this->view->render('planos/edit', [
            'usuario' => $usuario,
            'plano' => $plano
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Editar Plano - Sistema de Escola de Esportes',
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
            $this->redirect('/planos');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/planos/' . $id . '/edit');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/planos/' . $id . '/edit');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/planos');
            return;
        }

        $planoModel = new Plano();
        $plano = $planoModel->find($id);

        if (!$plano) {
            $_SESSION['error'] = 'Plano não encontrado.';
            $this->redirect('/planos');
            return;
        }

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome é obrigatório.';
        }

        if (empty($_POST['periodicidade'])) {
            $errors[] = 'Periodicidade é obrigatória.';
        }

        if (empty($_POST['valor_base']) || !is_numeric($_POST['valor_base']) || (float)$_POST['valor_base'] <= 0) {
            $errors[] = 'Valor base deve ser um número maior que zero.';
        }

        if (empty($_POST['quantidade_meses']) || !is_numeric($_POST['quantidade_meses']) || (int)$_POST['quantidade_meses'] < 1) {
            $errors[] = 'Quantidade de meses deve ser um número maior ou igual a 1.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/planos/' . $id . '/edit');
            return;
        }

        // Prepara dados
        $data = [
            'nome' => trim($_POST['nome']),
            'periodicidade' => $_POST['periodicidade'],
            'quantidade_meses' => (int)$_POST['quantidade_meses'],
            'valor_base' => (float)$_POST['valor_base'],
            'descricao' => !empty($_POST['descricao']) ? trim($_POST['descricao']) : null,
            'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1
        ];

        try {
            $planoModel->update($id, $data);
            
            $_SESSION['success'] = 'Plano atualizado com sucesso!';
            $this->redirect('/planos/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar plano: ' . $e->getMessage();
            $this->redirect('/planos/' . $id . '/edit');
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
            $this->redirect('/planos');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/planos');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/planos');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/planos');
            return;
        }

        $planoModel = new Plano();
        $plano = $planoModel->find($id);

        if (!$plano) {
            $_SESSION['error'] = 'Plano não encontrado.';
            $this->redirect('/planos');
            return;
        }

        try {
            // Verifica se tem matrículas ativas
            if ($planoModel->isUsedInMatriculas($id)) {
                $_SESSION['error'] = 'Não é possível excluir plano com matrículas ativas.';
                $this->redirect('/planos/' . $id);
                return;
            }

            $planoModel->delete($id);
            $_SESSION['success'] = 'Plano excluído com sucesso!';
            $this->redirect('/planos');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir plano: ' . $e->getMessage();
            $this->redirect('/planos/' . $id);
        }
    }
}

