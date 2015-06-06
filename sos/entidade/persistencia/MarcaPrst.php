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

include_once $SOS . 'modelo/MarcaMdl.php';
    
class MarcaPrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SQL_SELECT = array();
    private static $SQL_INSERT = array();
    private static $SQL_UPDATE = array();
    private static $SQL_DELETE = array();
    
    const PESQUISA_MARCA_CODIGO                    = 1;
    
    const PESQUISA_MARCAS                          = 101;
    const PESQUISA_MARCAS_ATIVAS                   = 102;
    const PESQUISA_MARCAS_NOME_APROXIMADO          = 103;
    const PESQUISA_MARCAS_NOME_LISTA               = 104;
    const PESQUISA_MARCAS_NOME                     = 105;
    const PESQUISA_MARCAS_NOME_CODIGO_DIFERENTE    = 106;
    
    const GRAVACAO_MARCA           = 1;
    const EXCLUSAO_MARCA_CODIGO    = 1;
    
    public static function inicia()
    {
        parent::inicia();
        self::preparaPersistencia();
    }
    
    public static function preparaPersistencia()
    {
        self::$SQL_SELECT[self::PESQUISA_MARCA_CODIGO] = 
            ' SELECT *' .
            ' FROM Marca' .
            ' WHERE marId=?';
        
        self::$SQL_SELECT[self::PESQUISA_MARCAS_ATIVAS] = 
            ' SELECT *' .
            ' FROM Marca';
        
        self::$SQL_SELECT[self::PESQUISA_MARCAS_NOME_APROXIMADO] = 
            ' SELECT *' .
            ' FROM Marca' .
            ' WHERE marNome LIKE ?';
        
        self::$SQL_SELECT[self::PESQUISA_MARCAS_NOME] = 
            ' SELECT *' .
            ' FROM Marca' .
            ' WHERE marNome = ?';
        
        self::$SQL_SELECT[self::PESQUISA_MARCAS_NOME_CODIGO_DIFERENTE] = 
            ' SELECT *' .
            ' FROM Marca' .
            ' WHERE marNome = ? AND marId <> ?';
        
        self::$SQL_SELECT[self::PESQUISA_MARCAS] = 
              ' SELECT *'
            . ' FROM Marca'
            . ' ORDER BY marNome';
        
        self::$SQL_SELECT[self::PESQUISA_MARCAS_NOME_LISTA] = 
            ' SELECT *' .
            ' FROM Marca' .
            ' WHERE marNome in (?)';
        
        self::$SQL_INSERT[self::GRAVACAO_MARCA] =
            ' INSERT INTO Marca' . 
                '(marId, marNome) ' .
            ' VALUES( 0, ?)';
        
        self::$SQL_UPDATE[self::GRAVACAO_MARCA] =
            ' UPDATE Marca SET marNome=?'.
            ' WHERE  marId=?';
        
        self::$SQL_DELETE[self::EXCLUSAO_MARCA_CODIGO] =
            ' DELETE FROM Marca'.
            ' WHERE  marId=?';
    }

    public function carregaModelo(MarcaMdl $marcaMdl, $registro)
    {
        $marcaMdl->atribuiId($registro['marId']);
        $marcaMdl->atribuiNome($registro['marNome']);
    }
    
    public function consultaModelo(MarcaMdl $marcaMdl, 
        $tipoGetMdl = self::PESQUISA_MARCA_CODIGO)
    {
        self::inicia();
        
        switch ($tipoGetMdl) {
        case self::PESQUISA_MARCA_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetMdl]);
            $iprst->atribuiInteiro(1, $marcaMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_PESQUISA_NAO_INFORMADO');
        }
        
        return parent::consultaModeloXmn(__CLASS__, $marcaMdl, $iprst);
    }
    
    public function consultaListaModelo(MarcaLstMdl $marcaLstMdl, 
        $tipoGetLstMdl = self::PESQUISA_MARCAS, MarcaMdl $marcaMdl = null)
    {
        self::inicia();
        
        $marcaLstMdl->movePrimeiro(true);
        
        switch($tipoGetLstMdl) {
        case self::PESQUISA_MARCAS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetLstMdl]);
            break;
            
        case self::PESQUISA_MARCAS_ATIVAS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetLstMdl]);
            break;
            
        case self::PESQUISA_MARCAS_NOME_APROXIMADO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetLstMdl]);
            $iprst->atribuiTexto(1, 
                '%' . $marcaLstMdl->pegaModelo()->pegaNome() . '%');
            break;
            
        case self::PESQUISA_MARCAS_NOME:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetLstMdl]);
            $iprst->atribuiTexto(1, $marcaLstMdl->pegaModelo()->pegaNome());
            break;
            
        case self::PESQUISA_MARCAS_NOME_CODIGO_DIFERENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetLstMdl]);
            $iprst->atribuiTexto(1, $marcaLstMdl->pegaModelo()->pegaNome());
            $iprst->atribuiInteiro(2, $marcaLstMdl->pegaModelo()->pegaId());
            break;
            
        case self::PESQUISA_MARCAS_NOME_LISTA:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoGetLstMdl]);
            $iprst->atribuiParametro(1, $marcaLstMdl->pegaIds());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_PESQUISA_INVALIDO');
        }
        
        try {
            parent::consultaListaModeloXmn(__CLASS__, $marcaLstMdl, $iprst);
            
        } catch(TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }
    
    public static function armazenaModelo(MarcaMdl $marcaMdl, 
        $tipoGravacao = self::GRAVACAO_MARCA)
    {
        self::inicia();
        try {
            if ($marcaMdl->pegaId() > 0) {
                self::alteraModelo($marcaMdl, $tipoGravacao);
            } else{
                self::insereModelo($marcaMdl, $tipoGravacao);
            }
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }
    
    private static function insereModelo(MarcaMdl $marcaMdl, 
        $tipoGravacao = self::GRAVACAO_MARCA)
    {
        try {
            self::validaModelo($marcaMdl);
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoGravacao) {
        case self::GRAVACAO_MARCA:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_INSERT[$tipoGravacao]);
            $iprst->atribuiTexto(1, $marcaMdl->pegaNome());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_GRAVACAO_INVALIDO');
        }
        
        try {
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento('GRAVACAO_NAO_REALIZADA');
            throw $tratXmn;
        }
    }
    
    private static function alteraModelo(MarcaMdl $marcaMdl, 
        $tipoGravacao = self::GRAVACAO_MARCA)
    {
        try {
            self::validaModelo($marcaMdl);
            
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoGravacao) {
        case self::GRAVACAO_MARCA:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_UPDATE[$tipoGravacao]);
            $iprst->atribuiTexto(1, $marcaMdl->pegaNome());
            $iprst->atribuiInteiro(2, $marcaMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_GRAVACAO_INVALIDO');
        }
        
        try {
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento('GRAVACAO_NAO_REALIZADA');
            throw $tratXmn;
        }
    }
    
    public function removeModelo(MarcaMdl $marcaMdl, 
        $tipoExclusao = self::EXCLUSAO_MARCA_CODIGO)
    {
        self::inicia();
        
        switch ($tipoExclusao) {
        case self::EXCLUSAO_MARCA_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_DELETE[$tipoExclusao]);
            $iprst->atribuiInteiro(1, $marcaMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_EXCLUSAO_INVALIDO');
        }
        
    try {
        $iprst->executa();
        
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento('EXCLUSAO_NAO_REALIADA');
            throw $tratXmn;
        }
    }
    
    private function validaModelo(MarcaMdl $marcaMdl)
    {
        if (is_null($marcaMdl)) {
            throw new TratamentoPrstXmn('MARCA_NAO_INFORMADA');
            
        } else if ($marcaMdl->pegaNome() == '') {
            throw new TratamentoPrstXmn('INFORME_NOME');
        }
        
        $marcaLstMdl = new MarcaLstMdl();
        
        try {
            $marcaLstMdl->adicionaModelo($marcaMdl);
            if ($marcaMdl->pegaId() > 0) {
                $pesquisa = self::PESQUISA_MARCAS_NOME_CODIGO_DIFERENTE;
            } else{
                $pesquisa = self::PESQUISA_MARCAS_NOME;
            }
            
            self::consultaListaModelo($marcaLstMdl, $pesquisa);

            if ($marcaLstMdl->pegaTotalModelo() > 0) {
                throw new TratamentoPrstXmn('MARCA_DUPLICADA');
            }
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
    }
}