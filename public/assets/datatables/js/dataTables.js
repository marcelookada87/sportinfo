/*! DataTables 1.13.6
 * Local version - sem CDN
 */
(function( factory ){
	if ( typeof define === 'function' && define.amd ) {
		define( ['jquery'], factory );
	} else if ( typeof module === 'object' && module.exports ) {
		module.exports = function (root, $) {
			if ( ! root ) {
				root = window;
			}
			if ( ! $ || ! $.fn.dataTable ) {
				$ = require('jquery')( root );
			}
			factory( $, root );
			return $.fn.dataTable;
		};
	} else {
		factory( jQuery, window );
	}
}(function( $, window ) {
'use strict';
var DataTable = $.fn.dataTable;
var _instCounter = 0;
var DataTable = function ( selector, options )
{
	if ( ! window.jQuery ) {
		throw new Error( 'DataTables requires jQuery' );
	}
	this._jq = $(selector);
	this._jq[0]._DT_CellIndex = null;
	this.s = {
		dt: this,
		settings: $.extend( true, {}, DataTable.defaults, DataTable.models.defaults, options || {} ),
		ext: {},
		api: null,
		idx: _instCounter++,
		columns: [],
		data: [],
		displayData: [],
		aoData: [],
		aiDisplay: [],
		aiDisplayMaster: [],
		bInitialised: false,
		aoOpenRows: [],
		aoPreSearchCols: [],
		aoHeader: [],
		aoFooter: [],
		lastSearch: {},
		oFeatures: {},
		oScroll: {},
		oLanguage: {
			oAria: {
				sSortAscending: ": ativar para ordenar coluna de forma ascendente",
				sSortDescending: ": ativar para ordenar coluna de forma descendente"
			},
			oPaginate: {
				sFirst: "Primeiro",
				sLast: "Último",
				sNext: "Próximo",
				sPrevious: "Anterior"
			},
			sEmptyTable: "Nenhum registro encontrado",
			sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
			sInfoEmpty: "Mostrando 0 até 0 de 0 registros",
			sInfoFiltered: "(Filtrados de _MAX_ registros)",
			sInfoPostFix: "",
			sInfoThousands: ".",
			sLengthMenu: "Mostrar _MENU_ resultados por página",
			sLoadingRecords: "Carregando...",
			sProcessing: "Processando...",
			sZeroRecords: "Nenhum registro encontrado",
			sSearch: "Buscar:",
			sUrl: "",
			oAria: {
				sSortAscending: ": Ordenar colunas de forma ascendente",
				sSortDescending: ": Ordenar colunas de forma descendente"
			}
		},
		oBrowser: {
			barWidth: 0,
			bBounding: false,
			bScrollOversize: false,
			bScrollbarLeft: false,
			div: null
		},
		oPreviousSearch: {},
		oClasses: {},
		oInit: options || {},
		destroying: false
	};
	this.s.settings.oInstance = this;
	this.s.dt = this;
	this._fnCompatMap();
	this._fnCompatCols();
	this._fnCompatOpts();
	this._fnCompatApi();
	DataTable.ext._builder._buildSettings( this );
	DataTable.ext._builder._buildColumns( this );
	DataTable.ext._builder._buildRowPos( this );
	DataTable.ext._builder._buildHead( this );
	DataTable.ext._builder._buildBody( this );
	DataTable.ext._builder._buildFoot( this );
	DataTable.ext._builder._buildClasses( this );
	DataTable.ext._builder._buildSettings( this );
	DataTable.ext._builder._buildFeatures( this );
	DataTable.ext._builder._buildComplete( this );
	return this;
};
DataTable.prototype = {
	_fnCompatMap: function () {
		var settings = this.s;
		var oInit = settings.oInit;
		if ( oInit && oInit.aoColumns ) {
			settings.aoColumns = oInit.aoColumns;
		}
	},
	_fnCompatCols: function () {
		var settings = this.s;
		if ( settings.aoColumns ) {
			settings.aoColumns.forEach( function (col, i) {
				if ( ! settings.aoColumns[i] ) {
					settings.aoColumns[i] = {};
				}
			});
		}
	},
	_fnCompatOpts: function () {
		var settings = this.s;
		if ( settings.oInit && settings.oInit.bPaginate !== undefined ) {
			settings.oFeatures.bPaginate = settings.oInit.bPaginate;
		}
	},
	_fnCompatApi: function () {
		this.api = function () {
			return new DataTable.Api( this );
		};
	}
};
$.fn.dataTable = DataTable;
$.fn.DataTable = DataTable;
return DataTable;
}));

