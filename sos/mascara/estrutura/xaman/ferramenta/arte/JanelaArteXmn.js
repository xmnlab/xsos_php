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
 * @package    xaman.mascara.arte
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

/** CLASSE JanelaArteXmn */
function JanelaArteXmn()
{
	var centralizaMascara;
	var criaJanelaFlutuante;
    var criaVisualizadorImagem;
	var redimensionaJanelaFlutuante;
	
	this.centralizaMascara      = janelaArteXmn_centralizaMascara;
    this.criaVisualizadorImagem = janelaArteXmn_criaVisualizadorImagem;
	this.criaJanelaFlutuante    = janelaArteXmn_criaJanelaFlutuante;
    this.redimensionaJanelaFlutuante = janelaArteXmn_redimensionaJanelaFlutuante;
}

/* CENTRALIZA MASCARA */
function janelaArteXmn_centralizaMascara(mascara, recipiente)
{ 
    var mascaraAltura;
    var mascaraLargura;
    var recipienteAltura;
    var recipienteLargura;
    
    if (parseInt(mascara.style.width) > 0) {
        mascaraLargura = parseInt(mascara.style.width);
        mascaraAltura  = parseInt(mascara.style.height);
    } else {
        mascaraLargura = parseInt(mascara.offsetWidth);
        mascaraAltura  = parseInt(mascara.offsetHeight);
    }
    
    if (parseInt(recipiente.style.width) > 0) {
        recipienteLargura = parseInt(recipiente.style.width);
        recipienteAltura  = parseInt(recipiente.style.height);
        alert(parseInt(recipiente.style.width));
    } else {
        recipienteLargura = parseInt(recipiente.offsetWidth);
        recipienteAltura  = parseInt(recipiente.offsetHeight);
        
    }
    
    mascara.style.left = (recipienteLargura - mascaraLargura) / 2;
    mascara.style.top  = (recipienteAltura  - mascaraAltura) / 2;
}

/* CRIA JANELA FLUTUANTE */
function janelaArteXmn_criaJanelaFlutuante(recipiente, lstParametro)
{
    var objAncora;
    var objDivConteudo;
    var objDivControleJanela;
    var objDivFundo;
    var objDivJanela;
    var objDivRodape;
    var objDivTitulo;
    var objImagemControle;
    
    /*
     * CONFIGURANDO TAMANHOS PADR�ES
     */
    
    if (lstParametro['larguraMinima'] == null) {
        lstParametro['larguraMinima'] = 0;
    }
    
    if (lstParametro['alturaMinima'] == null) {
        lstParametro['alturaMinima'] = 0;
    }
    
    if (lstParametro['alturaMinima'] < 100) {
        lstParametro['alturaMinima'] = 100;
    }
    
    if (lstParametro['larguraMinima'] < 200) {
        lstParametro['larguraMinima'] = 200;
    }
    
    if (lstParametro['alturaMaxima'] < lstParametro['alturaMinima']) {
        lstParametro['alturaMaxima'] = 
            this.lstFerramentas['JANELA_FLUTUANTE_ALTURA_MAX'];
    }
    
    if (lstParametro['larguraMaxima'] < lstParametro['larguraMinima']) {
        lstParametro['larguraMaxima'] = 
            this.lstFerramentas['JANELA_FLUTUANTE_LARGURA_MAX'];
    }
    
    objDivControleJanela    = document.createElement('div');
    objDivConteudo          = document.createElement('div');
    objDivJanela            = document.createElement('div');
    objDivRodape            = document.createElement('div');
    objDivTitulo            = document.createElement('div');
    
    objDivConteudo.id       = lstParametro['id'] + 'Conteudo';
    objDivControleJanela.id = lstParametro['id'] + 'CtrlJanela';
    objDivJanela.id         = lstParametro['id'];
    objDivRodape.id         = lstParametro['id'] + 'Rodape';
    objDivTitulo.id         = lstParametro['id'] + 'Titulo';
    
    this.redimensionaJanelaFlutuante(objDivConteudo, lstParametro);
    
    objDivTitulo.style.width    = '70%';
    objDivTitulo.style.height   = 20;
    objDivTitulo.className      = 'subTitulo';
    objDivTitulo.style.cssFloat = 'left';
    
    objDivControleJanela.style.width    = '30%';
    objDivControleJanela.style.height   = 20;
    objDivControleJanela.style.cssFloat = 'left';
    
    
    objDivRodape.style.width    = '100%';
    objDivRodape.style.height   = 20;
    objDivRodape.className      = 'subTitulo';
    
    objDivJanela.style.width = parseInt(objDivConteudo.style.width);
    objDivJanela.style.height = parseInt(objDivConteudo.style.height)
        + parseInt(objDivTitulo.style.height)
        + parseInt(objDivRodape.style.height);
        
    if (lstParametro['titulo'] == null) {
        lstParametro['titulo'] = 'Artes&atilde;o Xaman';
    }
    
    objDivTitulo.innerHTML = lstParametro['titulo'];
    
    objDivJanela.style.display    = 'block';
    objDivJanela.style.position   = 'absolute';
    objDivJanela.style.border     = '#ffffff 5px solid';
    objDivJanela.style.background = '#ffffff';
    
    objDivConteudo.style.display    = 'block';
    objDivConteudo.style.border     = '#ffffff 0px solid';
    objDivConteudo.style.background = '#ffffff';
    objDivConteudo.style.overflow   = 'auto';
    objDivConteudo.style.width      = '100%';
    
    /* CRIA CAMADA DE FUNDO TRANSPARENTE */
    if (lstParametro['fundo'] != null) {
        objDivFundo                  = document.createElement('div');
        objDivFundo.id               = lstParametro['fundo']['id'];
        objDivFundo.style.top        = 0;
        objDivFundo.style.left       = 0;
        objDivFundo.style.width      = "100%";
        objDivFundo.style.height     = "100%";
        objDivFundo.style.display    = 'block';
        objDivFundo.style.position   = 'absolute';
        objDivFundo.style.border     = '#ffffff 0px solid';
        objDivFundo.style.background = lstParametro['fundo']['cor'];
        objDivFundo.style.opacity    = lstParametro['fundo']['opacidade'];
        objDivFundo.style.overflow   = 'auto';
        
        document.getElementById('area_conteudo').appendChild(objDivFundo);
        
    } else {
        objDivJanela.onclick = function() {
            eval("document.getElementById('"
                + recipiente.id + "').removeChild(document.getElementById('" 
                + lstParametro['id'] + "'))");
        }
    }
    
    /* MONTA AS PARTES DA JANELA */
    objDivJanela.appendChild(objDivTitulo);
    objDivJanela.appendChild(objDivControleJanela);
    objDivJanela.appendChild(objDivConteudo);
    objDivJanela.appendChild(objDivRodape);
    
    /*CENTRALIZA JANELA FLUTUANTE */
    this.centralizaMascara(objDivJanela, recipiente);
    
    /* CRIA��O DOS OBJETOS DE CONTROLE DA JANELA FLUTUANTE */
    /* CONTROLE FECHAR */
    objImagemControle                 = document.createElement('img');
    objImagemControle.src             = 'estrutura/xaman/simbolo/fecha.png';
    objImagemControle.border          = '#ffffff 0px solid';
    objImagemControle.hspace          = 3;
    objImagemControle.style.cssFloat  = 'right';
    
    /* ANCORA PARA BOTAO FECHAR */
    objAncora = document.createElement('a');
    
    objAncora.href = '#';
    objAncora.onclick = function() {
        try {
            eval("document.getElementById('"
                + recipiente.id + "').removeChild(document.getElementById('" 
                + lstParametro['fundo']['id'] + "'))");
                
            eval("document.getElementById('"
                + recipiente.id + "').removeChild(document.getElementById('" 
                + lstParametro['id'] + "'))");
        } catch (tratXmn) {
            return;
        }
    };
    
    objAncora.appendChild(objImagemControle);
    
    objDivControleJanela.appendChild(objAncora);
    
    /* CONTROLE REDIMENSIONAR */
    objImagemControle        = document.createElement('img');
    objImagemControle.src    = 'estrutura/xaman/simbolo/redimensiona.png';
    objImagemControle.border = '#ffffff 0px solid';
    objImagemControle.hspace = 3;
    objImagemControle.style.cssFloat  = 'right'
    
    objAncora = document.createElement('a');
    
    objAncora.href = '#';
    objAncora.onclick = function() {
        var objDivConteudo;
        var objDivJanela;
        var objImagem;
        
        try {
            objDivConteudo = document.getElementById(lstParametro['id']
                 + 'Conteudo');
            objDivJanela = document.getElementById(lstParametro['id']);
            objImagem = document.getElementById(lstParametro['id']
                 + 'Imagem');
            
            if (lstParametro['altura'] == 0) {
                lstParametro['altura'] = objImagem.height;
            }
            
            if (lstParametro['largura'] == 0) {
                lstParametro['largura'] = objImagem.width;
            }
            
            janelaArteXmn.redimensionaJanelaFlutuante(objDivConteudo, lstParametro);
            
            objDivJanela.style.width = parseInt(objDivConteudo.style.width);
            objDivJanela.style.height = parseInt(objDivConteudo.style.height)
                + parseInt(objDivTitulo.style.height)
                + parseInt(objDivRodape.style.height);
                
            janelaArteXmn.centralizaMascara(objDivJanela, recipiente);
            
        } catch (tratXmn) {
            return;
        }
    };
    objAncora.appendChild(objImagemControle);
    
    objDivControleJanela.appendChild(objAncora);

    recipiente.appendChild(objDivJanela);
}

function janelaArteXmn_criaVisualizadorImagem(recipiente, url, lstParametro)
{
    var objDiv;
    var xml;

    objImagem = document.createElement('img');
    
    xml = xaman.requisitaDados(url);
    
    if (typeof(xml.getElementsByTagName('imagemXml')[0]) != 'undefined') {
        
        
        objImagem.src = xml.getElementsByTagName(
            'direcao')[0].firstChild.nodeValue.replace(/[|]/g, '&');
        objImagem.title = 
            xml.getElementsByTagName('titulo')[0].firstChild.nodeValue;
            
        lstParametro['titulo'] = xml.getElementsByTagName(
            'titulo')[0].firstChild.nodeValue;

    } else {
        objImagem = document.createElement('img');
        objImagem.src = 'estrutura/modelo/chefe/imagem/imagem_nao_localizada.png';
        
    }
    
    objImagem.id = lstParametro['id'] + 'Imagem';
    
    if (document.getElementById(lstParametro['id']) != null) {
        document.getElementById('divConteudo').removeChild(
            document.getElementById(lstParametro['id']));
    }
    
    for (var contador = 0; parseInt(objImagem.width) > 0; contador++){
        if (contador > 5000) {
            break;
        }
    }

    lstParametro['largura']         = objImagem.width;
    lstParametro['altura']          = objImagem.height;
    
    artesaoXmn.criaJanelaFlutuante(recipiente, lstParametro);
    
    /* INSTANCIANDO O CONTEUDO DA JANELA */
    objImagem.align     = 'middle';
    
    document.getElementById(lstParametro['id'] 
        + 'Conteudo').appendChild(objImagem);
}

function janelaArteXmn_redimensionaJanelaFlutuante(objJanela, lstParametro)
{
    if (lstParametro['largura'] > lstParametro['larguraMaxima']) {
        objJanela.style.width = lstParametro['larguraMaxima'];
        
    } else if (lstParametro['largura'] < lstParametro['larguraMinima']) {
        objJanela.style.width = lstParametro['larguraMinima'];
        
    } else {
        objJanela.style.width = lstParametro['largura'];
    }
    
    if (lstParametro['altura'] > lstParametro['alturaMaxima']) {
        objJanela.style.height = lstParametro['alturaMaxima'];
        
    } else if (lstParametro['altura'] < lstParametro['alturaMinima']) {
        objJanela.style.height = lstParametro['alturaMinima'];
        
    } else {
        objJanela.style.height = lstParametro['altura'];
    }

}

artesaoXmn.janela = new JanelaArteXmn(); 

/* CONSTANTES ARTESAOXMN */
artesaoXmn.criaFerramenta('LARGURA_MAX_JANELA_FLUTUANTE', 400);
artesaoXmn.criaFerramenta('LARGURA_MIN_JANELA_FLUTUANTE', 300);