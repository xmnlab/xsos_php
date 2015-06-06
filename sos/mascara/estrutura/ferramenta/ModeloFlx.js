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

function ModeloFlx()
{
    /*
     * ATRIBUTO
     */
    var mensagemXml;
    
    /*
     * METODO
     */
    var altera;
    var armazena;
    var cancela;
    var carregaMarca;
    var consulta;
    var inclui;
    var inicia;
    var pesquisa;
    var remove;
    var removeFoto;
    var validaTeclaPesquisa;
    var visualiza;
    
    this.altera                 = modeloFlx_altera;
    this.armazena               = modeloFlx_armazena;
    this.cancela                = modeloFlx_cancela;
    this.carregaMarca           = modeloFlx_carregaMarca;
    this.consulta               = modeloFlx_consulta;
    this.inclui                 = modeloFlx_inclui;
    this.inicia                 = modeloFlx_inicia;
    this.pesquisa               = modeloFlx_pesquisa;
    this.remove                 = modeloFlx_remove;
    this.removeFoto             = modeloFlx_removeFoto;
    this.validaTeclaPesquisa    = modeloFlx_validaTeclaPesquisa;
    this.visualiza              = modeloFlx_visualiza;
}

function modeloFlx_inicia()
{
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=sos&pedido=mensagem&lista=modelo',
        success: function (xml) {
            modeloFlx.mensagemXml = xml;
        }
    });
}

function modeloFlx_pesquisa(lista)
{
    return modeloFlx_consulta(
        document.frmSos.pesquisa.value,
        document.frmSos.valPesquisa.value,
        lista);
}

function modeloFlx_consulta(arquetipo, valor, estrutura)
{
    sosXmn.consulta('aviso', 'limpar');
    
    $.ajax ({
        url: 'sos.php', 
        type: 'POST',
        data: 'entidade=modelo&pedido=consultar'
            + '&pesquisa='    + arquetipo
            + '&valPesquisa=' + valor,
        success: function (xml) {
            artesaoXmn.carregaArtefato('lista', estrutura, xml);
            
        }
    });
}

function modeloFlx_inclui()
{
    document.frmSos.entidade.value  = 'modelo';
    document.frmSos.pedido.value    = 'incluir';
    document.frmSos.submit();
}

function modeloFlx_altera(codigo)
{
    document.frmSos.entidade.value      = 'modelo';
    document.frmSos.pedido.value        = 'alterar';
    document.frmSos.identificador.value = codigo;
    document.frmSos.submit();
}

function modeloFlx_remove(codigo, linha)
{
    sosXmn.consulta('aviso', 'limpar');
    
    if (confirm("Deseja excluir o registro selecionado?")) {
        $.ajax ({
            url: 'sos.php', 
            type: 'POST',
            data: 'entidade=modelo&pedido=remover&identificador=' + codigo,
            success: function (xml) {
                if ($('processo', xml).length > 0) {
                    if ($('processo', xml).attr('estado') == 'true') {
                        $('#listaGradePrincipal tr:eq(' + linha + ')').hide();
                        $('#qtde').text(parseInt($('#qtde').text()) - 1);
                    } else {
                        sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                            modeloFlx.mensagemXml);
                    }
                } else {
                    $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
                    return false;
                }
                
            }
        });
    }
}


function modeloFlx_removeFoto(codigo)
{
    return xaman.requisitaDados('sos.php?entidade=modelo&pedido=removerFoto&'
        + 'identificador=' + codigo); 
}
 
function modeloFlx_validaTeclaPesquisa(evento)
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

function modeloFlx_armazena(xml)
{
    if ($('processo', xml).length > 0) {
        if ($('processo', xml).attr('estado') == 'true') {
            window.open('sos.php?entidade=modelo&pedido=listar', '_self');
        } else {
            sosXmn.mensageiro($('processo', xml).attr('mensagem'), 
                modeloFlx.mensagemXml);
        }
    } else {
        $('#area_aviso').html('&gt;&gt;&gt;&nbsp;FALHA NO PROCESSO DA SOLICITACAO.');
        return false;
    }
}

function modeloFlx_cancela(){
    if(confirm('Deseja cancelar a operação?') == 1) {
    	window.open('sos.php?entidade=modelo&pedido=listar', '_self');
    }
}

function modeloFlx_visualiza(codigo)
{
    var lstParametro;
    var url;

    url = "sos.php?entidade=modelo&pedido=verificarFoto"
        + "&identificador=" + codigo;
        
        
    lstParametro = new Array();
    
    lstParametro['id']              = 'divJanelaModelo';
    lstParametro['larguraMaxima']   = 500;
    lstParametro['alturaMaxima']    = 400;
    
    lstParametro['fundo']   = new Array(); 
    
    lstParametro['fundo']['id'] = 'divFundoJanelaModelo';
    lstParametro['fundo']['cor'] = '#111111';
    lstParametro['fundo']['opacidade'] = 0.8;
    
    artesaoXmn.gravura.criaVisualizadorImagem(
        document.getElementById('area_conteudo'), url, lstParametro);
}

function modeloFlx_carregaMarca(modeloId, marcaId)
{
    var lstRequisicao = new Array();
    
    lstRequisicao['localizacao']   = 'sos.php?';
    lstRequisicao['parametro']     = 'entidade=modelo&pedido=verificarFoto&'
        + 'identificador=' + modeloId;
    lstRequisicao['recipiente']    = document.getElementById('divFoto');
    lstRequisicao['nomeImagem']    = 'imagem';
    lstRequisicao['metodoRemover'] = 'modeloFlx.removeFoto(' + modeloId + ')';
    
    artesaoXmn.carregaListaSelecao(
        'sos.php',
        'entidade=marca&pedido=consultar&pesquisa=nome&valPesquisa=',
        document.getElementById('slcMarca'),
        'marca',
        new Array('codigo', 'nome', marcaId));

    artesaoXmn.gravura.carregaImagem(lstRequisicao);
    document.frmSos.nome.focus();
}

var modeloFlx = new ModeloFlx();