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

//include_once $SOS . 'modelo/AtendenteMdl.php';
    
class RelatorioPrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SELECIONA = array();
    private static $INSERE    = array();
    private static $ALTERA    = array();
    private static $REMOVE    = array();
    
    const __BD_TABELA       = 1;
    const __TABELA_CAMPO    = 2;
    const __TABELA_ID       = 4;
    const __TABELA_CP_EX    = 8;
    const __VAZIO           = 16;
    
    public static function inicia() {
        parent::inicia();
        self::preparaPersistencia();
    }
    
    public static function preparaPersistencia()
    {
        self::$SELECIONA[self::__BD_TABELA] = 
            'show tables;';
        
        self::$SELECIONA[self::__TABELA_CAMPO] = 
            'desc ?;';
        
        self::$SELECIONA[self::__VAZIO] = 
            '?';

    }

    public function consulta($tipoPesquisa = self::__BD_TABELA, 
        $listaAtributo = null)
    {
        
        self::inicia();
        switch ($tipoPesquisa) {
            case self::__BD_TABELA:
                $iprst = new IntermediarioPrstXmn(
                    self::$SELECIONA[$tipoPesquisa]
                );
                break;
                
            case self::__TABELA_CAMPO:
                $iprst = new IntermediarioPrstXmn(
                    self::$SELECIONA[$tipoPesquisa]
                );
                $iprst->atribuiParametro(1, $listaAtributo['tabela']);
                break;
                
            case self::__VAZIO:
                $iprst = new IntermediarioPrstXmn(
                    self::$SELECIONA[$tipoPesquisa]
                );
                $iprst->atribuiParametro(1, $listaAtributo['consulta']);
                break;
                
            default:
                $tratXmn = new TratamentoPrstXmn();
                $tratXmn->atribuiArquetipo('FALHA_PESQUISA');
                $tratXmn->atribuiCodigo('PARAMETRO_PESQUISA_INVALIDO');
                throw $tratXmn;
        }
        
        return EscritaXmn::lista2xml($iprst->consulta());
    }
}
