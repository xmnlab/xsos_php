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

function BarreiraXmn()
{
    /* METODOS*/
    var carrega; 
    
    this.carrega = barreiraXmn_carrega;
}

function barreiraXmn_carrega(pedido, mascara)
{
    var url             = '';
    var visao           = null;
    var parametro       = null;
    var informe         = null;
    var listaInforme    = null;
    
    switch (pedido) {
    case 'autenticar':
        url         = 'xaman.php';
        parametro   =
              'entidade=paje&pedido=entrar'
            + '&usuario=' + mascara.frmBarreira.usuario.value
            + '&senha=' + mascara.frmBarreira.senha.value;
        
        informe = xaman.requisitaInforme(
            url, 'autenticacao', parametro);
        
        return informe;
        
    case 'sair':
        url         = 'xaman.php';
        parametro   =
              'entidade=paje&pedido=sair';
        
        informe = xaman.requisitaInforme(
            url, 'autorizacao', parametro);
        
        return informe;
        
    default:
        visao = mascara.getElementById('area_aviso');
        visao.innerHTML = 'CHAMADO_INVALIDO!';
        return;
    }

    xaman.requisitaMascara(url, visao, parametro);
}

var barreiraXmn = new BarreiraXmn();
