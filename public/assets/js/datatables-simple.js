/**
 * DataTables Simple - Versão Vanilla JS (sem jQuery)
 * Funcionalidades: ordenação, busca e paginação
 */

(function() {
    'use strict';

    function initDataTable(tableId, options) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const rows = Array.from(tbody.querySelectorAll('tr'));
        const headers = table.querySelectorAll('thead th');
        
        // Adiciona indicadores de ordenação
        headers.forEach((header, index) => {
            if (index < headers.length - 1) { // Não ordena última coluna (ações)
                header.style.cursor = 'pointer';
                header.style.position = 'relative';
                header.style.paddingRight = '2rem';
                
                const sortIcon = document.createElement('span');
                sortIcon.className = 'sort-icon';
                sortIcon.textContent = '⇅';
                sortIcon.style.cssText = 'position: absolute; right: 0.5rem; opacity: 0.3; font-size: 0.9rem;';
                header.appendChild(sortIcon);
                
                header.addEventListener('click', function() {
                    sortTable(table, index, rows);
                });
            }
        });

        // Adiciona busca rápida se houver campo de busca
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterTable(table, this.value.toLowerCase());
                }, 300);
            });
        }
    }

    function sortTable(table, column, rows) {
        const tbody = table.querySelector('tbody');
        const headers = table.querySelectorAll('thead th');
        const currentDir = table.dataset.sortDir || 'asc';
        const currentCol = parseInt(table.dataset.sortCol || '-1');
        
        const isAsc = currentCol !== column || currentDir !== 'asc';
        
        // Remove indicadores anteriores
        headers.forEach((h, i) => {
            const icon = h.querySelector('.sort-icon');
            if (icon) {
                icon.textContent = '⇅';
                icon.style.opacity = '0.3';
            }
        });
        
        // Atualiza indicador atual
        const currentHeader = headers[column];
        const icon = currentHeader.querySelector('.sort-icon');
        if (icon) {
            icon.textContent = isAsc ? '↑' : '↓';
            icon.style.opacity = '1';
        }
        
        // Ordena linhas
        const sortedRows = rows.slice().sort((a, b) => {
            const aText = a.cells[column]?.textContent?.trim() || '';
            const bText = b.cells[column]?.textContent?.trim() || '';
            
            // Tenta converter para número
            const aNum = parseFloat(aText.replace(/[^\d,.-]/g, '').replace(',', '.'));
            const bNum = parseFloat(bText.replace(/[^\d,.-]/g, '').replace(',', '.'));
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                return isAsc ? aNum - bNum : bNum - aNum;
            }
            
            return isAsc ? aText.localeCompare(bText, 'pt-BR') : bText.localeCompare(aText, 'pt-BR');
        });
        
        // Atualiza tabela
        sortedRows.forEach(row => tbody.appendChild(row));
        
        table.dataset.sortCol = column;
        table.dataset.sortDir = isAsc ? 'asc' : 'desc';
    }

    function filterTable(table, searchText) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    }

    // Inicializa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initDataTable('alunosTable');
            initDataTable('professoresTable');
            initDataTable('modalidadesTable');
            initDataTable('planosTable');
            initDataTable('matriculasTable');
        });
    } else {
        initDataTable('alunosTable');
        initDataTable('professoresTable');
        initDataTable('modalidadesTable');
        initDataTable('planosTable');
        initDataTable('matriculasTable');
    }
})();

