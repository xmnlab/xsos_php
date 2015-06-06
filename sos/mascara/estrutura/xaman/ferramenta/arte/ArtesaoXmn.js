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

/** CLASSE ArtesaoXmn */
function ArtesaoXmn()
{
    /* ATRIBUTO */
    var lstFerramenta
    
    /* METODO */
    var carregaArtefato;
    var carregaListaSelecao;
    var criaArtefato;
    var criaFerramenta;
    var criaListaFerramenta;

    
    this.lstFerramenta = new Array();
    
    this.carregaArtefato        = artesaoXmn_carregaArtefato;
    this.carregaListaSelecao    = artesaoXmn_carregaListaSelecao;
    this.criaArtefato           = artesaoXmn_criaArtefato;
    this.criaFerramenta         = artesaoXmn_criaFerramenta;
    this.criaListaFerramenta    = artesaoXmn_criaListaFerramenta;

}

function artesaoXmn_carregaArtefato(arquetipo, estrutura, dados)
{
    switch (arquetipo) {
    case 'lista':
        var corLinha;
        var texto;
            
        var objLinha;
        var objRodapeListaGrade;
        var objCelula;
        var objDados;
            
        var objAncora;
        var objImagem;
        var objeto;
            
        var nomeLista;
        var estado;
        var contadorLinha;
        var indiceCelula;
        
        var chave;
        
        contadorLinha = 0;
        
        estado = false;
        
        while (objTabela.rows.length > 1) {
            objTabela.deleteRow(1);
        }
        
        corLinha  = 2;
        nomeLista = objTabela.id;
        
        nomeLista = nomeLista.substr(10, 1).toLowerCase() 
          + nomeLista.substr(11, nomeLista.length - 1);
        
        chave = $(estrutura).attr('chave');
        
        texto     = '';
        $('lista_campo_calc > campo', estrutura).each(function () {
            texto += 'var ' + $(this).attr('nome') + '=0;';
        });
        
        eval(texto);
        texto = '';

        $('modeloXml', dados).each(function () {
            estado = true;

            objDados = $(this);
            
            objLinha  = objTabela.insertRow(++contadorLinha);
            
            if (corLinha == 2) {
                corLinha = 1;
            } else { 
                corLinha = 2;
            }
        
            objLinha.className = 'linha' + corLinha;
            
            objLinha.onmouseover = function() {
                    this.className='linhaSelecionada';
            };
            
            eval("objLinha.onmouseout  = function(){"
                   + "this.className = 'linha" + corLinha + "';"
                + "}"
            );
                
            indiceCelula = 0;
            
            /* ATRIBUTOS DA PESQUISA */
            $('lista_campo > campo', estrutura).each(function () {
                
                objCelula = objLinha.insertCell(objLinha.cells.length);
                
                objCelula.className  = 'itemConteudoGrade';

                objCelula.innerHTML  = $($(this).attr('nome'), objDados).text();
                texto = '';
            });
            
            /*
             * INICIA VALORES CALCULADOS
             */
            texto = new Array();
            $('lista_campo_calc > campo', estrutura).each(function () {
                texto.push($(this).attr('calc'));
            });
        
            for (indice in texto) {
                eval(texto[indice]);
            }
            
            texto = null;
            
            $('lista_ferramenta', estrutura).each(function () {
                objCelula = objLinha.insertCell(objLinha.cells.length);
                
                objCelula.className  = 'itemConteudoGrade';
                
                $(objCelula).css('width', $(this).attr('largura'));
                $(objCelula).css('text-align', 'center');
                
                $('ferramenta', $(this)).each(function () {
                    
                    $(objCelula).html($(objCelula).html() 
                        + $($(this).attr('nome'), objDados).text());
                    
                    objAncora = document.createElement('a');
                     
                    texto =  $(this).attr('enlace');
                    
                    texto = texto.replace('$i', objTabela.rows.length - 1);
                    
                    texto = texto.replace('?', $(chave, objDados).text());
                    
                    objAncora.href = 'javascript:' + texto + ';';
                    /* RECUPERA A FERRAMENTA DA LISTA */
                    objeto = artesaoXmn.lstFerramenta[$(this).attr('nome')];
                    
                    /* CRIA UMA NOVA FERRAMENTA 
                     * COM OS MESMOS ATRIBUTOS DA 
                     * FERRAMENTA DA LISTA
                     */
                    objImagem = document.createElement('img');
                    
                    objImagem.border    = objeto.border;
                    objImagem.hspace    = objeto.hspace;
                    objImagem.src       = objeto.src;
                    objImagem.titulo    = objeto.titulo;
                    
                    objAncora.appendChild(objImagem);
                    
                    objCelula.appendChild(objAncora);
                    
                    objeto      = null;
                    objAncora   = null;
                    texto       = '';
                });
                
                objCelula   = null;
            });
        }); 
        
        if (estado == false) {
            /* FLUXO ACIONADO QUANDO N�O FOR ENCONTRADO NENHUM REGISTRO */
            tmp       = $('tr > td', objTabela).length;
            objLinha  = objTabela.insertRow(1);
            objLinha.className = 'linha1';
            
            objCelula = objLinha.insertCell(0);
            objCelula.colSpan   = tmp;
            objCelula.className = 'itemConteudoGrade';
            objCelula.innerHTML = 'Nenhuma informa&ccedil;&atilde;o encontrada';
            
        } else {
            if ($('lista_campo_calc > campo', estrutura).html != '') {
                objRodapeListaGrade = objTabela.createTFoot();
                
                contadorLinha = 0;
                tmp           = objLinha.cells.length;
                $('lista_campo_calc > campo', estrutura).each(function () {
                    
                    objLinha = 
                        objRodapeListaGrade.insertRow(contadorLinha++);
                    objLinha.className  = 'linhaRodapeGrade';
                    
                    objCelula = objLinha.insertCell(0);
                    
                    objCelula.colSpan = tmp;
                    
                    texto = eval($(this).attr('nome'));
                    
                    if ($(this).attr('formato') != '') {
                        texto = $.format($(this).attr('formato'), texto); 
                    } 
                    objCelula.innerHTML = '<b>' + $(this).attr('titulo') 
                        + ': </b><span id="' 
                        + $(this).attr('nome') + '">' + texto + '</span>';
                    
                });
            }
        }
        
        break;
    }
}


/* PREPARA LISTA SELECAO */
function artesaoXmn_carregaListaSelecao(
    url, parametro, objLista, nomeLista, lstAtributo)
{
    /*
     * lstAtributo[0] = value;
     * lstAtributo[1] = text;
     * lstAtributo[2] = valorPadrao
     */
   
    var xml  = xaman.requisitaListaDados(url, parametro);
    
    while(objLista.length > 0){
        objLista.remove(0);
    }

    if (typeof(xml.getElementsByTagName('modeloXml')[0]) != 'undefined') {
    
        var item     = null;
        var objDados = null;
        
        for (var contador = 0; 
            contador < xml.getElementsByTagName('modeloXml').length; contador++) {
        
            objDados = xml.getElementsByTagName('modeloXml')[contador];
            
            item       = document.createElement('option');
            item.text  = objDados.getElementsByTagName(
                    lstAtributo[1]
                )[0].firstChild.nodeValue;
            
            item.value = objDados.getElementsByTagName(
                    lstAtributo[0]
                )[0].firstChild.nodeValue;

            try {
                objLista.add( item, null);
            } catch (tratXmn) {
                objLista.add(item);
            }
            
            if (item.value == lstAtributo[2]) {
                objLista.selectedIndex = contador;
            }
        }
        
    } else {
        item     = document.createElement('option');
        item.text = 'Nenhum registro encontrado';
        item.value = 0;
        
        try {
            objLista.add( item, null);
        } catch (tratXm) {
            objLista.add(item);
        }
    }
    
    return;
}

/* CRIA FERRAMENTA */
function artesaoXmn_criaFerramenta(nomeFerramenta, conteudo)
{
    this.lstFerramenta[nomeFerramenta] = conteudo;
}

function artesaoXmn_criaListaFerramenta(xml)
{
    var lista;
    var ferramentaItem  = new Array();
    var ferramenta;
    
    if (typeof(xml.getElementsByTagName('lista_ferramenta')[0]) != 'undefined') {
        for (var contLista = 0; 
            contLista < xml.getElementsByTagName('lista_ferramenta').length; 
            contLista++) {
        
            lista = xml.getElementsByTagName('lista_ferramenta')[contLista];
            
            for (var contModelo = 0;
                contModelo < xml.getElementsByTagName('ferramenta').length;
                contModelo++) {
                    
                objDados = xml.getElementsByTagName('ferramenta')[contModelo];
                
                ferramentaItem[contModelo] = new Array();
                
                ferramenta          = document.createElement('img');
                ferramenta.src      = objDados.getAttribute('gravura');
                ferramenta.border   = objDados.getAttribute('borda');
                ferramenta.hspace   = objDados.getAttribute('espaco');
                ferramenta.title    = objDados.getAttribute('titulo');
                
                this.criaFerramenta(objDados.getAttribute('titulo'), 
                    ferramenta);
            }
        }
        return true;
    }
}

function artesaoXmn_criaArtefato(arquetipo, artefato)
{
    switch (arquetipo) {
    case 'lista':
        var tabela;
        var linha;
        var celula;
        var contador;
        var texto;
        var objTopoListaGrade;
        
        nomeLista = artefato.attr('nome');
        
        nomeLista = nomeLista.substr(0, 1).toUpperCase() 
            + nomeLista.substr(1, nomeLista.length - 1);
            
        objTabela           = document.createElement('table');
        objTabela.id        = 'listaGrade' + nomeLista;
        objTabela.className = 'tabelaGrade';
        
        $(objTabela).css('table-layout', 'fixed');
        
        objTopoListaGrade = objTabela.createTHead();
        
        objLinha = objTopoListaGrade.insertRow(0);
        objLinha.className = 'linhaTituloGrade';

        contador = 0;
        $('lista_campo campo', artefato).each(function () {
            objCelula           = objLinha.insertCell(contador);

            $(objCelula).css('width', $(this).attr('largura'));
            objCelula.className = 'itemTituloGrade';
            objCelula.innerHTML = $(this).attr('titulo');
            
            contador++;
        });
        
        if (artefato.attr('ferramenta') == 'true') {
            objCelula = objLinha.insertCell(contador);
            objCelula.className = 'itemTituloGradeFerramenta';
            objCelula.align     = 'center';
            objCelula.innerHTML = '&nbsp;&nbsp;FERRAMENTAS&nbsp;&nbsp;';
        }
        
        $('#' + artefato.attr('recipiente')).append(objTabela);
        $('#' + artefato.attr('recipiente')).css('width', 
            (parseInt($(objTabela).css('width')) + 20) + 'px');
        break;
    }
}
 
var artesaoXmn = new ArtesaoXmn();