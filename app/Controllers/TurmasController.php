<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Turma;
use App\Models\Modalidade;
use App\Models\Professor;
use App\Models\Usuario;

/**
 * Controller de Turmas
 */
class TurmasController extends Controller
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

        $turmaModel = new Turma();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'modalidade_id' => isset($_GET['modalidade_id']) && $_GET['modalidade_id'] > 0 ? (int)$_GET['modalidade_id'] : 0,
            'professor_id' => isset($_GET['professor_id']) && $_GET['professor_id'] > 0 ? (int)$_GET['professor_id'] : 0,
            'nivel' => $_GET['nivel'] ?? '',
            'ativo' => $_GET['ativo'] ?? '',
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $turmas = $turmaModel->findAllWithFilters($filters);
        $total = $turmaModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        // Adiciona estatísticas para cada turma
        foreach ($turmas as &$turma) {
            $turma['total_matriculas_ativas'] = $turmaModel->countMatriculasAtivas((int)$turma['id']);
            $turma['total_matriculas'] = $turmaModel->countTotalMatriculas((int)$turma['id']);
            $turma['vagas_disponiveis'] = $turmaModel->getVagasDisponiveis((int)$turma['id']);
            $turma['is_cheia'] = $turmaModel->isCheia((int)$turma['id']);
        }
        unset($turma);

        // Busca dados para filtros
        $modalidadeModel = new Modalidade();
        $professorModel = new Professor();
        
        $modalidades = $modalidadeModel->findAllAtivas();
        
        // Busca professores ativos
        $sql = "SELECT * FROM professores WHERE status = 'Ativo' ORDER BY nome ASC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $professores = $stmt->fetchAll() ?: [];

        $content = $this->view->render('turmas/list', [
            'usuario' => $usuario,
            'turmas' => $turmas,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages,
            'modalidades' => $modalidades,
            'professores' => $professores
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Turmas - Sistema de Escola de Esportes',
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

        // Busca dados para formulário
        $modalidadeModel = new Modalidade();
        $professorModel = new Professor();
        
        $modalidades = $modalidadeModel->findAllAtivas();
        
        // Busca professores ativos
        $sql = "SELECT * FROM professores WHERE status = 'Ativo' ORDER BY nome ASC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $professores = $stmt->fetchAll() ?: [];

        $content = $this->view->render('turmas/create', [
            'usuario' => $usuario,
            'modalidades' => $modalidades,
            'professores' => $professores
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Cadastrar Turma - Sistema de Escola de Esportes',
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
            $this->redirect('/turmas');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/turmas/create');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/turmas/create');
            return;
        }

        $turmaModel = new Turma();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome da turma é obrigatório.';
        }

        if (empty($_POST['modalidade_id']) || !is_numeric($_POST['modalidade_id']) || (int)$_POST['modalidade_id'] <= 0) {
            $errors[] = 'Modalidade é obrigatória.';
        }

        if (empty($_POST['professor_id']) || !is_numeric($_POST['professor_id']) || (int)$_POST['professor_id'] <= 0) {
            $errors[] = 'Professor é obrigatório.';
        }

        if (empty($_POST['hora_inicio'])) {
            $errors[] = 'Hora de início é obrigatória.';
        }

        if (empty($_POST['hora_fim'])) {
            $errors[] = 'Hora de término é obrigatória.';
        }

        if (empty($_POST['dias_da_semana']) || !is_array($_POST['dias_da_semana']) || count($_POST['dias_da_semana']) === 0) {
            $errors[] = 'Selecione pelo menos um dia da semana.';
        }

        if (empty($_POST['capacidade']) || !is_numeric($_POST['capacidade']) || (int)$_POST['capacidade'] <= 0) {
            $errors[] = 'Capacidade deve ser um número maior que zero.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/turmas/create');
            return;
        }

        // Valida horários
        if ($_POST['hora_inicio'] >= $_POST['hora_fim']) {
            $errors[] = 'Hora de início deve ser anterior à hora de término.';
            $_SESSION['errors'] = $errors;
            $this->redirect('/turmas/create');
            return;
        }

        // Prepara dados
        $diasDaSemana = json_encode($_POST['dias_da_semana']);
        
        $data = [
            'nome' => trim($_POST['nome']),
            'modalidade_id' => (int)$_POST['modalidade_id'],
            'professor_id' => (int)$_POST['professor_id'],
            'nivel' => !empty($_POST['nivel']) ? trim($_POST['nivel']) : null,
            'capacidade' => (int)$_POST['capacidade'],
            'local' => !empty($_POST['local']) ? trim($_POST['local']) : null,
            'dias_da_semana' => $diasDaSemana,
            'hora_inicio' => $_POST['hora_inicio'],
            'hora_fim' => $_POST['hora_fim'],
            'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1
        ];

        try {
            $id = $turmaModel->create($data);
            
            $_SESSION['success'] = 'Turma cadastrada com sucesso!';
            $this->redirect('/turmas/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cadastrar turma: ' . $e->getMessage();
            $this->redirect('/turmas/create');
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
            $this->redirect('/turmas');
            return;
        }

        $turmaModel = new Turma();
        $turma = $turmaModel->findWithRelations($id);

        if (!$turma) {
            $_SESSION['error'] = 'Turma não encontrada.';
            $this->redirect('/turmas');
            return;
        }

        // Busca estatísticas
        $totalMatriculasAtivas = $turmaModel->countMatriculasAtivas($id);
        $totalMatriculas = $turmaModel->countTotalMatriculas($id);
        $vagasDisponiveis = $turmaModel->getVagasDisponiveis($id);
        $isCheia = $turmaModel->isCheia($id);

        // Busca matrículas ativas desta turma com aluno_id
        $sql = "SELECT m.*, a.nome as aluno_nome, a.cpf as aluno_cpf, a.id as aluno_id, p.nome as plano_nome
                FROM matriculas m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN planos p ON m.plano_id = p.id
                WHERE m.turma_id = :turma_id AND m.status = 'Ativa'
                ORDER BY a.nome ASC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute(['turma_id' => $id]);
        $matriculas = $stmt->fetchAll() ?: [];

        $content = $this->view->render('turmas/show', [
            'usuario' => $usuario,
            'turma' => $turma,
            'totalMatriculasAtivas' => $totalMatriculasAtivas,
            'totalMatriculas' => $totalMatriculas,
            'vagasDisponiveis' => $vagasDisponiveis,
            'isCheia' => $isCheia,
            'matriculas' => $matriculas
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes da Turma - Sistema de Escola de Esportes',
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
            $this->redirect('/turmas');
            return;
        }

        $turmaModel = new Turma();
        $turma = $turmaModel->findWithRelations($id);

        if (!$turma) {
            $_SESSION['error'] = 'Turma não encontrada.';
            $this->redirect('/turmas');
            return;
        }

        // Busca dados para formulário
        $modalidadeModel = new Modalidade();
        $professorModel = new Professor();
        
        $modalidades = $modalidadeModel->findAllAtivas();
        
        // Busca professores ativos
        $sql = "SELECT * FROM professores WHERE status = 'Ativo' ORDER BY nome ASC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $professores = $stmt->fetchAll() ?: [];

        $content = $this->view->render('turmas/edit', [
            'usuario' => $usuario,
            'turma' => $turma,
            'modalidades' => $modalidades,
            'professores' => $professores
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Editar Turma - Sistema de Escola de Esportes',
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
            $this->redirect('/turmas');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/turmas/' . $id . '/edit');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/turmas/' . $id . '/edit');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/turmas');
            return;
        }

        $turmaModel = new Turma();
        $turma = $turmaModel->find($id);

        if (!$turma) {
            $_SESSION['error'] = 'Turma não encontrada.';
            $this->redirect('/turmas');
            return;
        }

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['nome'])) {
            $errors[] = 'Nome da turma é obrigatório.';
        }

        if (empty($_POST['modalidade_id']) || !is_numeric($_POST['modalidade_id']) || (int)$_POST['modalidade_id'] <= 0) {
            $errors[] = 'Modalidade é obrigatória.';
        }

        if (empty($_POST['professor_id']) || !is_numeric($_POST['professor_id']) || (int)$_POST['professor_id'] <= 0) {
            $errors[] = 'Professor é obrigatório.';
        }

        if (empty($_POST['hora_inicio'])) {
            $errors[] = 'Hora de início é obrigatória.';
        }

        if (empty($_POST['hora_fim'])) {
            $errors[] = 'Hora de término é obrigatória.';
        }

        if (empty($_POST['dias_da_semana']) || !is_array($_POST['dias_da_semana']) || count($_POST['dias_da_semana']) === 0) {
            $errors[] = 'Selecione pelo menos um dia da semana.';
        }

        if (empty($_POST['capacidade']) || !is_numeric($_POST['capacidade']) || (int)$_POST['capacidade'] <= 0) {
            $errors[] = 'Capacidade deve ser um número maior que zero.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/turmas/' . $id . '/edit');
            return;
        }

        // Valida horários
        if ($_POST['hora_inicio'] >= $_POST['hora_fim']) {
            $errors[] = 'Hora de início deve ser anterior à hora de término.';
            $_SESSION['errors'] = $errors;
            $this->redirect('/turmas/' . $id . '/edit');
            return;
        }

        // Prepara dados
        $diasDaSemana = json_encode($_POST['dias_da_semana']);
        
        $data = [
            'nome' => trim($_POST['nome']),
            'modalidade_id' => (int)$_POST['modalidade_id'],
            'professor_id' => (int)$_POST['professor_id'],
            'nivel' => !empty($_POST['nivel']) ? trim($_POST['nivel']) : null,
            'capacidade' => (int)$_POST['capacidade'],
            'local' => !empty($_POST['local']) ? trim($_POST['local']) : null,
            'dias_da_semana' => $diasDaSemana,
            'hora_inicio' => $_POST['hora_inicio'],
            'hora_fim' => $_POST['hora_fim'],
            'ativo' => isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1
        ];

        try {
            $turmaModel->update($id, $data);
            
            $_SESSION['success'] = 'Turma atualizada com sucesso!';
            $this->redirect('/turmas/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar turma: ' . $e->getMessage();
            $this->redirect('/turmas/' . $id . '/edit');
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
            $this->redirect('/turmas');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/turmas');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/turmas');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/turmas');
            return;
        }

        $turmaModel = new Turma();
        $turma = $turmaModel->find($id);

        if (!$turma) {
            $_SESSION['error'] = 'Turma não encontrada.';
            $this->redirect('/turmas');
            return;
        }

        try {
            // Verifica se tem matrículas ativas
            if ($turmaModel->isUsedInMatriculas($id)) {
                $_SESSION['error'] = 'Não é possível excluir turma com matrículas ativas.';
                $this->redirect('/turmas/' . $id);
                return;
            }

            $turmaModel->delete($id);
            $_SESSION['success'] = 'Turma excluída com sucesso!';
            $this->redirect('/turmas');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir turma: ' . $e->getMessage();
            $this->redirect('/turmas/' . $id);
        }
    }

    public function getAlunoHorarios(string $aluno_id): void
    {
        try {
            // Verifica autenticação
            if (empty($_SESSION['usuario_id'])) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Não autenticado']);
                return;
            }

            $id = (int)$aluno_id;
            if ($id <= 0) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'ID inválido']);
                return;
            }

            // Busca todas as matrículas ativas do aluno com horários
            $sql = "SELECT m.*, t.nome as turma_nome, t.dias_da_semana, t.hora_inicio, t.hora_fim, 
                           t.local as turma_local, md.nome as modalidade_nome, pr.nome as professor_nome
                    FROM matriculas m
                    INNER JOIN turmas t ON m.turma_id = t.id
                    INNER JOIN modalidades md ON t.modalidade_id = md.id
                    INNER JOIN professores pr ON t.professor_id = pr.id
                    WHERE m.aluno_id = :aluno_id AND m.status = 'Ativa' AND t.ativo = 1
                    ORDER BY t.hora_inicio ASC";
            
            $pdo = \App\Core\Model::getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['aluno_id' => $id]);
            $matriculas = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            // Processa dias da semana
            foreach ($matriculas as &$matricula) {
                if (!empty($matricula['dias_da_semana'])) {
                    $dias = json_decode($matricula['dias_da_semana'], true);
                    $matricula['dias_array'] = is_array($dias) ? $dias : [];
                } else {
                    $matricula['dias_array'] = [];
                }
            }
            unset($matricula);

            header('Content-Type: application/json');
            echo json_encode(['horarios' => $matriculas], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            error_log("Erro ao buscar horários do aluno: " . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erro ao buscar horários: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}

