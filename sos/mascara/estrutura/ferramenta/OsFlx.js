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

function OsFlx ()
{
    /*
     * ATRIBUTOS
     */
    var mensagemXml;
    
    /* M�TODOS */
    var altera;
    var armazena;
    var cancela;
    var consulta;
    var exibeDadosCliente;
    var fechaDadosCliente;
    var habilitaValorPesquisa;
    var imprime;
    var inclui;
    var inicia;
    var pesquisa;
    var pesquisaCliente;
    var pesquisaModeloMarca;
    var remove;
    var validaTeclaPesquisa;

    /* REFER�NCIA DOS M�TODOS*/
    this.altera                  = osFlx_altera;
    this.armazena                = osFlx_armazena;
    this.cancela                 = osFlx_cancela;
    this.consulta                = osFlx_consulta;
    this.exibeDadosCliente       = osFlx_exibeDadosCliente;
    this.fechaDadosCliente       = osFlx_fechaDadosCliente;
    this.habilitaValorPesquisa   = osFlx_habilitaValorPesquisa;
    this.imprime                 = osFlx_imprime;
    this.imprime_mini            = osFlx_imprime_mini;
    this.inclui                  = osFlx_inclui;
    this.inicia                  = osFlx_inicia;
    this.pesquisa                = osFlx_pesquisa;
    this.pesquisaCliente         = osFlx_pesquisaCliente;
    this.pesquisaModeloMarca     = osFlx_pesquisaModeloMarca;
    this.remove                  = osFlx_remove;
    this.selecionaEstadoPesquisa = osFlx_selecionaEstadoPesquisa;
    this.validaTeclaPesquisa     = osFlx_validaTeclaPesquisa;
    
    this.inicia('construtor');
}

function osFlx_inicia(
    fluxo, clienteId, atendenteId, estadoOsId, operadoraId, marcaId, modeloId)
{
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=sos&pedido=mensagem&lista=os',
        success: function (xml) {
            osFlx.mensagemXml = xml;
        }
    });
    
    if (fluxo == 'cadastro') {
        document.frmSos.clientePesquisa.focus();

        var objeto          = null;
        var url             = '';
        var parametro        = '';
        var lstAtributo     = null;

        /*
         * LISTA DE CLIENTE
         */
        if (clienteId > 0)
        {
            objeto = document.getElementById('slcCliente');
            url = 'sos.php';
            parametro = 'entidade=cliente&pedido=consultar&pesquisa=codigo&'
                + 'valPesquisa=' 
                + clienteId;
        
            lstAtributo = new Array(
                'codigo', 'nome', 
                clienteId);
    
            artesaoXmn.carregaListaSelecao(url, parametro, objeto, 
                 'cliente', lstAtributo);
        }
        /*
         * LISTA DE ATENDENTE
         */
    
        objeto = document.getElementById('slcAtendente');
        url = 'sos.php';
        parametro = 'entidade=atendente&pedido=consultar&pesquisa=ativos';
        lstAtributo = new Array('codigo', 'nome', atendenteId);
    
        artesaoXmn.carregaListaSelecao(url, parametro, objeto, 'atendente', lstAtributo);

        /*
         * LISTA DE ESTADO OS
         */
        objeto = document.getElementById('slcEstado');
        url = 'sos.php';
        parametro = 'entidade=estado_os&pedido=consultar&pesquisa=ativos';
        lstAtributo = new Array('codigo', 'nome', estadoOsId);
    
        artesaoXmn.carregaListaSelecao(url, parametro, objeto, 'estado', lstAtributo);

        /*
         * LISTA DE OPERADORA
         */
        objeto = document.getElementById('slcOperadora');
        url = 'sos.php'
            parametro = 'entidade=operadora&pedido=consultar&pesquisa=ativos';
        lstAtributo = new Array('codigo', 'nome', operadoraId);
    
        artesaoXmn.carregaListaSelecao(url, parametro, objeto, 'operadora', lstAtributo);

        /*
         * LISTA DE MARCA
         */
        objeto = document.getElementById('slcMarca');
        url = 'sos.php'
            parametro = 'entidade=marca&pedido=consultar&pesquisa=ativos';
        lstAtributo = new Array('codigo', 'nome', marcaId);
    
        artesaoXmn.carregaListaSelecao(url, parametro, objeto, 'marca', lstAtributo);

        /*
         * LISTA DE MODELO
         */
        objeto    = document.getElementById('slcMarca');
        url       = 'sos.php';
        parametro = 'entidade=modelo&pedido=consultar&pesquisa=marca_codigo&'
            + 'valPesquisa=' + objeto.value;

        objeto = document.getElementById('slcModelo');
    
        lstAtributo = new Array('codigo', 'nome', modeloId);
    
        artesaoXmn.carregaListaSelecao(
            url, parametro, objeto, 'modelo', lstAtributo);
    }
}

function osFlx_pesquisa(lista)
{
    this.consulta(
        document.frmSos.pesquisa.value,
        document.frmSos.valPesquisa.value,
        lista);
}

function osFlx_consulta(arquetipo, valor, estrutura)
{
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=os&pedido=consultar'
            + '&pesquisa='    + arquetipo
            + '&valPesquisa=' + valor,
        success: function (xml) {
            artesaoXmn.carregaArtefato('lista', estrutura, xml);
        }
    });
 }

function osFlx_inclui()
{
    document.frmSos.entidade.value="os";
    document.frmSos.pedido.value="incluir";
    document.frmSos.submit();
}

function osFlx_altera(codigo)
{
    document.frmSos.entidade.value      = "os";
    document.frmSos.pedido.value        = "alterar";
    document.frmSos.identificador.value = codigo;
    document.frmSos.submit();
}

function osFlx_remove(codigo, objLinha)
{
    if (confirm("Deseja excluir o registro selecionado?")){
        $.ajax ({
            url: 'sos.php', 
            type: 'POST',
            data: 'entidade=os&pedido=remover&identificador=' + codigo,
            success: function (xml) {
                if ($('processo', xml).length > 0) {
                    if ($('processo', xml).attr('estado') == 'true') {
                        $('#area_aviso').html('&gt;&gt;&gt;&nbsp;REGISTRO REMOVIDO COM SUCESSO');
                        $(objLinha).remove(0);
                    } else {
                        sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                            osFlx.mensagemXml);
                    }
                } else {
                    $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                    return false;
                }
                
            }
        });
    }
}

function osFlx_selecionaEstadoPesquisa()
{
    objPesquisa = document.getElementById('')
    document.frmSos.valPesquisa.value="";
    document.frmSos.valPesquisa.disabled=true;
}

function osFlx_habilitaValorPesquisa()
{
    document.frmSos.valPesquisa.disabled=false;
}

function osFlx_imprime(codigo)
{
    window.open("sos.php?entidade=os&pedido=imprimir&identificador=" +
        codigo,"impressao","tollbars=no,scrollbars=yes");
}

function osFlx_imprime_mini(codigo)
{
    window.open("sos.php?entidade=os&pedido=imprimir-mini&identificador=" +
        codigo,"impressao","tollbars=no,scrollbars=yes");
}

function osFlx_validaTeclaPesquisa(evento){
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

function osFlx_armazena()
{
    document.frmSos.entidade.value  = 'os';
    document.frmSos.pedido.value    = 'armazenar';
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: $('#frmSos').serialize(),
        success: function (xml) {
            if ($('processo', xml).length > 0) {
                if ($('processo', xml).attr('estado') == 'true') {
                    window.open('sos.php?entidade=os&pedido=listar', '_self');
                } else {
                    sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                        osFlx.mensagemXml);
                }
            } else {
                $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                return false;
            }
            
        }
    });
}

function osFlx_pesquisaCliente(){
    var url = "sos.php";
    var parametro = "entidade=cliente&pedido=consultar&pesquisa=" 
        + 'nome&valPesquisa=' + document.frmSos.clientePesquisa.value;
        
    var lista = document.getElementById('slcCliente');
    var atributo = new Array('codigo', 'nome');
    
    artesaoXmn.carregaListaSelecao(url, parametro, lista, 'cliente', atributo);
    
    $('#slcCliente').focus();
    return;
}

function osFlx_pesquisaModeloMarca(){
    var url   = 'sos.php';
    var param = 'entidade=modelo&pedido=consultar&pesquisa=' 
        + 'marca_codigo&valPesquisa=' + document.frmSos.marca.value;
        
    var lista = document.getElementById('slcModelo');
    var atributo = new Array('codigo', 'nome');
    
    artesaoXmn.carregaListaSelecao(url, param, lista, 'modelo', atributo);
    
    return;
}

function osFlx_cancela(){
    if(confirm('Deseja cancelar a operação?') == 1) {
        window.open('sos.php?entidade=os&pedido=listar', '_self');
    }
}

function osFlx_exibeDadosCliente()
{
    if (document.frmSos.slcCliente.value < 1) {
        return;
    }
    var div = document.getElementById("divDadosCliente");
    
    if (div.style.height == "200px") {
        this.fechaDadosCliente();
        return;
    }
    var url = "sos.php?entidade=cliente&pedido=listaDados&id=" 
        + document.frmSos.slcCliente.value;

    xaman.requisitaMascara(url, div);

    div.style.background = '#dadada';
    div.style.border     = '4px #333333 solid';
    div.style.height     = '200px';
    div.style.width      = '300px';
    div.style.position   = 'absolute';
}

function osFlx_fechaDadosCliente(){
    var div = document.getElementById("divDadosCliente");

    div.innerHTML = "";
    div.style.border     = '0px';
    div.style.height     = '0px';
    div.style.width      = '0px';
    div.style.position   = 'absolute';
}

var osFlx = new OsFlx();