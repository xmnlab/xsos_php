<?php
/**
 * XSOS - Sistema de gestão Ordem de Serviço
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
 * @category   persistencia
 * @package    sos
 * @subpackage sos.persistencia
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

class SosPrst extends PersistenciaXmn
{
    public static function inicia($conexao = 'xsos')
    {
        $prst = simplexml_load_file (
          SosXmn::pegaAldeia('ritual', 'base/persistencia.xmn'));
        
        try {
            ConexaoXmn::conecta($prst, $conexao);
            ConexaoXmn::$formato['saida']   = 'latin-1->utf-8';
            ConexaoXmn::$formato['entrada'] = 'utf-8->latin-1';
        } catch(TratamentoPrstXmn $tratXmn) { 
            throw $tratXmn;
        }
    }
}
