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

function OperadoraFlx()
{
    /*
     * ATRIBUTOS
     */
    var mensagemXml;
    
    /*
     * METODOS
     */
    var altera;
    var armazena;
    var cancela;
    var consulta;
    var inclui;
    var inicia;
    var pesquisa;
    var remove;
    var validaTeclaPesquisa;
    
    this.altera              = operadoraFlx_altera;
    this.armazena            = operadoraFlx_armazena;
    this.cancela             = operadoraFlx_cancela;
    this.consulta            = operadoraFlx_consulta;
    this.inclui              = operadoraFlx_inclui;
    this.inicia              = operadoraFlx_inicia;
    this.pesquisa            = operadoraFlx_pesquisa;
    this.remove              = operadoraFlx_remove;
    this.validaTeclaPesquisa = operadoraFlx_validaTeclaPesquisa;
    
    this.inicia();
}

function operadoraFlx_inicia()
{
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=sos&pedido=mensagem&lista=operadora',
        success: function (xml) {
            operadoraFlx.mensagemXml = xml;
        }
    });
}
 
function operadoraFlx_pesquisa(lista)
{
    return operadoraFlx_consulta(
        document.frmSos.pesquisa.value,
        document.frmSos.valPesquisa.value,
        lista);
}

function operadoraFlx_consulta(arquetipo, valor, estrutura)
{
    sosXmn.consulta('aviso', 'limpar');

    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=operadora&pedido=consultar'
            + '&pesquisa='    + arquetipo
            + '&valPesquisa=' + valor,
        success: function (xml) {
            artesaoXmn.carregaArtefato('lista', estrutura, xml);
            
        }
    });
}

function operadoraFlx_inclui()
{
    document.forms[0].entidade.value = "operadora";
    document.forms[0].pedido.value   = "incluir";
    document.forms[0].submit();
}

function operadoraFlx_altera(codigo)
{
    document.forms[0].entidade.value        = "operadora";
    document.forms[0].pedido.value          = "alterar";
    document.forms[0].identificador.value   = codigo;
    document.forms[0].submit();
}

function operadoraFlx_remove(codigo, linha)
{
    sosXmn.consulta('aviso', 'limpar');
    
    if (confirm("Deseja excluir o registro selecionado?")) {
        $.ajax ({
            url: 'sos.php', 
            type: 'POST',
            data: 'entidade=operadora&pedido=remover&identificador=' + codigo,
            success: function (xml) {
                if ($('processo', xml).length > 0) {
                    if ($('processo', xml).attr('estado') == 'true') {
                        $('#listaGradePrincipal tr:eq(' + linha + ')').hide();
                        $('#qtde').text(parseInt($('#qtde').text()) - 1);
                    } else {
                        sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                            operadoraFlx.mensagemXml);
                    }
                } else {
                    $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                    return false;
                }
                
            }
        });
    }
}
 
function operadoraFlx_validaTeclaPesquisa(evento)
{
    var tecla;
    var texto;

    if (window.event){
        tecla = evento.keyCode;
    } else {
        tecla = evento.which;
    }

    if (tecla == 13){
        this.pesquisa();
        return false;
    }
}

function operadoraFlx_armazena()
{
    document.frmSos.entidade.value  = 'operadora';
    document.frmSos.pedido.value    = 'armazenar';
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: $('#frmSos').serialize(),
        success: function (xml) {
            if ($('processo', xml).length > 0) {
                if ($('processo', xml).attr('estado') == 'true') {
                    window.open('sos.php?entidade=operadora&pedido=listar', '_self');
                } else {
                    sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                        operadoraFlx.mensagemXml);
                }
            } else {
                $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                return false;
            }
            
        }
    });
}

function operadoraFlx_cancela()
{
    if(confirm('Deseja cancelar a operação?') == 1) {
    	window.open('sos.php?entidade=operadora&pedido=listar', '_self');
    }
}

var operadoraFlx = new OperadoraFlx();