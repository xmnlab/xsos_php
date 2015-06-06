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
 * @category   xaman
 * @package    xaman
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */
function Anexo(id,nome,item){ 
	this.x = 0; 
	this.id = id; 
	this.item = item; 
	this.nome = nome; 
	this.remover = _Anexo_remover; 
	this.addAnexoArquivo = _Anexo_addAnexoArquivo; 
	this.getElementos = _Anexo_getElementos; 
} 
function _Anexo_getElementos(){ 
	return this.x; 
} 
function _Anexo_remover(x){ 
	anexo = document.getElementById("item"+x); 
	arquivos = document.getElementById(this.id); 
	arquivos.removeChild(anexo); 
} 
function _Anexo_addAnexoArquivo(){ 
	remover = document.createTextNode("remover"); 

	anexo = document.createElement("input"); 
	anexo.setAttribute("type","file"); 
	anexo.setAttribute("id",this.id+this.x); 
	anexo.setAttribute("name",this.item+this.x); 

	var br = document.createElement("br"); 
 
	var link = document.createElement("a"); 
	link.setAttribute("href","javascript: "+this.nome+".remover("+this.x+")"); 
	link.appendChild(remover); 

	var item = document.createElement("div"); 

	item.setAttribute("id","item"+this.x); 
	item.appendChild(anexo); 
	item.appendChild(link); 
	item.appendChild(br); 

	var arquivos = document.getElementById(this.id); 
	arquivos.appendChild(item); 

	this.x++; 
}
