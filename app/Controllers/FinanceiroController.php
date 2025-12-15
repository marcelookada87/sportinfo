<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Mensalidade;
use App\Models\Pagamento;
use App\Models\Matricula;
use App\Models\Aluno;
use App\Models\Usuario;

/**
 * Controller de Financeiro
 */
class FinanceiroController extends Controller
{
    /**
     * Lista mensalidades
     */
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

        $mensalidadeModel = new Mensalidade();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'competencia' => $_GET['competencia'] ?? '',
            'dt_vencimento_inicio' => $_GET['dt_vencimento_inicio'] ?? '',
            'dt_vencimento_fim' => $_GET['dt_vencimento_fim'] ?? '',
            'aluno_id' => $_GET['aluno_id'] ?? '',
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $mensalidades = $mensalidadeModel->findAllWithFilters($filters);
        $total = $mensalidadeModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        // Calcula e atualiza multa e juros automaticamente para mensalidades vencidas
        foreach ($mensalidades as &$mensalidade) {
            // Atualiza multa e juros se necessário
            $mensalidadeModel->calcularEAtualizarMultaEJuros((int)$mensalidade['id']);
            
            // Busca novamente para pegar valores atualizados
            $mensalidadeAtualizada = $mensalidadeModel->find((int)$mensalidade['id']);
            if ($mensalidadeAtualizada) {
                $mensalidade['multa'] = $mensalidadeAtualizada['multa'];
                $mensalidade['juros'] = $mensalidadeAtualizada['juros'];
                $mensalidade['status'] = $mensalidadeAtualizada['status'];
            }
            
            $mensalidade['valor_total'] = $mensalidadeModel->calcularValorTotal((int)$mensalidade['id']);
            $mensalidade['is_atrasada'] = $mensalidadeModel->isAtrasada((int)$mensalidade['id']);
        }
        unset($mensalidade);

        // Agrupa mensalidades por aluno, competência e modalidade (consolidação)
        // IMPORTANTE: O aluno paga apenas UMA vez pelo plano por modalidade
        // Se tiver matrículas em modalidades diferentes, cada modalidade terá sua mensalidade separada
        $mensalidadesAgrupadas = [];
        foreach ($mensalidades as $mensalidade) {
            $alunoId = isset($mensalidade['aluno_id']) ? (int)$mensalidade['aluno_id'] : 0;
            $competencia = $mensalidade['competencia'] ?? '';
            $modalidadeNome = $mensalidade['modalidade_nome'] ?? '';
            
            if ($alunoId > 0 && !empty($competencia)) {
                // Chave inclui modalidade para separar mensalidades de modalidades diferentes
                $chave = $alunoId . '_' . $competencia . '_' . md5($modalidadeNome);
                
                if (!isset($mensalidadesAgrupadas[$chave])) {
                    // Cria grupo consolidado por modalidade
                    // Usa o valor do plano (não soma), pois o aluno paga apenas uma vez por modalidade
                    $mensalidadesAgrupadas[$chave] = [
                        'aluno_id' => $alunoId,
                        'aluno_nome' => $mensalidade['aluno_nome'] ?? '',
                        'aluno_cpf' => $mensalidade['aluno_cpf'] ?? '',
                        'competencia' => $competencia,
                        'modalidade_nome' => $modalidadeNome,
                        'valor' => (float)($mensalidade['valor'] ?? 0), // Valor do plano (não soma)
                        'desconto' => (float)($mensalidade['desconto'] ?? 0),
                        'multa' => (float)($mensalidade['multa'] ?? 0),
                        'juros' => (float)($mensalidade['juros'] ?? 0),
                        'valor_total' => (float)($mensalidade['valor_total'] ?? 0), // Valor do plano
                        'dt_vencimento' => $mensalidade['dt_vencimento'] ?? '',
                        'status' => 'Aberto',
                        'is_atrasada' => false,
                        'mensalidades' => [],
                        'primeira_mensalidade_id' => (int)$mensalidade['id']
                    ];
                }
                
                // NÃO soma valores - o aluno paga apenas o valor do plano uma vez
                // Mantém o primeiro valor encontrado (que é o valor do plano)
                
                // Usa a data de vencimento mais próxima (mais antiga) ou a primeira
                if (!empty($mensalidade['dt_vencimento'])) {
                    if (empty($mensalidadesAgrupadas[$chave]['dt_vencimento']) || 
                        $mensalidade['dt_vencimento'] < $mensalidadesAgrupadas[$chave]['dt_vencimento']) {
                        $mensalidadesAgrupadas[$chave]['dt_vencimento'] = $mensalidade['dt_vencimento'];
                    }
                }
                
                // Verifica se está atrasada
                if ($mensalidade['is_atrasada'] ?? false) {
                    $mensalidadesAgrupadas[$chave]['is_atrasada'] = true;
                }
                
                // Adiciona mensalidade individual ao grupo
                $mensalidadesAgrupadas[$chave]['mensalidades'][] = $mensalidade;
            }
        }
        
        // Calcula status consolidado após agrupar todas as mensalidades
        foreach ($mensalidadesAgrupadas as $chave => &$grupo) {
            $totalPago = 0;
            $totalCancelado = 0;
            $totalAtrasado = 0;
            
            foreach ($grupo['mensalidades'] as $msg) {
                $status = $msg['status'] ?? 'Aberto';
                if ($status === 'Pago') {
                    $totalPago++;
                } elseif ($status === 'Cancelado') {
                    $totalCancelado++;
                } elseif ($status === 'Atrasado' || ($msg['is_atrasada'] ?? false)) {
                    $totalAtrasado++;
                }
            }
            
            $totalMensalidades = count($grupo['mensalidades']);
            
            // Define status consolidado
            if ($totalCancelado === $totalMensalidades) {
                $grupo['status'] = 'Cancelado';
            } elseif ($totalPago === $totalMensalidades) {
                $grupo['status'] = 'Pago';
            } elseif ($totalPago > 0) {
                $grupo['status'] = 'Parcial';
            } elseif ($totalAtrasado > 0 || $grupo['is_atrasada']) {
                $grupo['status'] = 'Atrasado';
            } else {
                $grupo['status'] = 'Aberto';
            }

            // Calcula dias de atraso para exibição compacta na lista
            $grupo['dias_atraso'] = 0;
            if (!empty($grupo['dt_vencimento'])) {
                try {
                    $hoje = new \DateTime();
                    $hoje->setTime(0, 0, 0);
                    $vencimento = new \DateTime($grupo['dt_vencimento']);
                    $vencimento->setTime(0, 0, 0);

                    if ($hoje > $vencimento) {
                        $grupo['dias_atraso'] = (int)$hoje->diff($vencimento)->days;
                    }
                } catch (\Exception $e) {
                    // Em caso de data inválida, mantém dias_atraso como 0
                    $grupo['dias_atraso'] = 0;
                }
            }
        }

        // Busca estatísticas
        $estatisticas = $mensalidadeModel->getEstatisticas($filters);

        // Busca alunos para filtro
        $alunoModel = new Aluno();
        $alunos = $alunoModel->all([], 'nome ASC');

        $content = $this->view->render('financeiro/list', [
            'usuario' => $usuario,
            'mensalidades' => $mensalidades,
            'mensalidadesAgrupadas' => $mensalidadesAgrupadas,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages,
            'estatisticas' => $estatisticas,
            'alunos' => $alunos
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Financeiro - Mensalidades - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    /**
     * Atualiza multa e juros de todas as mensalidades vencidas
     */
    public function atualizarMensalidades(): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/financeiro');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/financeiro');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/financeiro');
            return;
        }

        try {
            $mensalidadeModel = new Mensalidade();
            $atualizadas = $mensalidadeModel->atualizarMultaEJurosVencidas();
            
            if ($atualizadas > 0) {
                $_SESSION['success'] = "{$atualizadas} mensalidade(s) atualizada(s) com sucesso! Multa e juros foram recalculados.";
            } else {
                $_SESSION['success'] = 'Nenhuma mensalidade vencida encontrada para atualizar.';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar mensalidades: ' . $e->getMessage();
        }

        $this->redirect('/financeiro');
    }

    /**
     * Cria nova mensalidade
     */
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

        // Busca matrículas ativas
        $matriculaModel = new Matricula();
        $sql = "SELECT m.*, a.nome as aluno_nome, a.cpf as aluno_cpf, pl.nome as plano_nome, pl.valor_base as plano_valor_base
                FROM matriculas m
                INNER JOIN alunos a ON m.aluno_id = a.id
                INNER JOIN planos pl ON m.plano_id = pl.id
                WHERE m.status = 'Ativa'
                ORDER BY a.nome ASC";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute();
        $matriculas = $stmt->fetchAll() ?: [];

        $content = $this->view->render('financeiro/create', [
            'usuario' => $usuario,
            'matriculas' => $matriculas
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Cadastrar Mensalidade - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    /**
     * Salva nova mensalidade
     */
    public function store(): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/financeiro');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/financeiro/create');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/financeiro/create');
            return;
        }

        $mensalidadeModel = new Mensalidade();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['matricula_id'])) {
            $errors[] = 'Matrícula é obrigatória.';
        }

        if (empty($_POST['competencia'])) {
            $errors[] = 'Competência é obrigatória.';
        } elseif (!preg_match('/^\d{4}-\d{2}$/', $_POST['competencia'])) {
            $errors[] = 'Competência deve estar no formato YYYY-MM.';
        }

        if (empty($_POST['valor']) || !is_numeric($_POST['valor']) || (float)$_POST['valor'] <= 0) {
            $errors[] = 'Valor deve ser um número maior que zero.';
        }

        if (empty($_POST['dt_vencimento'])) {
            $errors[] = 'Data de vencimento é obrigatória.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/financeiro/create');
            return;
        }

        // Verifica se já existe mensalidade para esta matrícula e competência
        $sql = "SELECT id FROM mensalidades WHERE matricula_id = :matricula_id AND competencia = :competencia";
        $stmt = \App\Core\Model::getConnection()->prepare($sql);
        $stmt->execute([
            'matricula_id' => (int)$_POST['matricula_id'],
            'competencia' => trim($_POST['competencia'])
        ]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Já existe uma mensalidade para esta matrícula e competência.';
            $this->redirect('/financeiro/create');
            return;
        }

        // Prepara dados
        $data = [
            'matricula_id' => (int)$_POST['matricula_id'],
            'competencia' => trim($_POST['competencia']),
            'valor' => (float)$_POST['valor'],
            'desconto' => !empty($_POST['desconto']) ? (float)$_POST['desconto'] : 0.00,
            'multa' => !empty($_POST['multa']) ? (float)$_POST['multa'] : 0.00,
            'juros' => !empty($_POST['juros']) ? (float)$_POST['juros'] : 0.00,
            'dt_vencimento' => $_POST['dt_vencimento'],
            'status' => $_POST['status'] ?? 'Aberto'
        ];

        try {
            $id = $mensalidadeModel->create($data);
            
            $_SESSION['success'] = 'Mensalidade cadastrada com sucesso!';
            $this->redirect('/financeiro/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao cadastrar mensalidade: ' . $e->getMessage();
            $this->redirect('/financeiro/create');
        }
    }

    /**
     * Exibe detalhes da mensalidade
     */
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
            $this->redirect('/financeiro');
            return;
        }

        $mensalidadeModel = new Mensalidade();
        $mensalidade = $mensalidadeModel->findWithDetails($id);

        if (!$mensalidade) {
            $_SESSION['error'] = 'Mensalidade não encontrada.';
            $this->redirect('/financeiro');
            return;
        }

        // Calcula e atualiza multa e juros automaticamente se necessário
        $mensalidadeModel->calcularEAtualizarMultaEJuros($id);
        
        // Busca novamente para pegar valores atualizados
        $mensalidade = $mensalidadeModel->findWithDetails($id);
        
        // Calcula valor total
        $valorTotal = $mensalidadeModel->calcularValorTotal($id);
        $isAtrasada = $mensalidadeModel->isAtrasada($id);
        
        // Calcula dias de atraso se estiver vencida
        $diasAtraso = 0;
        if ($isAtrasada && !empty($mensalidade['dt_vencimento'])) {
            $hoje = new \DateTime();
            $vencimento = new \DateTime($mensalidade['dt_vencimento']);
            $diasAtraso = (int)$hoje->diff($vencimento)->days;
        }

        // Busca pagamentos desta mensalidade
        $pagamentoModel = new Pagamento();
        $pagamentos = $pagamentoModel->findByMensalidade($id);
        $totalPago = $pagamentoModel->getTotalPago($id);

        $content = $this->view->render('financeiro/show', [
            'usuario' => $usuario,
            'mensalidade' => $mensalidade,
            'valorTotal' => $valorTotal,
            'isAtrasada' => $isAtrasada,
            'diasAtraso' => $diasAtraso,
            'pagamentos' => $pagamentos,
            'totalPago' => $totalPago
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes da Mensalidade - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    /**
     * Edita mensalidade
     */
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
            $this->redirect('/financeiro');
            return;
        }

        $mensalidadeModel = new Mensalidade();
        $mensalidade = $mensalidadeModel->findWithDetails($id);

        if (!$mensalidade) {
            $_SESSION['error'] = 'Mensalidade não encontrada.';
            $this->redirect('/financeiro');
            return;
        }

        $content = $this->view->render('financeiro/edit', [
            'usuario' => $usuario,
            'mensalidade' => $mensalidade
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Editar Mensalidade - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    /**
     * Atualiza mensalidade
     */
    public function update(string $id): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/financeiro');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/financeiro/' . $id . '/edit');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/financeiro/' . $id . '/edit');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/financeiro');
            return;
        }

        $mensalidadeModel = new Mensalidade();
        $mensalidade = $mensalidadeModel->find($id);

        if (!$mensalidade) {
            $_SESSION['error'] = 'Mensalidade não encontrada.';
            $this->redirect('/financeiro');
            return;
        }

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['competencia'])) {
            $errors[] = 'Competência é obrigatória.';
        } elseif (!preg_match('/^\d{4}-\d{2}$/', $_POST['competencia'])) {
            $errors[] = 'Competência deve estar no formato YYYY-MM.';
        }

        if (empty($_POST['valor']) || !is_numeric($_POST['valor']) || (float)$_POST['valor'] <= 0) {
            $errors[] = 'Valor deve ser um número maior que zero.';
        }

        if (empty($_POST['dt_vencimento'])) {
            $errors[] = 'Data de vencimento é obrigatória.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $this->redirect('/financeiro/' . $id . '/edit');
            return;
        }

        // Verifica se já existe outra mensalidade para esta matrícula e competência
        if ($mensalidade['competencia'] !== trim($_POST['competencia']) || $mensalidade['matricula_id'] != (int)$_POST['matricula_id']) {
            $sql = "SELECT id FROM mensalidades WHERE matricula_id = :matricula_id AND competencia = :competencia AND id != :id";
            $stmt = \App\Core\Model::getConnection()->prepare($sql);
            $stmt->execute([
                'matricula_id' => (int)$_POST['matricula_id'],
                'competencia' => trim($_POST['competencia']),
                'id' => $id
            ]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Já existe outra mensalidade para esta matrícula e competência.';
                $this->redirect('/financeiro/' . $id . '/edit');
                return;
            }
        }

        // Prepara dados
        $data = [
            'competencia' => trim($_POST['competencia']),
            'valor' => (float)$_POST['valor'],
            'desconto' => !empty($_POST['desconto']) ? (float)$_POST['desconto'] : 0.00,
            'multa' => !empty($_POST['multa']) ? (float)$_POST['multa'] : 0.00,
            'juros' => !empty($_POST['juros']) ? (float)$_POST['juros'] : 0.00,
            'dt_vencimento' => $_POST['dt_vencimento'],
            'status' => $_POST['status'] ?? 'Aberto'
        ];

        try {
            $mensalidadeModel->update($id, $data);
            
            $_SESSION['success'] = 'Mensalidade atualizada com sucesso!';
            $this->redirect('/financeiro/' . $id);
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao atualizar mensalidade: ' . $e->getMessage();
            $this->redirect('/financeiro/' . $id . '/edit');
        }
    }

    /**
     * Exclui mensalidade
     */
    public function delete(string $id): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/financeiro');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/financeiro');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/financeiro');
            return;
        }

        // Converte string para int
        $id = (int)$id;
        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            $this->redirect('/financeiro');
            return;
        }

        $mensalidadeModel = new Mensalidade();
        $mensalidade = $mensalidadeModel->find($id);

        if (!$mensalidade) {
            $_SESSION['error'] = 'Mensalidade não encontrada.';
            $this->redirect('/financeiro');
            return;
        }

        try {
            // Verifica se tem pagamentos
            $pagamentoModel = new Pagamento();
            $pagamentos = $pagamentoModel->findByMensalidade($id);
            if (!empty($pagamentos)) {
                $_SESSION['error'] = 'Não é possível excluir mensalidade com pagamentos registrados.';
                $this->redirect('/financeiro/' . $id);
                return;
            }

            $mensalidadeModel->delete($id);
            $_SESSION['success'] = 'Mensalidade excluída com sucesso!';
            $this->redirect('/financeiro');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erro ao excluir mensalidade: ' . $e->getMessage();
            $this->redirect('/financeiro/' . $id);
        }
    }

    /**
     * Lista pagamentos
     */
    public function pagamentos(): void
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

        $pagamentoModel = new Pagamento();
        
        // Filtros
        $filters = [
            'search' => $_GET['search'] ?? '',
            'forma' => $_GET['forma'] ?? '',
            'conciliado' => $_GET['conciliado'] ?? '',
            'dt_pagamento_inicio' => $_GET['dt_pagamento_inicio'] ?? '',
            'dt_pagamento_fim' => $_GET['dt_pagamento_fim'] ?? '',
            'aluno_id' => $_GET['aluno_id'] ?? '',
            'limit' => 20,
            'offset' => (int)($_GET['page'] ?? 0) * 20
        ];

        $pagamentos = $pagamentoModel->findAllWithFilters($filters);
        $total = $pagamentoModel->countWithFilters($filters);
        $totalPages = ceil($total / 20);

        // Busca estatísticas
        $estatisticas = $pagamentoModel->getEstatisticas($filters);

        // Busca alunos para filtro
        $alunoModel = new Aluno();
        $alunos = $alunoModel->all([], 'nome ASC');

        $content = $this->view->render('financeiro/pagamentos', [
            'usuario' => $usuario,
            'pagamentos' => $pagamentos,
            'filters' => $filters,
            'total' => $total,
            'currentPage' => (int)($_GET['page'] ?? 0),
            'totalPages' => $totalPages,
            'estatisticas' => $estatisticas,
            'alunos' => $alunos
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Financeiro - Pagamentos - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    /**
     * Cria novo pagamento
     */
    public function pagamentoCreate(string $mensalidadeId): void
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
        $mensalidadeId = (int)$mensalidadeId;
        if ($mensalidadeId <= 0) {
            $_SESSION['error'] = 'ID de mensalidade inválido.';
            $this->redirect('/financeiro');
            return;
        }

        $mensalidadeModel = new Mensalidade();
        $mensalidade = $mensalidadeModel->findWithDetails($mensalidadeId);

        if (!$mensalidade) {
            $_SESSION['error'] = 'Mensalidade não encontrada.';
            $this->redirect('/financeiro');
            return;
        }

        // Calcula valor total e já pago
        $valorTotal = $mensalidadeModel->calcularValorTotal($mensalidadeId);
        $pagamentoModel = new Pagamento();
        $totalPago = $pagamentoModel->getTotalPago($mensalidadeId);
        $valorRestante = max(0, $valorTotal - $totalPago);

        $content = $this->view->render('financeiro/pagamento_create', [
            'usuario' => $usuario,
            'mensalidade' => $mensalidade,
            'valorTotal' => $valorTotal,
            'totalPago' => $totalPago,
            'valorRestante' => $valorRestante
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Registrar Pagamento - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }

    /**
     * Salva novo pagamento
     */
    public function pagamentoStore(): void
    {
        // Verifica autenticação
        if (empty($_SESSION['usuario_id'])) {
            $this->redirect('/login');
            return;
        }

        // Valida método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/financeiro');
            return;
        }

        // Valida CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token de segurança inválido.';
            $this->redirect('/financeiro');
            return;
        }

        // Proteção contra duplo submit
        if ($this->isDuplicateRequest($csrfToken)) {
            $_SESSION['error'] = 'Requisição duplicada detectada. Aguarde um momento antes de tentar novamente.';
            $this->redirect('/financeiro');
            return;
        }

        $pagamentoModel = new Pagamento();
        $mensalidadeModel = new Mensalidade();

        // Validações básicas
        $errors = [];
        
        if (empty($_POST['mensalidade_id'])) {
            $errors[] = 'Mensalidade é obrigatória.';
        }

        if (empty($_POST['forma'])) {
            $errors[] = 'Forma de pagamento é obrigatória.';
        }

        if (empty($_POST['valor_pago']) || !is_numeric($_POST['valor_pago']) || (float)$_POST['valor_pago'] <= 0) {
            $errors[] = 'Valor pago deve ser um número maior que zero.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $mensalidadeId = !empty($_POST['mensalidade_id']) ? (int)$_POST['mensalidade_id'] : 0;
            if ($mensalidadeId > 0) {
                $this->redirect('/financeiro/pagamento/' . $mensalidadeId . '/create');
            } else {
                $this->redirect('/financeiro');
            }
            return;
        }

        $mensalidadeId = (int)$_POST['mensalidade_id'];
        $mensalidade = $mensalidadeModel->find($mensalidadeId);

        if (!$mensalidade) {
            $_SESSION['error'] = 'Mensalidade não encontrada.';
            $this->redirect('/financeiro');
            return;
        }

        // Verifica se valor pago não excede o valor total
        $valorTotal = $mensalidadeModel->calcularValorTotal($mensalidadeId);
        $totalPago = $pagamentoModel->getTotalPago($mensalidadeId);
        $valorPago = (float)$_POST['valor_pago'];

        if ($totalPago + $valorPago > $valorTotal) {
            $_SESSION['error'] = 'Valor pago excede o valor total da mensalidade.';
            $this->redirect('/financeiro/pagamento/' . $mensalidadeId . '/create');
            return;
        }

        // Prepara dados
        $data = [
            'mensalidade_id' => $mensalidadeId,
            'forma' => $_POST['forma'],
            'valor_pago' => $valorPago,
            'dt_pagamento' => !empty($_POST['dt_pagamento']) ? $_POST['dt_pagamento'] : date('Y-m-d H:i:s'),
            'transacao_ref' => !empty($_POST['transacao_ref']) ? trim($_POST['transacao_ref']) : null,
            'conciliado' => isset($_POST['conciliado']) ? (int)$_POST['conciliado'] : 0,
            'observacoes' => !empty($_POST['observacoes']) ? trim($_POST['observacoes']) : null
        ];

        try {
            $pdo = \App\Core\Model::getConnection();
            $pdo->beginTransaction();

            // Cria pagamento
            $pagamentoId = $pagamentoModel->create($data);

            // Atualiza status da mensalidade se necessário
            $novoTotalPago = $totalPago + $valorPago;
            if ($novoTotalPago >= $valorTotal) {
                $mensalidadeModel->updateStatus($mensalidadeId, 'Pago');
            } elseif ($mensalidade['status'] === 'Pago' && $novoTotalPago < $valorTotal) {
                // Se estava pago mas agora não está mais completo
                $dtVencimento = new \DateTime($mensalidade['dt_vencimento']);
                $hoje = new \DateTime();
                $status = $dtVencimento < $hoje ? 'Atrasado' : 'Aberto';
                $mensalidadeModel->updateStatus($mensalidadeId, $status);
            }

            $pdo->commit();
            
            $_SESSION['success'] = 'Pagamento registrado com sucesso!';
            $this->redirect('/financeiro/' . $mensalidadeId);
        } catch (\Exception $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            $_SESSION['error'] = 'Erro ao registrar pagamento: ' . $e->getMessage();
            $this->redirect('/financeiro/pagamento/' . $mensalidadeId . '/create');
        }
    }

    /**
     * Exibe detalhes do pagamento
     */
    public function pagamentoShow(string $id): void
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
            $this->redirect('/financeiro/pagamentos');
            return;
        }

        $pagamentoModel = new Pagamento();
        $pagamento = $pagamentoModel->findWithDetails($id);

        if (!$pagamento) {
            $_SESSION['error'] = 'Pagamento não encontrado.';
            $this->redirect('/financeiro/pagamentos');
            return;
        }

        $content = $this->view->render('financeiro/pagamento_show', [
            'usuario' => $usuario,
            'pagamento' => $pagamento
        ]);
        
        echo $this->view->renderWithLayout('layout', [
            'title' => 'Detalhes do Pagamento - Sistema de Escola de Esportes',
            'content' => $content,
            'usuario' => $usuario
        ]);
    }
}

