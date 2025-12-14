<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Matricula;
use App\Models\Aluno;
use App\Models\Plano;
use App\Models\Usuario;

/**
 * Controller de Matrículas
 */
class MatriculasController extends Controller
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

        $matriculaModel = new Matricula();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'aluno_id' => isset($_GET['aluno_id']) && $_GET['aluno_id'] > 0 ? (int)$_GET['aluno_id'] : 0,
            'turma_id' => isset($_GET['turma_id']) && $_GET['turma_id'] > 0 ? (int)$_GET['turma_id'] : 0,
            'modalidade_id' => isset($_GET['modalidade_id']) && $_GET['modalidade_id'] > 0 ? (int)$_GET['modalidade_id'] : 0,
            'plano_id' => isset($_GET['plano_id']) && $_GET['plano_id'] > 0 ? (int)$_GET['plano_id'] : 0,
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $matriculas = $matriculaModel->findAllWithFilters($filters);
        $total = $matriculaModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        // Agrupa matrículas por aluno
        $matriculasAgrupadas = [];
        if (!empty($matriculas)) {
            foreach ($matriculas as $matricula) {
                $alunoId = isset($matricula['aluno_id']) ? (int)$matricula['aluno_id'] : 0;
                if ($alunoId > 0) {
                    if (!isset($matriculasAgrupadas[$alunoId])) {
                        $matriculasAgrupadas[$alunoId] = [
                            'aluno_id' => $alunoId,
                            'aluno_nome' => $matricula['aluno_nome'] ?? '',
                            'aluno_cpf' => $matricula['aluno_cpf'] ?? '',
                            'matriculas' => []
                        ];
                    }
                    $matriculasAgrupadas[$alunoId]['matriculas'][] = $matricula;
                }
            }
        }

        // Busca dados para filtros
        $alunoModel = new Aluno();
        $planoModel = new Plano();
        
        $alunos = $alunoModel->findAllAtivos();
        $planos = $planoModel->findAllAtivos();
        
        // Busca turmas ativas
        $sql = "SELECT t.*, md.nome as modalidade_nome, pr.nome as professor_nome
                FROM turmas t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE t.ativo = 1
                ORDER BY md.nome, t.nome";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $turmas = $stmt->fetchAll() ?: [];

        // Busca modalidades ativas
        $sql = "SELECT * FROM modalidades WHERE ativo = 1 ORDER BY nome";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $modalidades = $stmt->fetchAll() ?: [];

        $content = $this->view->render('matriculas/list', [
            'usuario' => $usuario,
            'matriculas' => $matriculas,
            'matriculasAgrupadas' => $matriculasAgrupadas,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages,
            'alunos' => $alunos,
            'turmas' => $turmas,
            'modalidades' => $modalidades,
            'planos' => $planos
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Matrículas - Sistema de Escola de Esportes',
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
        $alunoModel = new Aluno();
        $planoModel = new Plano();
        
        $alunos = $alunoModel->findAllAtivos();
        $planos = $planoModel->findAllAtivos();
        
        // Busca turmas ativas com todos os dados
        $sql = "SELECT t.*, md.nome as modalidade_nome, pr.nome as professor_nome
                FROM turmas t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE t.ativo = 1
                ORDER BY md.nome, t.nome";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $turmas = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        
        // Processa dias da semana para cada turma
        foreach ($turmas as &$turma) {
            if (!empty($turma['dias_da_semana'])) {
                $dias = json_decode($turma['dias_da_semana'], true);
                $turma['dias_array'] = is_array($dias) ? $dias : [];
            } else {
                $turma['dias_array'] = [];
            }
        }
        unset($turma);

        $content = $this->view->render('matriculas/create', [
            'usuario' => $usuario,
            'alunos' => $alunos,
            'turmas' => $turmas,
            'planos' => $planos
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Cadastrar Matrícula - Sistema de Escola de Esportes',
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
            $this->redirect('/matriculas');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/matriculas/create');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/matriculas/create');
            return;
        }

        $matriculaModel = new Matricula();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['aluno_id']) || !is_numeric($_POST['aluno_id']) || (int)$_POST['aluno_id'] <= 0) {
            $errors[] = 'Aluno é obrigatório.';
        }

        if (empty($_POST['turma_id']) || !is_numeric($_POST['turma_id']) || (int)$_POST['turma_id'] <= 0) {
            $errors[] = 'Turma é obrigatória.';
        }

        if (empty($_POST['plano_id']) || !is_numeric($_POST['plano_id']) || (int)$_POST['plano_id'] <= 0) {
            $errors[] = 'Plano é obrigatório.';
        }

        if (empty($_POST['dt_inicio'])) {
            $errors[] = 'Data de início é obrigatória.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/matriculas/create');
            return;
        }

        $alunoId = (int)$_POST['aluno_id'];
        $turmaId = (int)$_POST['turma_id'];

        // Verifica se aluno já está matriculado na turma
        if ($matriculaModel->alunoJaMatriculado($alunoId, $turmaId)) {
            $_SESSION['error'] = 'Aluno já está matriculado nesta turma.';
            $this->redirect('/matriculas/create');
            return;
        }

        // Calcula data de término se não fornecida
        $dtFim = !empty($_POST['dt_fim']) ? $_POST['dt_fim'] : null;
        if (empty($dtFim)) {
            $planoModel = new Plano();
            $plano = $planoModel->find((int)$_POST['plano_id']);
            if ($plano && !empty($plano['quantidade_meses'])) {
                $dtInicio = new \DateTime($_POST['dt_inicio']);
                $dtInicio->modify('+' . (int)$plano['quantidade_meses'] . ' months');
                $dtInicio->modify('-1 day'); // Último dia do período
                $dtFim = $dtInicio->format('Y-m-d');
            }
        }

        // Prepara dados
        $data = [
            'aluno_id' => $alunoId,
            'turma_id' => $turmaId,
            'plano_id' => (int)$_POST['plano_id'],
            'dt_inicio' => $_POST['dt_inicio'],
            'dt_fim' => $dtFim,
            'status' => $_POST['status'] ?? 'Ativa'
        ];

        try {
            $pdo = \App\Core\Model::getConnection();
            $pdo->beginTransaction();

            // Cria matrícula
            $id = $matriculaModel->create($data);
            
            // Gera mensalidades automaticamente
            $mensalidadesGeradas = $matriculaModel->gerarMensalidadesAutomaticas($id);
            $totalMensalidades = count($mensalidadesGeradas);

            $pdo->commit();
            
            $mensagem = 'Matrícula cadastrada com sucesso!';
            if ($totalMensalidades > 0) {
                $mensagem .= " {$totalMensalidades} mensalidade(s) gerada(s) automaticamente.";
            }
            
            $_SESSION['success'] = $mensagem;
            $this->redirect('/matriculas/' . $id);
        } catch (\Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['error'] = 'Erro ao cadastrar matrícula: ' . $e->getMessage();
            $this->redirect('/matriculas/create');
        }
    }

    public function storeMultiple(): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/matriculas');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/matriculas/create');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/matriculas/create');
            return;
        }

        $matriculaModel = new Matricula();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['aluno_id']) || !is_numeric($_POST['aluno_id']) || (int)$_POST['aluno_id'] <= 0) {
            $errors[] = 'Aluno é obrigatório.';
        }

        if (empty($_POST['turmas']) || !is_array($_POST['turmas']) || count($_POST['turmas']) === 0) {
            $errors[] = 'Selecione pelo menos uma turma.';
        }

        if (empty($_POST['plano_id']) || !is_numeric($_POST['plano_id']) || (int)$_POST['plano_id'] <= 0) {
            $errors[] = 'Plano é obrigatório.';
        }

        if (empty($_POST['dt_inicio'])) {
            $errors[] = 'Data de início é obrigatória.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/matriculas/create');
            return;
        }

        $alunoId = (int)$_POST['aluno_id'];
        $turmasIds = array_map('intval', $_POST['turmas']);
        $turmasIds = array_filter($turmasIds, function($id) { return $id > 0; });

        if (empty($turmasIds)) {
            $_SESSION['error'] = 'Nenhuma turma válida selecionada.';
            $this->redirect('/matriculas/create');
            return;
        }

        // Verifica se aluno já está matriculado em alguma das turmas
        $turmasJaMatriculadas = [];
        foreach ($turmasIds as $turmaId) {
            if ($matriculaModel->alunoJaMatriculado($alunoId, $turmaId)) {
                // Busca nome da turma
                $sql = "SELECT t.nome FROM turmas t WHERE t.id = :turma_id";
                $stmt = \App\Core\Model::getConnection()->prepare($sql);
                $stmt->execute(['turma_id' => $turmaId]);
                $turma = $stmt->fetch();
                if ($turma) {
                    $turmasJaMatriculadas[] = $turma['nome'];
                }
            }
        }

        if (!empty($turmasJaMatriculadas)) {
            $_SESSION['error'] = 'Aluno já está matriculado nas seguintes turmas: ' . implode(', ', $turmasJaMatriculadas);
            $this->redirect('/matriculas/create');
            return;
        }

        // Calcula data de término se não fornecida
        $dtFim = !empty($_POST['dt_fim']) ? $_POST['dt_fim'] : null;
        if (empty($dtFim)) {
            $planoModel = new Plano();
            $plano = $planoModel->find((int)$_POST['plano_id']);
            if ($plano && !empty($plano['quantidade_meses'])) {
                $dtInicio = new \DateTime($_POST['dt_inicio']);
                $dtInicio->modify('+' . (int)$plano['quantidade_meses'] . ' months');
                $dtInicio->modify('-1 day'); // Último dia do período
                $dtFim = $dtInicio->format('Y-m-d');
            }
        }

        // Cria todas as matrículas primeiro
        $matriculasCriadas = [];
        $erros = [];
        $pdo = \App\Core\Model::getConnection();

            try {
                $pdo->beginTransaction();

            foreach ($turmasIds as $turmaId) {
                $data = [
                    'aluno_id' => $alunoId,
                    'turma_id' => $turmaId,
                    'plano_id' => (int)$_POST['plano_id'],
                    'dt_inicio' => $_POST['dt_inicio'],
                    'dt_fim' => $dtFim,
                    'status' => $_POST['status'] ?? 'Ativa'
                ];

                $id = $matriculaModel->create($data);
                $matriculasCriadas[] = $id;
            }
            
            $pdo->commit();
            
            // Gera mensalidades apenas UMA vez usando a primeira matrícula
            // Isso garante que será criada apenas uma mensalidade consolidada por competência
            if (!empty($matriculasCriadas)) {
                $primeiraMatriculaId = $matriculasCriadas[0];
                $mensalidadesGeradas = $matriculaModel->gerarMensalidadesAutomaticas($primeiraMatriculaId);
                $totalMensalidadesGeradas = count($mensalidadesGeradas);
            } else {
                $totalMensalidadesGeradas = 0;
            }
            } catch (\Exception $e) {
                if (isset($pdo) && $pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                
            $_SESSION['error'] = 'Erro ao cadastrar matrículas: ' . $e->getMessage();
            $this->redirect('/matriculas/create');
            return;
        }

        // Retorna resultado
        if (!empty($matriculasCriadas)) {
            $total = count($matriculasCriadas);
            $mensagem = "{$total} matrícula(s) criada(s) com sucesso!";
            
            if ($totalMensalidadesGeradas > 0) {
                $mensagem .= " {$totalMensalidadesGeradas} mensalidade(s) gerada(s) automaticamente (consolidadas).";
            }
            
                $_SESSION['success'] = $mensagem;
            
            // Redireciona para a primeira matrícula criada ou lista
            if (count($matriculasCriadas) === 1) {
                $this->redirect('/matriculas/' . $matriculasCriadas[0]);
            } else {
                $this->redirect('/matriculas?aluno_id=' . $alunoId);
            }
        } else {
            $_SESSION['error'] = 'Nenhuma matrícula foi criada.';
            $this->redirect('/matriculas/create');
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
            $this->redirect('/matriculas');
            return;
        }

        $matriculaModel = new Matricula();
        $matricula = $matriculaModel->findWithRelations($id);

        if (!$matricula) {
            $_SESSION['error'] = 'Matrícula não encontrada.';
            $this->redirect('/matriculas');
            return;
        }

        // Busca estatísticas
        $totalMensalidades = $matriculaModel->countMensalidades($id);
        $totalMensalidadesAbertas = $matriculaModel->countMensalidadesAbertas($id);

        // Busca mensalidades
        $sql = "SELECT * FROM mensalidades WHERE matricula_id = :matricula_id ORDER BY competencia DESC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute(['matricula_id' => $id]);
        $mensalidades = $stmt->fetchAll() ?: [];

        // Calcula resumo financeiro
        $mensalidadeModel = new \App\Models\Mensalidade();
        $pagamentoModel = new \App\Models\Pagamento();
        $totalPago = 0.0;
        $totalPendente = 0.0;
        $totalAtrasado = 0.0;
        $valorTotalMensalidades = 0.0;
        $hoje = new \DateTime();

        foreach ($mensalidades as &$mensalidade) {
            $valorTotal = $mensalidadeModel->calcularValorTotal((int)$mensalidade['id']);
            $valorTotalMensalidades += $valorTotal;
            $valorPago = $pagamentoModel->getTotalPago((int)$mensalidade['id']);
            $totalPago += $valorPago;

            // Verifica se está atrasada
            $dtVencimento = new \DateTime($mensalidade['dt_vencimento']);
            $isAtrasada = $mensalidade['status'] !== 'Pago' 
                && $mensalidade['status'] !== 'Cancelado'
                && $dtVencimento < $hoje;

            if ($isAtrasada) {
                $totalAtrasado += ($valorTotal - $valorPago);
            }

            if ($mensalidade['status'] !== 'Pago' && $mensalidade['status'] !== 'Cancelado') {
                $totalPendente += ($valorTotal - $valorPago);
            }

            // Adiciona informações calculadas ao array
            $mensalidade['valor_total'] = $valorTotal;
            $mensalidade['valor_pago'] = $valorPago;
            $mensalidade['valor_restante'] = $valorTotal - $valorPago;
            $mensalidade['is_atrasada'] = $isAtrasada;
        }
        unset($mensalidade);

        // Busca presenças recentes
        $sql = "SELECT * FROM presencas WHERE matricula_id = :matricula_id ORDER BY data DESC LIMIT 10";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute(['matricula_id' => $id]);
        $presencas = $stmt->fetchAll() ?: [];

        $content = $this->view->render('matriculas/show', [
            'usuario' => $usuario,
            'matricula' => $matricula,
            'totalMensalidades' => $totalMensalidades,
            'totalMensalidadesAbertas' => $totalMensalidadesAbertas,
            'mensalidades' => $mensalidades,
            'presencas' => $presencas,
            'totalPago' => $totalPago,
            'totalPendente' => $totalPendente,
            'totalAtrasado' => $totalAtrasado,
            'valorTotalMensalidades' => $valorTotalMensalidades
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes da Matrícula - Sistema de Escola de Esportes',
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
            $this->redirect('/matriculas');
            return;
        }

        $matriculaModel = new Matricula();
        $matricula = $matriculaModel->find($id);

        if (!$matricula) {
            $_SESSION['error'] = 'Matrícula não encontrada.';
            $this->redirect('/matriculas');
            return;
        }

        // Busca dados para formulário
        $alunoModel = new Aluno();
        $planoModel = new Plano();
        
        $alunos = $alunoModel->findAllAtivos();
        $planos = $planoModel->findAllAtivos();
        
        // Busca turmas ativas
        $sql = "SELECT t.*, md.nome as modalidade_nome, pr.nome as professor_nome
                FROM turmas t
                INNER JOIN modalidades md ON t.modalidade_id = md.id
                INNER JOIN professores pr ON t.professor_id = pr.id
                WHERE t.ativo = 1
                ORDER BY md.nome, t.nome";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $turmas = $stmt->fetchAll() ?: [];

        $content = $this->view->render('matriculas/edit', [
            'usuario' => $usuario,
            'matricula' => $matricula,
            'alunos' => $alunos,
            'turmas' => $turmas,
            'planos' => $planos
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Editar Matrícula - Sistema de Escola de Esportes',
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
            $this->redirect('/matriculas');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/matriculas/' . $id . '/edit');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/matriculas/' . $id . '/edit');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/matriculas');
            return;
        }

        $matriculaModel = new Matricula();
        $matricula = $matriculaModel->find($id);

        if (!$matricula) {
            $_SESSION['error'] = 'Matrícula não encontrada.';
            $this->redirect('/matriculas');
            return;
        }

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['aluno_id']) || !is_numeric($_POST['aluno_id']) || (int)$_POST['aluno_id'] <= 0) {
            $errors[] = 'Aluno é obrigatório.';
        }

        if (empty($_POST['turma_id']) || !is_numeric($_POST['turma_id']) || (int)$_POST['turma_id'] <= 0) {
            $errors[] = 'Turma é obrigatória.';
        }

        if (empty($_POST['plano_id']) || !is_numeric($_POST['plano_id']) || (int)$_POST['plano_id'] <= 0) {
            $errors[] = 'Plano é obrigatório.';
        }

        if (empty($_POST['dt_inicio'])) {
            $errors[] = 'Data de início é obrigatória.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/matriculas/' . $id . '/edit');
            return;
        }

        $alunoId = (int)$_POST['aluno_id'];
        $turmaId = (int)$_POST['turma_id'];

        // Verifica se aluno já está matriculado na turma (excluindo a matrícula atual)
        if ($matriculaModel->alunoJaMatriculado($alunoId, $turmaId, $id)) {
            $_SESSION['error'] = 'Aluno já está matriculado nesta turma.';
            $this->redirect('/matriculas/' . $id . '/edit');
            return;
        }

        // Prepara dados
        $data = [
            'aluno_id' => $alunoId,
            'turma_id' => $turmaId,
            'plano_id' => (int)$_POST['plano_id'],
            'dt_inicio' => $_POST['dt_inicio'],
            'dt_fim' => !empty($_POST['dt_fim']) ? $_POST['dt_fim'] : null,
            'status' => $_POST['status'] ?? 'Ativa'
        ];

        try {
            $matriculaModel->update($id, $data);
            
            $_SESSION['success'] = 'Matrícula atualizada com sucesso!';
            $this->redirect('/matriculas/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar matrícula: ' . $e->getMessage();
            $this->redirect('/matriculas/' . $id . '/edit');
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
            $this->redirect('/matriculas');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/matriculas');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/matriculas');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/matriculas');
            return;
        }

        $matriculaModel = new Matricula();
        $matricula = $matriculaModel->find($id);

        if (!$matricula) {
            $_SESSION['error'] = 'Matrícula não encontrada.';
            $this->redirect('/matriculas');
            return;
        }

        try {
            // Verifica se tem mensalidades em aberto
            $mensalidadesAbertas = $matriculaModel->countMensalidadesAbertas($id);
            if ($mensalidadesAbertas > 0) {
                $_SESSION['error'] = 'Não é possível excluir matrícula com mensalidades em aberto.';
                $this->redirect('/matriculas/' . $id);
                return;
            }

            $matriculaModel->delete($id);
            $_SESSION['success'] = 'Matrícula excluída com sucesso!';
            $this->redirect('/matriculas');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir matrícula: ' . $e->getMessage();
            $this->redirect('/matriculas/' . $id);
        }
    }
}

