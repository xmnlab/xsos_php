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

function AtendenteFlx()
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
    
    this.altera              = atendenteFlx_altera;
    this.armazena            = atendenteFlx_armazena;
    this.cancela             = atendenteFlx_cancela;
    this.consulta            = atendenteFlx_consulta;
    this.inclui              = atendenteFlx_inclui;
    this.inicia              = atendenteFlx_inicia;
    this.pesquisa            = atendenteFlx_pesquisa;
    this.remove              = atendenteFlx_remove;
    this.validaTeclaPesquisa = atendenteFlx_validaTeclaPesquisa;
    
    this.inicia();
}

function atendenteFlx_inicia()
{
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=sos&pedido=mensagem&lista=atendente',
        success: function (xml) {
            atendenteFlx.mensagemXml = xml;
        }
    });
}

function atendenteFlx_pesquisa(lista)
{
    atendenteFlx_consulta(
        document.frmSos.pesquisa.value,
        document.frmSos.valPesquisa.value,
        lista);
}

function atendenteFlx_consulta(arquetipo, valor, estrutura)
{
    sosXmn.consulta('aviso', 'limpar');
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=atendente&pedido=consultar'
            + '&pesquisa='    + arquetipo
            + '&valPesquisa=' + valor,
        success: function (xml) {
            artesaoXmn.carregaArtefato('lista', estrutura, xml);
            
        }
    });
}

function atendenteFlx_inclui()
{
    document.frmSos.entidade.value      = "atendente";
    document.frmSos.pedido.value        = "incluir";
    document.frmSos.submit();
}
function atendenteFlx_altera(codigo)
{
    window.open('sos.php?entidade=atendente&pedido=alterar&identificador='
        + codigo + '&xml=false', '_self');
}
function atendenteFlx_remove(codigo, linha)
{
    sosXmn.consulta('aviso', 'limpar');
    
    if (confirm("Deseja excluir o registro selecionado?")) {
        $.ajax ({
            url: 'sos.php', 
            type: 'POST',
            data: 'entidade=atendente&pedido=remover&identificador=' + codigo,
            success: function (xml) {
                if ($('processo', xml).length > 0) {
                    if ($('processo', xml).attr('estado') == 'true') {
                        $('#listaGradePrincipal tr:eq(' + linha + ')').hide();
                        $('#qtde').text(parseInt($('#qtde').text()) - 1);
                    } else {
                        sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                            atendenteFlx.mensagemXml);
                    }
                } else {
                    $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                    return false;
                }
                
            }
        });
    }
}
 
function atendenteFlx_validaTeclaPesquisa(evento){
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

function atendenteFlx_armazena()
{
    document.frmSos.entidade.value  = 'atendente';
    document.frmSos.pedido.value    = 'armazenar';
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: $('#frmSos').serialize(),
        success: function (xml) {
            if ($('processo', xml).length > 0) {
                if ($('processo', xml).attr('estado') == 'true') {
                    window.open('sos.php?entidade=atendente&pedido=listar', '_self');
                } else {
                    sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                        atendenteFlx.mensagemXml);
                }
            } else {
                $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                return false;
            }
            
        }
    });
}
    
function atendenteFlx_cancela()
{
    if(confirm('Deseja cancelar a operação?') == 1) {
    	window.open('sos.php?entidade=atendente&pedido=listar', '_self');
    }
}

var atendenteFlx = new AtendenteFlx();