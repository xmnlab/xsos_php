/**
 * XSOS - Sistema de gest�o Ordem de Servi�o
 * Copyright (C) 2008  Ivan Ogassavara  
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @license    http://www.gnu.org/licenses    GNU License
 * @copyright  Copyright (C) 2008  Ivan Ogassavara
 * @category   mascara
 * @package    sos
 * @subpackage sos.mascara
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

function RelatorioFlx()
{
    /* ATRIBUTO */
    var xml;
    
    /* METODO */
    var consulta;
    var carrega;
    
    this.xml          = new Array();
    this.carrega      = relatorioFlx_carrega;
    this.consulta     = relatorioFlx_consulta;

}

function relatorioFlx_carrega(entidade, pedido)
{
    switch (entidade) {
    case 'carga':
        switch (pedido) {
        case 'abertura':
            window.open('sos.php?entidade=relatorio&pedido=carga_abertura', '_self');
            return;
            
        case 'armazenar':
            document.frmSos.submit();
            return;
            
        }
        break;
    case 'relatorio':
        switch (pedido) {
        case 'listar':
            window.open('sos.php?entidade=relatorio&pedido=listar', '_self');
            return;
        }
    }
}

function relatorioFlx_consulta(tipo)
{
    switch (tipo) {
    case 'tabela':
        var url       = 'sos.php'
        var parametro = 'entidade=relatorio&pedido=listar_tabela';
        
        var lista = document.getElementById('slcTabela');
        var atributo = new Array('tabela', 'tabela');
    
        artesaoXmn.carregaListaSelecao(url, parametro, lista, 'tabela', atributo);
        break;
        
    case 'campo':
        var url       = 'sos.php'
        var parametro = 
              'entidade=relatorio&pedido=listar_campo&tabela=' 
            + $('#slcTabela').attr('value');
        
        var lista = document.createElement('select');
        
        lista.id = 'slc' + $('#slcTabela').attr('value') + 'Campos';
        $(lista).attr('multiple', 'multiple');
        $('#capaCampo').append(lista);
        
        var atributo = new Array('campo', 'campo');

        artesaoXmn.carregaListaSelecao(url, parametro, lista, 'campo', atributo);
        break;
        
    case 'lista_relatorio':
        var url       = 'sos.php'
        var parametro = 'entidade=relatorio&pedido=listar_relatorio';
        var objDOM    = null;
        var atributo  = new Array();
    
        this.xml = xaman.requisitaListaDados(url, parametro);
        
        while ($('#slcRelatorio').lenght > 0){
        	$('#slcRelatorio').remove(0);
        }
        
        for (i = 0; i < this.xml.getElementsByTagName('relatorio').length; i++) {
            atributo = this.xml.getElementsByTagName('relatorio')[i];
            
            objDOM       = document.createElement('option');
            objDOM.value = atributo.getAttribute('nome')
            objDOM.text  = atributo.getAttribute('titulo');
            $('#slcRelatorio').append(objDOM);
        }
        
        $('#slcRelatorio').focus();
        break;
        
    case 'relatorio':
        $('#colBotao').css('visibility', 'hidden');
        $('#relConteudo').html('');
        
        if ($('#slcRelatorio').attr('value') == '') {
            $('#relatorio, #relatorio div').css('visibility', 'hidden');
            return false;
        } else {
            $('#relatorio, #relatorio div').css('visibility', 'visible');
        }
        
        $('#filtro option').each(function () {
            $(this).remove(0);
        });
        
        $('#colValores input[type="text"], #colValores label').each(function () {
            $(this).remove(0);
        });
        
        $('#' + $('#slcRelatorio').attr('value'), this.xml).each(function () {
           
            var objeto   = new Array();
            var i        = new Array();
            
            if ($(this).attr('filtro') == 'true') {
                $('#capaFiltro').css('visibility', 'visible');
            } else {
            	$('#capaFiltro').css('visibility', 'hidden');
            }
            
            $('#relNome').text($(this).attr('titulo'));
            
            $('#filtro_fixo').val('');
                        
            objeto['rel_selecionado'] = $('#' 
               + $('#slcRelatorio option:selected').val());
            
            $('#relNome').html($(objeto['rel_selecionado']).attr('titulo'));
            
            
            $("lista_consulta > consulta", $(this)).each(function () {
                
                $("lista_tabela > tabela", $(this)).each(function () {
                    
                });
                
                $("lista_filtro > filtro[fixo='true']", $(this)).each(function () {
                  $('#filtro_fixo').val(
                      $('#filtro_fixo').val() 
                    + ';'
                    + $(this).attr('enlace'));
                });
                
                $("lista_filtro > filtro", $(this)).each(function () {
                    if($(this).attr('fixo') != 'true') {
                        objeto['html']       = document.createElement('option');
                        objeto['html'].value = $(this).attr('nome')
                        objeto['html'].text  = $(this).attr('titulo');
                        
                        $('#filtro').append(objeto['html']);
                        
                    } else {
                        objeto['html']       = document.createElement('input');
                        objeto['html'].type  = 'hidden';
                        objeto['html'].value = $(this).attr('enlace')
                        
                        $('#frmSos').append(objeto['html']);
                    }
                });
            });
            
        });
        
        if ($('#filtro').length > 0) {
            this.consulta('filtro');
        }
        $('#filtro').focus();
        return;
        
    case 'filtro':
        var campo = new Array();
        var consulta;
        var filtro;
        var objeto;
        var tmp;
        
        $('#colValores input[type="text"], #colValores label').each(function () {
            $(this).remove(0);
        });
        
        $('#colBotao').css('visibility', 'hidden');
        
        if ($('#filtro option:selected').val() == '') {
            this.consulta('consulta');
            
        } else {
            filtro =  $('#filtro option:selected').val();
            
            if (filtro == undefined) {
                return;
            }
            
            objeto = $('consulta[nome="principal"] filtro[nome="'
                + filtro + '"]', 
                $('#' + $('#slcRelatorio').attr('value'), this.xml));
            
            campo['atributos']           = new Array();
            campo['atributos']['titulo'] = new Array();
            campo['atributos']['filtro'] = new Array();
            
            $('campo', objeto).each (function () {
                tmp           = document.createElement('label');
                tmp.innerHTML = '&nbsp;&nbsp;' 
                    + $(this).attr('titulo') 
                    + '&nbsp;&nbsp;';
                $('#colValores').append(tmp);
                
                tmp      = document.createElement('input');
                tmp.type = 'text';
                tmp.name = 'filtro_txt_' + $(this).attr('nome');
                tmp.id   = 'filtro_txt_' + $(this).attr('nome');
                
                $('#colValores').append(tmp);
                
                $(tmp).mask($(this).attr('formato'));
                
            });
            
            if ($('#colValores').html() != '') {
                $('#colBotao').css('visibility', 'visible');
            }
            
        }
        return;
        
    case 'consultar':
        var filtro;
        var objeto;
        var consulta;
        var sql;
        var lstAtributo    = new Array();
        var lstAtributoTit = new Array();
        var lstFerramenta  = new Array()
        
        objeto = new Array();
        sql    = new Array();
        
        objeto['valor'      ] = new Array();
        
        objeto['filtro_nome'] =  $('#filtro option:selected').val();
        objeto['consulta'   ] = $('consulta[nome="principal"]', 
            $('#' + $('#slcRelatorio').attr('value'), this.xml));
        
        objeto['filtro'     ] = $('filtro[nome="' + objeto['filtro_nome'] + '"]', 
            objeto['consulta']);
        
        
        objeto['enlace'] = objeto['filtro'].attr('enlace').split('?');
        
        sql['campo'  ] = '';
        sql['tabela' ] = '';
        sql['filtro' ] = '';
        sql['literal'] = '';
        
        $('#colValores input[type="text"]').each(function () {
            objeto['valor'].push($(this).attr('value'));
        });
        
        for (cont in objeto['enlace']) {
            sql['filtro'] += objeto['enlace'][cont];
            if (objeto['valor'][cont] != undefined) {
                sql['filtro'] += objeto['valor'][cont];
            }
        }
        
        $('#frmSos input[type="hidden"]').each(function () {
            if (sql['filtro'] != '') {
                sql['filtro'] += ' AND ';
            }
            
            sql['filtro'] += $(this).attr('value') + ' ';
        });
        
        $('lista_referencia', objeto['consulta']).each(function () {
            $('referencia', $(this)).each(function () {
                if ($(this).attr('arquetipo') != undefined
                    && $(this).attr('arquetipo') != '') {
                
                    sql['tabela'] += ' INNER JOIN ' + $(this).attr('nome')
                      + ' ON (' + $(this).attr('chave') + ')';
                    
                } else {
                    if (sql['tabela'] != '') {
                        sql['tabela'] += ', ';
                    }
                    sql['tabela'] += $(this).attr('nome');
                }
            });
        });
        
        lstAtributo[0] = Array('osxId');
        
        $('lista_campo', objeto['consulta']).each(function () {
            $('campo', $(this)).each(function () {
                if (sql['campo'] != '') {
                    sql['campo'] += ', ';
                }
                sql['campo'] += $(this).attr('enlace') 
                    + ' AS ' + $(this).attr('nome');
                lstAtributo.push($(this).attr('nome'));
                lstAtributoTit.push($(this).attr('titulo'));
            });
        });
        
        objeto['sql'] = 'SELECT ' + sql['campo'] + ' FROM ' + sql['tabela'] + ' WHERE ' + sql['filtro'];
        
        objeto['parametro'] = 'entidade=relatorio&pedido=consultar&sql=' 
            + objeto['sql'];
        
        objeto['mascara'] = $('mascara', $('#' + $('#slcRelatorio').attr('value'), 
            this.xml));
            
        $('#relConteudo').html(objeto['mascara'].text());
        
        consulta = xaman.requisitaListaDados('sos.php', objeto['parametro']);
        
        artesaoXmn.criaArtefato('lista', $('#relConteudo > lista[nome="principal"]'));
        artesaoXmn.carregaArtefato('lista', 
          $('#relConteudo > lista[nome="principal"]'),
          consulta);
        
        objeto['resultado'] = '';
        
    }
    
    return;
}

var relatorioFlx = new RelatorioFlx();