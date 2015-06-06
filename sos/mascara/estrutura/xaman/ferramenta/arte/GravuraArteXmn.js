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

/** CLASSE GravuraArteXmn */
function GravuraArteXmn()
{
	var carregaImagem;
	
	this.carregaImagem = gravuraArteXmn_carregaImagem;
}

/* CARREGA IMAGEM */
function gravuraArteXmn_carregaImagem(requisicao)
{
    var xml;
    var objEntrada;
    var objImagem;
    var objeto;
    
    xml  = xaman.requisitaListaDados(
        requisicao['localizacao'],
        requisicao['parametro']);
    
    if (typeof(xml.getElementsByTagName('imagemXml')[0]) != 'undefined') {
        objImagem = document.createElement('img');
        
        objImagem.src = xml.getElementsByTagName(
            'direcao')[0].firstChild.nodeValue;
        objImagem.title = 
            xml.getElementsByTagName('titulo')[0].firstChild.nodeValue;
            
        requisicao['recipiente'].appendChild(objImagem);
        
        if (requisicao['metodoRemover']) {
            requisicao['recipiente'].appendChild(document.createElement('br'));
            
            
            objeto              = document.createElement('a');
            objeto.href         = "#"
            objeto.onclick      = function() {
                var xml;
                var objeto;
                
                if (confirm("Deseja remover a imagem selecionada?")) {
                    
                    requisicao['recipiente'].innerHTML = '';
                    
                    objeto      = document.createElement('input');
                    objeto.name = requisicao['nomeImagem'];
                    objeto.type = 'file';
                    
                    requisicao['recipiente'].appendChild(objeto);
                }
            };
            objeto.innerHTML    = 'remover';
            requisicao['recipiente'].appendChild(objeto);
        }
        
    } else if (requisicao['nomeImagem'] != null) {
        objEntrada = document.createElement('input');
        
        objEntrada.name = requisicao['nomeImagem'];
        objEntrada.type = 'file';
        
        requisicao['recipiente'].appendChild(objEntrada);
    }
}

artesaoXmn.gravura = new GravuraArteXmn();