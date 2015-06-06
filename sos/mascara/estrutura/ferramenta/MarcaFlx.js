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
 
function MarcaFlx()
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
    
    this.altera              = marcaFlx_altera;
    this.armazena            = marcaFlx_armazena;
    this.cancela             = marcaFlx_cancela
    this.consulta            = marcaFlx_consulta;
    this.inclui              = marcaFlx_inclui;
    this.inicia              = marcaFlx_inicia;
    this.pesquisa            = marcaFlx_pesquisa;
    this.remove              = marcaFlx_remove;
    this.validaTeclaPesquisa = marcaFlx_validaTeclaPesquisa;
    
    this.inicia();
}

function marcaFlx_altera(codigo)
{
    document.frmSos.entidade.value      = "marca";
    document.frmSos.pedido.value        = "alterar";
    document.frmSos.identificador.value = codigo;
    document.frmSos.submit();
}

function marcaFlx_inclui()
{
    document.frmSos.entidade.value  = "marca";
    document.frmSos.pedido.value    = "incluir";
    document.frmSos.submit();
}

function marcaFlx_inicia()
{
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=sos&pedido=mensagem&lista=marca',
        success: function (xml) {
            marcaFlx.mensagemXml = xml;
        }
    });
}

function marcaFlx_pesquisa(lista)
{
    sosXmn.consulta('aviso', 'limpar');
    
    return marcaFlx_consulta(
        document.frmSos.pesquisa.value,
        document.frmSos.valPesquisa.value,
        lista);
}

function marcaFlx_consulta(arquetipo, valor, estrutura)
{
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=marca&pedido=consultar'
            + '&pesquisa='    + arquetipo
            + '&valPesquisa=' + valor,
        success: function (xml) {
            artesaoXmn.carregaArtefato('lista', estrutura, xml);
            
        }
    });
}

function marcaFlx_remove(codigo, linha)
{
    sosXmn.consulta('aviso', 'limpar');
    
    if (confirm("Deseja excluir o registro selecionado?")) {
        $.ajax ({
            url: 'sos.php', 
            type: 'POST',
            data: 'entidade=marca&pedido=remover&identificador=' + codigo,
            success: function (xml) {
                if ($('processo', xml).length > 0) {
                    if ($('processo', xml).attr('estado') == 'true') {
                        $('#listaGradePrincipal tr:eq(' + linha + ')').hide();
                        $('#qtde').text(parseInt($('#qtde').text()) - 1);
                    } else {
                        sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                            marcaFlx.mensagemXml);
                    }
                } else {
                    $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                    return false;
                }
                
            }
        });
    }
}
function marcaFlx_validaTeclaPesquisa(evento)
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

function marcaFlx_armazena()
{
    document.frmSos.entidade.value  = 'marca';
    document.frmSos.pedido.value    = 'armazenar';
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: $('#frmSos').serialize(),
        success: function (xml) {
            if ($('processo', xml).length > 0) {
                if ($('processo', xml).attr('estado') == 'true') {
                    window.open('sos.php?entidade=marca&pedido=listar', '_self');
                } else {
                    sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                        marcaFlx.mensagemXml);
                }
            } else {
                $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                return false;
            }
            
        }
    });
}

function marcaFlx_cancela()
{
    if(confirm('Deseja cancelar a operação?') == 1) {
    	window.open('sos.php?entidade=marca&pedido=listar', '_self');
    }
}

var marcaFlx = new MarcaFlx(); 