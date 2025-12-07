/**
 * Sistema de Escola de Esportes - JavaScript Principal
 * Código moderno, limpo e bem estruturado
 */

(function() {
    'use strict';

    /**
     * Namespace principal da aplicação
     */
    const App = {
        /**
         * Inicialização da aplicação
         */
        init: function() {
            this.initForms();
            this.initTooltips();
            this.initModals();
            this.initTables();
            this.initAccessibility();
        },

        /**
         * Inicializa validação de formulários
         */
        initForms: function() {
            const forms = document.querySelectorAll('form[data-validate]');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!App.validateForm(this)) {
                        e.preventDefault();
                        return false;
                    }
                });

                // Validação em tempo real
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        App.validateField(this);
                    });
                });
            });
        },

        /**
         * Valida formulário completo
         */
        validateForm: function(form) {
            let isValid = true;
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

            inputs.forEach(input => {
                if (!App.validateField(input)) {
                    isValid = false;
                }
            });

            return isValid;
        },

        /**
         * Valida campo individual
         */
        validateField: function(field) {
            const value = field.value.trim();
            const type = field.type;
            let isValid = true;
            let message = '';

            // Remove mensagens anteriores
            const existingError = field.parentElement.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            field.classList.remove('error');

            // Validação de campo obrigatório
            if (field.hasAttribute('required') && !value) {
                isValid = false;
                message = 'Este campo é obrigatório';
            }

            // Validação de email
            if (type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    message = 'Email inválido';
                }
            }

            // Validação de CPF
            if (field.hasAttribute('data-cpf') && value) {
                if (!App.validateCPF(value)) {
                    isValid = false;
                    message = 'CPF inválido';
                }
            }

            // Exibe erro se houver
            if (!isValid) {
                field.classList.add('error');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error';
                errorDiv.textContent = message;
                errorDiv.setAttribute('role', 'alert');
                field.parentElement.appendChild(errorDiv);
            }

            return isValid;
        },

        /**
         * Valida CPF
         */
        validateCPF: function(cpf) {
            cpf = cpf.replace(/[^\d]/g, '');
            
            if (cpf.length !== 11) return false;
            if (/^(\d)\1+$/.test(cpf)) return false;

            let sum = 0;
            for (let i = 0; i < 9; i++) {
                sum += parseInt(cpf.charAt(i)) * (10 - i);
            }
            let digit = 11 - (sum % 11);
            if (digit >= 10) digit = 0;
            if (digit !== parseInt(cpf.charAt(9))) return false;

            sum = 0;
            for (let i = 0; i < 10; i++) {
                sum += parseInt(cpf.charAt(i)) * (11 - i);
            }
            digit = 11 - (sum % 11);
            if (digit >= 10) digit = 0;
            if (digit !== parseInt(cpf.charAt(10))) return false;

            return true;
        },

        /**
         * Inicializa tooltips
         */
        initTooltips: function() {
            const tooltipElements = document.querySelectorAll('[data-tooltip]');
            
            tooltipElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    App.showTooltip(this);
                });
                element.addEventListener('mouseleave', function() {
                    App.hideTooltip();
                });
            });
        },

        /**
         * Exibe tooltip
         */
        showTooltip: function(element) {
            const text = element.getAttribute('data-tooltip');
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = text;
            tooltip.setAttribute('role', 'tooltip');
            document.body.appendChild(tooltip);

            const rect = element.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';
        },

        /**
         * Oculta tooltip
         */
        hideTooltip: function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        },

        /**
         * Inicializa modais
         */
        initModals: function() {
            const modalTriggers = document.querySelectorAll('[data-modal]');
            
            modalTriggers.forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modalId = this.getAttribute('data-modal');
                    App.openModal(modalId);
                });
            });

            const modalCloses = document.querySelectorAll('.modal-close, .modal-overlay');
            modalCloses.forEach(close => {
                close.addEventListener('click', function() {
                    App.closeModal();
                });
            });

            // Fecha modal com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    App.closeModal();
                }
            });
        },

        /**
         * Abre modal
         */
        openModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // Foca no primeiro elemento focável
                const firstFocusable = modal.querySelector('input, button, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            }
        },

        /**
         * Fecha modal
         */
        closeModal: function() {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                activeModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        },

        /**
         * Inicializa tabelas (ordenação, busca)
         */
        initTables: function() {
            const tables = document.querySelectorAll('table[data-sortable]');
            
            tables.forEach(table => {
                const headers = table.querySelectorAll('th[data-sort]');
                headers.forEach(header => {
                    header.style.cursor = 'pointer';
                    header.addEventListener('click', function() {
                        App.sortTable(table, this);
                    });
                });
            });
        },

        /**
         * Ordena tabela
         */
        sortTable: function(table, header) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const columnIndex = Array.from(header.parentElement.children).indexOf(header);
            const isAscending = header.classList.contains('sort-asc');

            // Remove classes de ordenação
            table.querySelectorAll('th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });

            // Ordena linhas
            rows.sort((a, b) => {
                const aText = a.children[columnIndex].textContent.trim();
                const bText = b.children[columnIndex].textContent.trim();
                
                if (isAscending) {
                    return aText.localeCompare(bText);
                } else {
                    return bText.localeCompare(aText);
                }
            });

            // Reordena no DOM
            rows.forEach(row => tbody.appendChild(row));

            // Adiciona classe de ordenação
            header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
        },

        /**
         * Melhorias de acessibilidade
         */
        initAccessibility: function() {
            // Adiciona aria-labels em botões sem texto
            const iconButtons = document.querySelectorAll('button:not([aria-label]):empty');
            iconButtons.forEach(button => {
                const title = button.getAttribute('title');
                if (title) {
                    button.setAttribute('aria-label', title);
                }
            });

            // Gerencia foco em modais
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    const modal = document.querySelector('.modal.active');
                    if (modal) {
                        const focusableElements = modal.querySelectorAll(
                            'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
                        );
                        const firstElement = focusableElements[0];
                        const lastElement = focusableElements[focusableElements.length - 1];

                        if (e.shiftKey && document.activeElement === firstElement) {
                            e.preventDefault();
                            lastElement.focus();
                        } else if (!e.shiftKey && document.activeElement === lastElement) {
                            e.preventDefault();
                            firstElement.focus();
                        }
                    }
                }
            });
        },

        /**
         * Utilitário: Formata CPF
         */
        formatCPF: function(value) {
            value = value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return value;
        },

        /**
         * Utilitário: Formata telefone
         */
        formatPhone: function(value) {
            value = value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            return value;
        },

        /**
         * Utilitário: Formata moeda
         */
        formatCurrency: function(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        },

        /**
         * Utilitário: Exibe mensagem de sucesso
         */
        showSuccess: function(message) {
            App.showAlert(message, 'success');
        },

        /**
         * Utilitário: Exibe mensagem de erro
         */
        showError: function(message) {
            App.showAlert(message, 'error');
        },

        /**
         * Utilitário: Exibe alerta
         */
        showAlert: function(message, type) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.setAttribute('role', 'alert');
            alert.textContent = message;
            
            const container = document.querySelector('main') || document.body;
            container.insertBefore(alert, container.firstChild);
            
            setTimeout(() => {
                alert.remove();
            }, 5000);
        },

        /**
         * Utilitário: Requisição AJAX
         */
        ajax: function(url, options = {}) {
            const defaults = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };

            const config = Object.assign({}, defaults, options);

            return fetch(url, config)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .catch(error => {
                    App.showError('Erro ao processar requisição');
                    throw error;
                });
        }
    };

    // Inicializa quando DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => App.init());
    } else {
        App.init();
    }

    // Expõe App globalmente
    window.App = App;
})();

