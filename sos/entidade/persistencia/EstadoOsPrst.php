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

include_once $SOS . 'modelo/EstadoMdl.php';
include_once $SOS . 'modelo/EstadoLstMdl.php';
    
class EstadoOsPrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SQL_SELECT = array();
    private static $SQL_INSERT = array();
    private static $SQL_UPDATE = array();
    private static $SQL_DELETE = array();
    
    const PESQUISA_ESTADO_CODIGO                   = 1;
    
    const PESQUISA_ESTADOS                         = 100;
    const PESQUISA_ESTADOS_ATIVOS                  = 101;
    const PESQUISA_ESTADOS_NOME                    = 102;
    const PESQUISA_ESTADOS_NOME_APROXIMADO         = 103;
    const PESQUISA_ESTADOS_NOME_CODIGO_DIFERENTE   = 104;
    
    const GRAVACAO_ESTADO                          = 1;
    const EXCLUSAO_ESTADO_CODIGO                   = 1;
    
    public static function inicia()
    {
        parent::inicia();
        self::preparaPersistencia();
    }
    
    public static function preparaPersistencia()
    {
        self::$SQL_SELECT[self::PESQUISA_ESTADO_CODIGO] = 
            ' SELECT *' .
            ' FROM EstadoOs' .
            ' WHERE etoId=?';
        
        self::$SQL_SELECT[self::PESQUISA_ESTADOS_NOME] = 
            ' SELECT *' .
            ' FROM EstadoOs' .
            ' WHERE etoNome=?';
        
        self::$SQL_SELECT[self::PESQUISA_ESTADOS_NOME_APROXIMADO] = 
            ' SELECT *' .
            ' FROM EstadoOs' .
            ' WHERE etoNome LIKE ?';
        
        self::$SQL_SELECT[self::PESQUISA_ESTADOS_NOME_CODIGO_DIFERENTE] = 
            ' SELECT *' .
            ' FROM EstadoOs' .
            ' WHERE etoNome=? AND etoId<>?';
        
        self::$SQL_SELECT[self::PESQUISA_ESTADOS] = 
              ' SELECT *'
            . ' FROM EstadoOs'
            . ' ORDER BY etoNome';
            
        self::$SQL_SELECT[self::PESQUISA_ESTADOS_ATIVOS] = 
              ' SELECT *'
            . ' FROM EstadoOs'
            . ' ORDER BY etoNome';
        
        self::$SQL_DELETE[self::EXCLUSAO_ESTADO_CODIGO] =
            ' DELETE FROM EstadoOs'.
            ' WHERE etoId=?';
        
        self::$SQL_INSERT[self::GRAVACAO_ESTADO] =
            ' INSERT INTO EstadoOs'.
            ' VALUES (0, ?)';
        
        self::$SQL_UPDATE[self::GRAVACAO_ESTADO] =
            ' UPDATE EstadoOs SET etoNome=?'.
            ' WHERE etoId=?';
    }
    
    
    public static function carregaModelo(EstadoMdl $estadoMdl, $registro)
    {
        $estadoMdl->atribuiId($registro['etoId']);
        $estadoMdl->atribuiNome($registro['etoNome']);
    }

    public static function consultaModelo(EstadoMdl $estadoMdl, 
        $tipoGetMdl = self::PESQUISA_ESTADO_CODIGO) 
    {
        self::inicia();
        
        switch ($tipoGetMdl) {
        case self::PESQUISA_ESTADO_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetMdl]);
            $iprst->atribuiInteiro(1, $estadoMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn();
        }

        return parent::consultaModeloXmn(__CLASS__, $estadoMdl, $iprst);
    }
    
    public static function consultaListaModelo(EstadoLstMdl $estadoLstMdl, 
        $tipoPesquisa = self::PESQUISA_ESTADOS) 
    {
        self::inicia();
        
        $estadoLstMdl->movePrimeiro(true);
        switch($tipoPesquisa) {
        case self::PESQUISA_ESTADOS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            break;
            
        case self::PESQUISA_ESTADOS_ATIVOS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            break;
            
        case self::PESQUISA_ESTADOS_NOME:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $estadoLstMdl->pegaModelo()->pegaNome());
            break;
            
        case self::PESQUISA_ESTADOS_NOME_APROXIMADO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, 
                '%' . $estadoLstMdl->pegaModelo()->pegaNome() . '%');
            break;
            
        case self::PESQUISA_ESTADOS_NOME_CODIGO_DIFERENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $estadoLstMdl->pegaModelo()->pegaNome());
            $iprst->atribuiInteiro(2, $estadoLstMdl->pegaModelo()->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_PESQUISA_INVALIDO');
        }
        
        try {
            parent::consultaListaModeloXmn(__CLASS__, $estadoLstMdl, $iprst);
            
        } catch(TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }

    public static function armazenaModelo(EstadoMdl $estadoMdl, 
        $tipoGravacao = self::GRAVACAO_ESTADO)
    {
        self::inicia();
        
        try {
            if ($estadoMdl->pegaId() > 0) {
                self::alteraModelo($estadoMdl, $tipoGravacao);
            } else {
                self::insereModelo($estadoMdl, $tipoGravacao);
            }
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }   

    private static function insereModelo(EstadoMdl $estadoMdl, 
        $tipoGravacao = self::GRAVACAO_ESTADO) 
    {
        try {
            self::validaModelo($estadoMdl);
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoGravacao){
        case self::GRAVACAO_ESTADO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_INSERT[$tipoGravacao]);
            $iprst->atribuiTexto(1, $estadoMdl->pegaNome());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_INCLUSAO_INVALIDO');
        }
                
        if (!$iprst->executa()) {
            throw new TratamentoPrstXmn('INCLUSAO_NAO_REALIZADA');
        }
    }
    
    private static function alteraModelo(EstadoMdl $estadoMdl, 
        $tipoGravacao = self::GRAVACAO_ESTADO) 
    {
        try {
            self::validaModelo($estadoMdl);
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    
        switch ($tipoGravacao){
        case self::GRAVACAO_ESTADO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_UPDATE[$tipoGravacao]);
            $iprst->atribuiTexto(1, $estadoMdl->pegaNome());
            $iprst->atribuiTexto(2, $estadoMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_ALTERACAO_INVALIDO');
        }
                
        if (!$iprst->executa()) {
            throw new TratamentoPrstXmn('ALTERACAO_NAO_REALIZADA');
        }
    }
    
    public static function removeModelo(EstadoMdl $estadoMdl, 
        $tipoExclusao = self::EXCLUSAO_ESTADO_CODIGO) 
    {
        self::inicia();
        
        switch ($tipoExclusao){
        case self::EXCLUSAO_ESTADO_CODIGO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_DELETE[$tipoExclusao]);
            $iprst->atribuiInteiro(1, $estadoMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_EXCLUSAO_INVALIDA');
        }
                
        if (!$iprst->executa()) {
            throw new TratamentoPrstXmn('EXCLUSAO_NAO_REALIZADA');
        }
    }
    
    public static function validaModelo(EstadoMdl $estadoMdl)
    {
        if (is_null($estadoMdl)) {
            throw new TratamentoPrstXmn('ESTADO_INVALIDO');

        } else if ($estadoMdl->pegaNome() == '') {
            throw new TratamentoPrstXmn('INFORME_NOME');
        }
        
        $estadoLstMdl = new EstadoLstMdl();
        $estadoLstMdl->adicionaModelo($estadoMdl);

        try {
            if ($estadoMdl->pegaId() > 0) {
                $pesquisa = self::PESQUISA_ESTADOS_NOME_CODIGO_DIFERENTE;
            } else {
                $pesquisa = self::PESQUISA_ESTADOS_NOME;
            }

            self::consultaListaModelo($estadoLstMdl, $pesquisa);
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento('FALHA_PESQUISA_ESTADO_DUPLICADO');
        }
        
        if ($estadoLstMdl->pegaTotalModelo() > 0) {
            throw new TratamentoPrstXmn('ESTADO_DUPLICADO');
        }
    }
}
