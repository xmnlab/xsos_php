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

function MenuFlx()
{
    var retornaMenu;
    
    this.retornaMenu         = menuFlx_retornaMenu;
} 

function menuFlx_retornaMenu()
{
    var listaMenu    = new Array();
    var lista;
    var menuItem     = new Array();
    var menu         = new Array();
    var grupos       = '';
    
    listaMenu = xaman.requisitaListaDados('sos.php', 
        'entidade=menu&pedido=listar_menu');
    
    if (typeof(listaMenu.getElementsByTagName('lista_menu')[0]) != 'undefined') {
        for (var contLista = 0; 
            contLista < listaMenu.getElementsByTagName('lista_menu').length; 
            contLista++) {
        
            lista = listaMenu.getElementsByTagName('lista_menu')[contLista];
            
            for (var contModelo = 0;
                contModelo < listaMenu.getElementsByTagName('menu').length;
                contModelo++) {
                    
                objDados = listaMenu.getElementsByTagName('menu')[contModelo];
                
                menuItem[contModelo] = new Array();

                grupos = objDados.getAttribute('grupos');
                if (grupos.match(sosXmn.usuario['grupo']) == null)
                {
                    continue;
                }
                
                menuItem[contModelo]['nome'] =
                    objDados.getAttribute('nome');
                
                menuItem[contModelo]['descricao'] = 
                    objDados.getAttribute('descricao');
                
                menuItem[contModelo]['chamada'] = 
                    objDados.getAttribute('chamada');
                
                menu.push(menuItem);
                
            }
        }
        return menu;
    }
}

var menuFlx = new MenuFlx();