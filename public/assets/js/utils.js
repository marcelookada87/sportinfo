/**
 * Utilitários JavaScript
 * Funções auxiliares para o sistema
 */

(function() {
    'use strict';

    const Utils = {
        /**
         * Formata CPF
         */
        formatCPF: function(value) {
            if (!value) return '';
            value = value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return value;
        },

        /**
         * Formata CNPJ
         */
        formatCNPJ: function(value) {
            if (!value) return '';
            value = value.replace(/\D/g, '');
            if (value.length > 14) value = value.substring(0, 14);
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            return value;
        },

        /**
         * Formata telefone
         */
        formatPhone: function(value) {
            if (!value) return '';
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
         * Formata CEP
         */
        formatCEP: function(value) {
            if (!value) return '';
            value = value.replace(/\D/g, '');
            if (value.length > 8) value = value.substring(0, 8);
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            return value;
        },

        /**
         * Formata moeda
         */
        formatCurrency: function(value) {
            if (!value && value !== 0) return '';
            value = parseFloat(value);
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        },

        /**
         * Remove formatação de moeda
         */
        unformatCurrency: function(value) {
            if (!value) return '0';
            return value.replace(/[^\d,]/g, '').replace(',', '.');
        },

        /**
         * Formata data
         */
        formatDate: function(date, format = 'dd/mm/yyyy') {
            if (!date) return '';
            const d = new Date(date);
            if (isNaN(d.getTime())) return '';

            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();

            return format
                .replace('dd', day)
                .replace('mm', month)
                .replace('yyyy', year);
        },

        /**
         * Formata data e hora
         */
        formatDateTime: function(date) {
            if (!date) return '';
            const d = new Date(date);
            if (isNaN(d.getTime())) return '';

            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            const hours = String(d.getHours()).padStart(2, '0');
            const minutes = String(d.getMinutes()).padStart(2, '0');

            return `${day}/${month}/${year} ${hours}:${minutes}`;
        },

        /**
         * Converte data para formato MySQL
         */
        dateToMySQL: function(date) {
            if (!date) return null;
            const d = new Date(date);
            if (isNaN(d.getTime())) return null;

            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        },

        /**
         * Valida email
         */
        validateEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        /**
         * Valida CPF
         */
        validateCPF: function(cpf) {
            cpf = cpf.replace(/\D/g, '');
            
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
         * Debounce function
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Throttle function
         */
        throttle: function(func, limit) {
            let inThrottle;
            return function(...args) {
                if (!inThrottle) {
                    func.apply(this, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        /**
         * Copia texto para clipboard
         */
        copyToClipboard: function(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    textArea.remove();
                    return Promise.resolve();
                } catch (err) {
                    textArea.remove();
                    return Promise.reject(err);
                }
            }
        },

        /**
         * Download de arquivo
         */
        downloadFile: function(url, filename) {
            const link = document.createElement('a');
            link.href = url;
            link.download = filename || '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        /**
         * Scroll suave para elemento
         */
        scrollTo: function(element, offset = 0) {
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    };

    // Aplica formatação automática em inputs
    document.addEventListener('DOMContentLoaded', function() {
        // CPF
        document.querySelectorAll('input[data-format="cpf"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = Utils.formatCPF(this.value);
            });
        });

        // CNPJ
        document.querySelectorAll('input[data-format="cnpj"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = Utils.formatCNPJ(this.value);
            });
        });

        // Telefone
        document.querySelectorAll('input[data-format="phone"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = Utils.formatPhone(this.value);
            });
        });

        // CEP
        document.querySelectorAll('input[data-format="cep"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = Utils.formatCEP(this.value);
            });
        });

        // Moeda
        document.querySelectorAll('input[data-format="currency"]').forEach(input => {
            input.addEventListener('input', function() {
                const value = this.value.replace(/\D/g, '');
                if (value) {
                    const formatted = (parseInt(value) / 100).toFixed(2);
                    this.value = Utils.formatCurrency(formatted);
                }
            });
        });
    });

    // Expõe Utils globalmente
    window.Utils = Utils;
})();

