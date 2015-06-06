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

include_once $SOS . 'modelo/OperadoraMdl.php';
    
class OperadoraPrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SQL_SELECT = array();
    private static $SQL_INSERT = array();
    private static $SQL_UPDATE = array();
    private static $SQL_DELETE = array();
    
    const PESQUISA_OPERADORA_CODIGO = 1;
    
    const PESQUISA_OPERADORAS                          = 101;
    const PESQUISA_OPERADORAS_ATIVAS                   = 102;
    const PESQUISA_OPERADORAS_NOME_APROXIMADO          = 103;
    const PESQUISA_OPERADORAS_NOME                     = 104;
    const PESQUISA_OPERADORAS_NOME_CODIGO_DIFERENTE    = 105;

    const GRAVACAO_OPERADORA           = 1;
    const EXCLUSAO_OPERADORAS_CODIGO   = 1;
    
    public static function inicia(){
        parent::inicia();
        self::preparaPersistencia();
    }
    
    public static function preparaPersistencia(){
        self::$SQL_SELECT[self::PESQUISA_OPERADORA_CODIGO] = 
            ' SELECT *' .
            ' FROM Operadora' .
            ' WHERE opeId=?';
            
        self::$SQL_SELECT[self::PESQUISA_OPERADORAS] = 
              ' SELECT *'
            . ' FROM Operadora'
            . ' ORDER BY opeNome';
            
        self::$SQL_SELECT[self::PESQUISA_OPERADORAS_ATIVAS] = 
              ' SELECT *'
            . ' FROM Operadora'
            . ' ORDER BY opeNome';
            
        self::$SQL_SELECT[self::PESQUISA_OPERADORAS_NOME] = 
            ' SELECT *' .
            ' FROM Operadora WHERE opeNome=?';
        
        self::$SQL_SELECT[self::PESQUISA_OPERADORAS_NOME_CODIGO_DIFERENTE] = 
            ' SELECT *' .
            ' FROM Operadora' .
            ' WHERE opeNome=? and opeId<>?';

        self::$SQL_SELECT[self::PESQUISA_OPERADORAS_NOME_APROXIMADO] = 
            ' SELECT *' .
            ' FROM Operadora' .
            ' WHERE opeNome like ?';
        
        self::$SQL_INSERT[self::GRAVACAO_OPERADORA] = 
            ' INSERT INTO Operadora (opeId, opeNome)' .
            ' VALUES(0,?)';
        
        self::$SQL_UPDATE[self::GRAVACAO_OPERADORA] = 
            ' UPDATE Operadora SET opeNome=?'.
            ' WHERE opeId=?';
        
        self::$SQL_DELETE[self::EXCLUSAO_OPERADORAS_CODIGO] = 
            ' DELETE FROM Operadora'.
            ' WHERE opeId=?';

    }
        
    public function carregaModelo(OperadoraMdl $operadoraMdl, $registro)
    {
        $operadoraMdl->atribuiId($registro['opeId']);
        $operadoraMdl->atribuiNome($registro['opeNome']);
    }

    public function consultaModelo(OperadoraMdl $operadoraMdl, 
        $tipoGetMdl = self::PESQUISA_OPERADORA_CODIGO)
    {
        self::inicia();
        switch ($tipoGetMdl){
        case self::PESQUISA_OPERADORA_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                    self::$SQL_SELECT[$tipoGetMdl]);
            $iprst->atribuiInteiro(1, $operadoraMdl->pegaId());
            break;
            
        default:
            break;
        }
        return parent::consultaModeloXmn(__CLASS__, $operadoraMdl, $iprst);
    }
    
    public function consultaListaModelo(OperadoraLstMdl $operadoraLstMdl,
        $tipoPesquisa = self::PESQUISA_OPERADORAS, 
        OperadoraMdl $operadoraMdl = null)
    {
        self::inicia();
        
        $operadoraLstMdl->movePrimeiro(true);
        switch($tipoPesquisa){
        case self::PESQUISA_OPERADORAS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            break;
            
        case self::PESQUISA_OPERADORAS_ATIVAS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            break;
            
        case self::PESQUISA_OPERADORAS_NOME_APROXIMADO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, 
                '%' . $operadoraLstMdl->pegaModelo()->pegaNome() . '%');
            break;
            
        case self::PESQUISA_OPERADORAS_NOME:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $operadoraLstMdl->pegaModelo()->pegaNome());
            break;
            
        case self::PESQUISA_OPERADORAS_NOME_CODIGO_DIFERENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $operadoraLstMdl->pegaModelo()->pegaNome());
            $iprst->atribuiInteiro(2, $operadoraLstMdl->pegaModelo()->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_NAO_INFORMADO');
        }
        
        try {
            parent::consultaListaModeloXmn(__CLASS__, $operadoraLstMdl, $iprst);
            
        }catch(TratamentoPrstXmn $tratXmn){
            throw $tratXmn;
        }
    }
    
    public function armazenaModelo(OperadoraMdl $operadoraMdl, 
        $tipoGravacao = self::GRAVACAO_OPERADORA)
    {
        self::inicia();
        
        try{
            if ($operadoraMdl->pegaId() > 0) {
                self::alteraModelo($operadoraMdl, $tipoGravacao);
            } else {
                self::insereModelo($operadoraMdl, $tipoGravacao);
            }
        }catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }

    public function insereModelo(OperadoraMdl $operadoraMdl, 
        $tipoGravacao = self::GRAVACAO_OPERADORA)
    {
        try{
            self::validaModelo($operadoraMdl);
        }catch (TratamentoPrstXmn $tratXmn){
            throw $tratXmn;
        }

        switch ($tipoGravacao){
            case self::GRAVACAO_OPERADORA:
                $iprst = new IntermediarioPrstXmn(
                    self::$SQL_INSERT[$tipoGravacao]);
                $iprst->atribuiTexto(1, $operadoraMdl->pegaNome());
                break;
            default:
                throw new TratamentoPrstXmn('PARAMETRO_INCLUSAO_INVALIDO');
        }
        
        try{
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            
        }catch (TratamentoPrstXmn $tratXmn){
            $tratXmn->atribuiTratamento('INCLUSAO_NAO_REALIZADA');
            throw $tratXmn;
        }
    }
    
    public function alteraModelo(OperadoraMdl $operadoraMdl, 
        $tipoGravacao = self::GRAVACAO_OPERADORA)
    {
        try{
            self::validaModelo($operadoraMdl);
        }catch (TratamentoPrstXmn $tratXmn){
            throw $tratXmn;
        }
        
        switch ($tipoGravacao){
            case self::GRAVACAO_OPERADORA:
                $iprst = new IntermediarioPrstXmn(
                    self::$SQL_UPDATE[$tipoGravacao]);
                $iprst->atribuiTexto(1, $operadoraMdl->pegaNome());
                $iprst->atribuiInteiro(2, $operadoraMdl->pegaId());
                break;
            default:
                throw new TratamentoPrstXmn('PARAMETRO_ALTERACAO_INVALIDO');
        }
        
        try{
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
        }catch (TratamentoPrstXmn $tratXmn){
            $tratXmn->atribuiTratamento('ALTERACAO_NAO_REALIZADA');
            throw $tratXmn;
        }
    }
    
    public function removeModelo(OperadoraMdl $operadoraMdl, 
        $tipoExclusao = self::EXCLUSAO_OPERADORAS_CODIGO)
    {
        self::inicia();
        
        switch ($tipoExclusao){
            case self::EXCLUSAO_OPERADORAS_CODIGO:
                $iprst = new IntermediarioPrstXmn(
                    self::$SQL_DELETE[$tipoExclusao]);
                $iprst->atribuiInteiro(1, $operadoraMdl->pegaId());
                break;
                
            default:
                throw new TratamentoPrstXmn('PARAMETRO_EXCLUSAO_INVALIDO');
        }
        
        try{
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
        }catch (TratamentoPrstXmn $tratXmn){
            $tratXmn->atribuiTratamento('EXCLUSAO_NAO_REALIZADA');
            throw $tratXmn;
        }
    }
    
    private function validaModelo(OperadoraMdl $operadoraMdl)
    {
        if (is_null($operadoraMdl)) {
            throw new TratamentoPrstXmn('OPERADORA_INVALIDA');
            
        } else if ($operadoraMdl->pegaNome() == '') {
            throw new TratamentoPrstXmn('INFORME_NOME');
        }
        
        $operadoraLstMdl = new OperadoraLstMdl();
        
        try {
            if ($operadoraMdl->pegaId() > 0) {
                $pesquisa = self::PESQUISA_OPERADORAS_NOME_CODIGO_DIFERENTE;
            } else {
                $pesquisa = self::PESQUISA_OPERADORAS_NOME;
            }
            
            $operadoraLstMdl->adicionaModelo($operadoraMdl);

            self::consultaListaModelo($operadoraLstMdl, $pesquisa);
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento('FALHA_PESQUISA_OPERADORA_DUPLICADA');
            throw $tratXmn;
        }
        
        if ($operadoraLstMdl->pegaTotalModelo() > 0){
            throw new TratamentoPrstXmn('OPERADORA_DUPLICADA');
        }
    }   
}