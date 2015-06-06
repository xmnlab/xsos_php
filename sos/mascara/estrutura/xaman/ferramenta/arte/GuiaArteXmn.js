/**
 * Xaman - Estrutura de Desenvolvimento
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
 * @package    xaman.mascara
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

function GuiaArteXmn()
{
    /* ATRIBUTO */
    var barraGuia   = null;
    var guia        = null;
    var conteudo    = null;
    var lstGuia     = null;
    var lstInforme  = null;
    var nome        = null;
    
    var entidade    = '';
    var guiaItem    = new Array();
    
    /* METODO */
    var adicionaGuia;
    var carrega;
    var chamaEvento;
    var inicia;
    var ratoPassaSobreGuia;
    var ratoPassaForaGuia;
    var ratoApertaGuia;
    
    this.adicionaGuia       = guiaArteXmn_adicionaGuia;
    this.carrega            = guiaArteXmn_carrega;
    this.chamaEvento        = guiaArteXmn_chamaEvento;
    this.inicia             = guiaArteXmn_inicia;
    this.ratoPassaSobreGuia = guiaArteXmn_ratoPassaSobreGuia;
    this.ratoPassaForaGuia  = guiaArteXmn_ratoPassaForaGuia;
    this.ratoApertaGuia     = guiaArteXmn_ratoApertaGuia;
    
    

}

function guiaArteXmn_inicia(nome, chamada, parametro)
{
	/*
     * INICIACAO DA CLASSE
     */
    this.lstGuia        = new Array();
    
    this.guia           = document.createElement('div');
    this.barraGuia      = document.createElement('div');
    this.conteudo       = document.createElement('div');
    
    
    this.nome           = nome;
    this.guia.id        = 'guia_' + nome;
    this.barraGuia.id   = 'guia_barra_' + nome;
    this.conteudo.id    = 'guia_conteudo_' + nome;
    
    /*
     * CONFIGURAR GUIA
     */
    this.barraGuia.className  = 'guia_barra_titulo';
    
    /*
     * CONFIGURAR CONTEUDO GUIA
     */
    this.conteudo.className     = 'guia_conteudo'
        
    this.guia.appendChild(this.barraGuia);
    this.guia.appendChild(this.conteudo);
    
    /*
     * CARREGA OS DADOS DA GUIA
     */
    this.carrega(chamada, parametro);
    $("#conteudo > div:eq(0)").show();
}

/* ADICIONA GUIA */
function guiaArteXmn_adicionaGuia(lstNovaGuia, fenomeno)
{
    var guiaId;
    var parametroGuia;
    
    for (idItemLstNovaGuia in lstNovaGuia) {
        parametroGuia = lstNovaGuia[idItemLstNovaGuia];
        
        parametroGuia['nome']    = this.guia.id + '_' + parametroGuia['nome'];
        parametroGuia['comando'] = 
              fenomeno 
            + ".chamaEvento('" 
            + parametroGuia['nome'] 
            + "')";
        
        guiaId              = parametroGuia['nome'];
        
        this.lstGuia[this.lstGuia.length] = parametroGuia;
        
        objDivGuia           = document.createElement('div');
        objDivGuia.id        = guiaId;
        objDivGuia.className = 'guia';
        objDivGuia.innerHTML = parametroGuia['descricao'];
        
        objGuia = this;
        
        eval("objDivGuia.onmouseover = "
            + "function() "
            + "{"
            +     "objGuia.ratoPassaSobreGuia('" + guiaId + "');"
            + "};");
        
        eval("objDivGuia.onmouseout = "
                + "function() "
                + "{"
                +     "objGuia.ratoPassaForaGuia('" + guiaId + "');"
                + "};");
        
        eval("objDivGuia.onclick = "
                + "function() "
                + "{"
                +     "objGuia.ratoApertaGuia('" + guiaId + "');"
                + "};");
        
        this.barraGuia.appendChild(objDivGuia);
        
        /*
         * ADICIONA CONTEUDOS DAS ABAS
         */
        objDivConteudo = document.createElement('div');
        
        xaman.requisitaMascara(lstNovaGuia[idItemLstNovaGuia]['chamada'],
            objDivConteudo,
            lstNovaGuia[idItemLstNovaGuia]['parametro']);
            
        this.conteudo.appendChild(objDivConteudo);
    }
}

function guiaArteXmn_chamaEvento()
{
    //METODO ABSTRATO
}

function guiaArteXmn_carrega(chamada, parametro)
{
    var guiaItem   = null;
    var item       = null;
    var lista      = null;
    var objDados   = null;
    var objChamada = null;
    
    if (chamada != '') {
        guiaItem = new Array();
        
        this.informe = xaman.requisitaListaInforme(chamada, parametro);
        
        if (typeof(this.informe.getElementsByTagName('listaXml')[0]) != 'undefined') {
            for (var contLista = 0; 
                contLista < this.informe.getElementsByTagName('listaXml').length; 
                contLista++) {
            
                lista = this.informe.getElementsByTagName('listaXml')[contLista];
                
                this.entidade = 
                    lista.getElementsByTagName('entidade')[0].firstChild.nodeValue;
                    
                for (var contModelo = 0;
                    contModelo < this.informe.getElementsByTagName('modeloXml').length;
                    contModelo++) {
                        
                    objDados = this.informe.getElementsByTagName('modeloXml')[contModelo];
                    
                    guiaItem[contModelo] = new Array();
                    guiaItem[contModelo]['nome'] =
                        objDados.getElementsByTagName(
                                'nome'
                            )[0].firstChild.nodeValue;
                    guiaItem[contModelo]['descricao'] =
                        objDados.getElementsByTagName(
                                'descricao'
                            )[0].firstChild.nodeValue;
                    
                    /*
                     * COLETA DADOS CHAMADA
                     */
                    
                    objChamada = objDados.getElementsByTagName(
                        'chamada')[0];
                    
                    guiaItem[contModelo]['chamada'] =
                        objChamada.getElementsByTagName('pagina')[0].firstChild.nodeValue;
                    
                    guiaItem[contModelo]['parametro'] = 
                          "entidade="    + objChamada.getElementsByTagName(
                            'entidade')[0].firstChild.nodeValue
                        + "&pedido="      + objChamada.getElementsByTagName(
                            'pedido')[0].firstChild.nodeValue
                        + "&arquetipo="   + objChamada.getElementsByTagName(
                            'arquetipo')[0].firstChild.nodeValue
                        + "&guia="        + objChamada.getElementsByTagName(
                            'guia')[0].firstChild.nodeValue;
                }
            }
            
            /*
             * ADICIONA GUIA
             */
            this.adicionaGuia(guiaItem, this.entidade);
        }
    }
}

function guiaArteXmn_ratoPassaSobreGuia (guiaId)
{
    guia = document.getElementById(guiaId);
    guia.className = 'guia_foco';
}

function guiaArteXmn_ratoPassaForaGuia(guiaId)
{
    guia = document.getElementById(guiaId);
    guia.className = 'guia';
}

function guiaArteXmn_ratoApertaGuia(guiaId)
{
    var comando = '';
    var guia    = null;
    var objGuia = null;
    var chamada = null;
    var param   = null;
    var ordem   = null;
    var cont    = null;
    
    ordem = 0;
    cont  = 0;
    for (guiaLstId in this.lstGuia) {
        var guiaItem;
        
        if (document.getElementById(this.lstGuia[guiaLstId]['nome']) == null) {
            return false;
        }
        
        guiaItem = document.getElementById(this.lstGuia[guiaLstId]['nome']);
        
        guiaItem.className = 'guia';
        
        eval("guiaItem.onmouseout = "
            + "function() "
            + "{"
            +     this.entidade + ".ratoPassaForaGuia('" + guiaItem.id + "');"
            + "};");
        
        if (this.lstGuia[guiaLstId]['nome'] == guiaId) {
            ordem = cont;
        }
        
        cont++;
    }
    
    guia                = document.getElementById(guiaId);
    guia.onmouseout     = '';
    guia.className      = 'guia_foco';
    
    $("#" + this.conteudo.id + " > div").hide();
    $("#" + this.conteudo.id + " > div:eq(" + ordem + ")").fadeIn();
}

artesaoXmn.janela.guia = new GuiaArteXmn();