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

function ClienteFlx()
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
    var armazenaAbreNovaOs;
    var atualizaRadio;
    var cancela;
    var consulta;
    var inclui;
    var incluiOs;
    var incia;
    var pesquisa;
    var remove;
    var validaTeclaPesquisa;
    
    this.mensagemXml = null;
    
    this.altera              = clienteFlx_altera;
    this.armazena            = clienteFlx_armazena;
    this.armazenaAbreNovaOs  = clienteFlx_armazenaAbreNovaOs;
    this.atualizaRadio       = clienteFlx_atualizaRadio;
    this.cancela             = clienteFlx_cancela;
    this.consulta            = clienteFlx_consulta;
    this.inclui              = clienteFlx_inclui;
    this.incluiOs            = clienteFlx_incluiOs;
    this.inicia              = clienteFlx_inicia;
    this.pesquisa            = clienteFlx_pesquisa;
    this.remove              = clienteFlx_remove;
    this.validaTeclaPesquisa = clienteFlx_validaTeclaPesquisa;
    
    this.inicia();
}

function clienteFlx_inicia()
{
	$.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=sos&pedido=mensagem&lista=cliente',
        success: function (xml) {
            clienteFlx.mensagemXml = xml;
        }
    });
}

function clienteFlx_pesquisa(estrutura)
{
    clienteFlx_consulta(
        document.frmSos.pesquisa.value,
        document.frmSos.valPesquisa.value,
        estrutura);
    return;
}

function clienteFlx_consulta(arquetipo, valor, estrutura)
{
    sosXmn.consulta('aviso', 'limpar');
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=cliente&pedido=consultar'
            + '&pesquisa='    + arquetipo
            + '&valPesquisa=' + valor,
        success: function (xml) {
            artesaoXmn.carregaArtefato('lista', estrutura, xml);
            
        }
    });
}

function clienteFlx_inclui()
{
    document.frmSos.entidade.value  = "cliente";
    document.frmSos.pedido.value    = "incluir";
    document.frmSos.submit();
}

function clienteFlx_incluiOs(codigo)
{
    window.open('sos.php?entidade=os&pedido=incluirParaNovoCliente'
        + '&cliente=' + codigo, '_self');
}

function clienteFlx_altera(codigo)
{
    document.frmSos.entidade.value      = "cliente";
    document.frmSos.pedido.value        = "alterar";
    document.frmSos.identificador.value = codigo;
    document.frmSos.submit();
}

function clienteFlx_remove(codigo, linha)
{
    sosXmn.consulta('aviso', 'limpar');
    
    if (confirm("Deseja excluir o registro selecionado?")) {
        $.ajax ({
            url: 'sos.php', 
            type: 'POST',
            data: 'entidade=cliente&pedido=remover&identificador=' + codigo,
            success: function (xml) {
                if ($('processo', xml).length > 0) {
                    if ($('processo', xml).attr('estado') == 'true') {
                        $('#listaGradePrincipal tr:eq(' + linha + ')').hide();
                        $('#qtde').text(parseInt($('#qtde').text()) - 1);
                    } else {
                        sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                            clienteFlx.mensagemXml);
                    }
                } else {
                    $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                    return false;
                }
                
            }
        });
    }
}

function clienteFlx_validaTeclaPesquisa(evento){
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

function clienteFlx_armazena()
{
    document.frmSos.entidade.value  = 'cliente';
    document.frmSos.pedido.value    = 'armazenar';
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: $('#frmSos').serialize(),
        success: function (xml) {
            if ($('processo', xml).length > 0) {
                if ($('processo', xml).attr('estado') == 'true') {
                    window.open('sos.php?entidade=cliente&pedido=listar', '_self');
                } else {
                    sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                        clienteFlx.mensagemXml);
                }
            } else {
                $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                return false;
            }
            
        }
    });
}

function clienteFlx_armazenaAbreNovaOs()
{
    document.frmSos.entidade.value  = 'cliente';
    document.frmSos.pedido.value    = 'armazenar';
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: $('#frmSos').serialize(),
        success: function (xml) {
            if ($('processo', xml).length > 0) {
                if ($('processo', xml).attr('estado') == 'true') {
                    var codigo = 0;
                    
                    if (!parseInt(document.frmSos.identificador.value) > 0) {
                        codigo = $('processo', xml).attr('codigo');
                    } else {
                        codigo = document.frmSos.identificador.value;
                    }
                    
                    window.open('sos.php?entidade=os&pedido=incluirParaNovoCliente'
                        + '&cliente=' + codigo, '_self');
                    
                } else {
                    sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                        clienteFlx.mensagemXml);
                }
            } else {
                $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                return false;
            }
        }
    });
}
   
function clienteFlx_cancela()
{
    if(confirm('Deseja cancelar a operação?') == 1) {
        window.open('sos.php?entidade=cliente&pedido=listar', '_self');
    }
}

function clienteFlx_atualizaRadio()
{
    if (document.frmSos.optioncpf[0].checked == true) {
        document.frmSos.cnpj.disabled   = true;
        document.frmSos.cnpj.value      = '';
        document.frmSos.cpf.disabled    = false;
        document.frmSos.rg.disabled     = false;
    } else {
        document.frmSos.cnpj.disabled   = false;
        document.frmSos.cpf.disabled    = true;
        document.frmSos.rg.disabled     = true;
        document.frmSos.cpf.value       = '';
        document.frmSos.rg.value        = '';
    }
}

var clienteFlx = new ClienteFlx();