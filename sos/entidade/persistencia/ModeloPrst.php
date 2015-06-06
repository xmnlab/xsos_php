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

include_once $SOS . 'modelo/ModeloMdl.php';
include_once $SOS . 'modelo/ModeloLstMdl.php';
include_once $SOS . 'modelo/MarcaMdl.php';

class ModeloPrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SQL_SELECT = array();
    private static $SQL_INSERT = array();
    private static $SQL_UPDATE = array();
    private static $SQL_DELETE = array();
    
    const PESQUISA_MODELO_CODIGO                        = 1;
    const PESQUISA_MODELO_IMAGEM_CODIGO                 = 2;
    const PESQUISA_MODELO_VERIFICA_IMAGEM_CODIGO        = 4;
    
    const PESQUISA_MODELOS_NOME_APROXIMADO              = 16;
    const PESQUISA_MODELOS_MARCA_APROXIMADA             = 32;
    const PESQUISA_MODELOS_NOME                         = 64;
    const PESQUISA_MODELOS                              = 128;
    const PESQUISA_MODELOS_MARCA_CODIGO                 = 256;
    const PESQUISA_MODELOS_NOME_MARCA_CODIGO_DIFERENTE  = 572;
    const PESQUISA_MODELOS_NOME_MARCA                   = 1024;

    const GRAVACAO_MODELO                               = 2048;
    const GRAVACAO_MODELO_SEM_FOTO                      = 4096;
    const EXCLUSAO_MODELO_CODIGO                        = 8192;
    
    const REMOVE_FOTO                                   = 16384;
    
    public static function inicia()
    {
        parent::inicia();
        self::preparaPersistencia();
    }
    
    public static function preparaPersistencia()
    {
        self::$SQL_SELECT[self::PESQUISA_MODELO_CODIGO] = 
              ' SELECT modId, modNome, modMarca'
            . ' FROM Modelo'
            . ' WHERE modId=?';
            
        self::$SQL_SELECT[self::PESQUISA_MODELO_VERIFICA_IMAGEM_CODIGO] = 
              ' SELECT modId, modNome, modMarca'
            . ' FROM Modelo'
            . ' WHERE modId=? AND NOT ISNULL(modImagem)';
            
        self::$SQL_SELECT[self::PESQUISA_MODELO_IMAGEM_CODIGO] = 
              ' SELECT modId, modNome, modMarca, modImagem'
            . ' FROM Modelo'
            . ' WHERE modId=?';
        
        self::$SQL_SELECT[self::PESQUISA_MODELOS] = 
              ' SELECT modId, modNome, modMarca'
            . ' FROM Modelo'
            . ' ORDER BY modNome';
        
        self::$SQL_SELECT[self::PESQUISA_MODELOS_NOME_APROXIMADO] = 
            ' SELECT modId, modNome, modMarca' .
            ' FROM Modelo' .
            ' WHERE modNome LIKE ?';
        
        self::$SQL_SELECT[self::PESQUISA_MODELOS_NOME] = 
            ' SELECT modId, modNome, modMarca' .
            ' FROM Modelo' .
            ' WHERE modNome = ?';
        
        self::$SQL_SELECT[self::PESQUISA_MODELOS_NOME_MARCA] = 
            ' SELECT modId, modNome, modMarca' .
            ' FROM Modelo' .
            ' WHERE modNome=? AND modMarca=?';
            
        self::$SQL_SELECT[self::PESQUISA_MODELOS_MARCA_APROXIMADA] = 
            ' SELECT Modelo.modId, Modelo.modNome, Modelo.modMarca' .
            ' FROM '.
            '     Modelo INNER JOIN ('.
            '        SELECT * FROM Marca WHERE marNome LIKE ?) Marca'.
            '     ON (marId = modMarca)';
        
        self::$SQL_SELECT[self::PESQUISA_MODELOS_MARCA_CODIGO] = 
              ' SELECT modId, modNome, modMarca'
            . ' FROM Modelo'
            . ' WHERE modMarca = ?'
            . ' ORDER BY modNome';
        
        self::$SQL_SELECT[self::PESQUISA_MODELOS_NOME_MARCA_CODIGO_DIFERENTE] = 
            ' SELECT modId, modNome, modMarca' .
            ' FROM Modelo' .
            ' WHERE modNome=? AND modMarca=? AND modId<>?';
        
        self::$SQL_INSERT[self::GRAVACAO_MODELO] =
            ' INSERT INTO Modelo (modId, modNome, modMarca, modImagem)'.
            ' VALUES(NULL, ?, ?, ?)';
        
        self::$SQL_UPDATE[self::GRAVACAO_MODELO] =    
              ' UPDATE Modelo SET '
            .    ' modNome=?,'
            .    ' modMarca=?,'
            .    ' modImagem=?'
            . ' WHERE modId=?';
            
        self::$SQL_UPDATE[self::GRAVACAO_MODELO_SEM_FOTO] =    
              ' UPDATE Modelo SET '
            .    ' modNome=?,'
            .    ' modMarca=?'
            . ' WHERE modId=?';
            
        self::$SQL_UPDATE[self::REMOVE_FOTO] =    
              ' UPDATE Modelo SET '
            .    ' modImagem=NULL'
            . ' WHERE modId=?';
        
        self::$SQL_DELETE[self::EXCLUSAO_MODELO_CODIGO] =    
            ' DELETE FROM Modelo WHERE modId=?';
    }
    
    public static function carregaModelo(ModeloMdl $modeloMdl, $registro) 
    {
        $modeloMdl->atribuiId($registro['modId']);
        $modeloMdl->atribuiNome($registro['modNome']);
       @$modeloMdl->pegaGravuraXMdl()->atribuiConteudo($registro['modImagem']);
        $modeloMdl->pegaMarcaMdl()->atribuiId($registro['modMarca']);
        
        MarcaPrst::consultaModelo($modeloMdl->pegaMarcaMdl());
    }
    
    public function consultaModelo(ModeloMdl $modeloMdl, 
        $tipoGetMdl = self::PESQUISA_MODELO_CODIGO) 
    {
        self::inicia();
        
        switch ($tipoGetMdl) {
        case self::PESQUISA_MODELO_CODIGO:
        case self::PESQUISA_MODELO_VERIFICA_IMAGEM_CODIGO:
        case self::PESQUISA_MODELO_IMAGEM_CODIGO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoGetMdl]);
            $iprst->atribuiInteiro(1, $modeloMdl->pegaId());
            break;
            
        default:
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'PESQUISA_PARAMETRO_INVALIDO'));
            throw $tratXmn;
        }
        
        return parent::consultaModeloXmn(__CLASS__, $modeloMdl, $iprst);
    }
    
    public function consultaListaModelo(ModeloLstMdl $modeloLstMdl, 
        $tipoPesquisa = self::PESQUISA_MODELOS)
    {
        self::inicia();
        
        $modeloLstMdl->movePrimeiro(true);
        
        switch($tipoPesquisa) {
        case self::PESQUISA_MODELOS:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoPesquisa]);
            break;
            
        case self::PESQUISA_MODELOS_NOME_APROXIMADO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, '%'.$modeloLstMdl->pegaModelo()->pegaNome().'%');
            break;
            
        case self::PESQUISA_MODELOS_NOME:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $modeloLstMdl->pegaModelo()->pegaNome());
            break;
            
        case self::PESQUISA_MODELOS_MARCA_APROXIMADA:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, '%'.$modeloLstMdl->pegaModelo()->pegaMarcaMdl()->pegaNome().'%');
            break;
            
        case self::PESQUISA_MODELOS_NOME_MARCA_CODIGO_DIFERENTE:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $modeloLstMdl->pegaModelo()->pegaNome());
            $iprst->atribuiInteiro(2, $modeloLstMdl->pegaModelo()->pegaMarcaMdl()->pegaId());
            $iprst->atribuiInteiro(3, $modeloLstMdl->pegaModelo()->pegaId());
            break;
            
        case self::PESQUISA_MODELOS_NOME_MARCA:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $modeloLstMdl->pegaModelo()->pegaNome());
            $iprst->atribuiInteiro(2, $modeloLstMdl->pegaModelo()->pegaMarcaMdl()->pegaId());
            break;
            
        case self::PESQUISA_MODELOS_MARCA_CODIGO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiInteiro(1, $modeloLstMdl->pegaModelo()->pegaMarcaMdl()->pegaId());
            break;
            
        default:
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'PESQUISA_PARAMETRO_INVALIDO'));
            throw $tratXmn;
        }
        
        try {
            parent::consultaListaModeloXmn(__CLASS__, $modeloLstMdl, $iprst);
            
        } catch(TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }
    
    public static function armazenaModelo(ModeloMdl $modeloMdl, 
        $tipoGravacao = self::GRAVACAO_MODELO) 
    {
        self::inicia();
        try {
            if ($modeloMdl->pegaId() > 0) {
                self::alteraModelo($modeloMdl, $tipoGravacao);
                
            } else {
                self::insereModelo($modeloMdl, $tipoGravacao);
            }
            
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }
    
    private static function insereModelo(ModeloMdl $modeloMdl,
        $tipoGravacao = self::GRAVACAO_MODELO) 
    {
        self::inicia();
        
        try {
            self::validaModelo($modeloMdl, $tipoGravacao);
            
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch($tipoGravacao) {
        case self::GRAVACAO_MODELO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_INSERT[$tipoGravacao]);
            $iprst->atribuiTexto(1, $modeloMdl->pegaNome());
            $iprst->atribuiInteiro(2, $modeloMdl->pegaMarcaMdl()->pegaId());
            $iprst->atribuiBinario(3, GravuraXmn::retornaImagem(
                $modeloMdl->pegaGravuraXMdl(), 'JPEG'),
                'NULL');
            break;
            
        default:
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'PARAMETRO_INCLUSAO_INVALIDO'));
            throw $tratXmn;
        }
        
        try {
            if(!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            
            unset($iprst);
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(
                array('codigo' => 'FALHA_INCLUSAO'));
            throw $tratXmn; 
        }
    }
    
    private static function alteraModelo(ModeloMdl $modeloMdl, 
        $tipoGravacao = self::GRAVACAO_MODELO)
    {
        self::inicia();
        try {
            self::validaModelo($modeloMdl, $tipoGravacao);
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch($tipoGravacao) {
        case self::GRAVACAO_MODELO:
            if ($modeloMdl->pegaGravuraXMdl()->pegaObjeto() != '') {
                $iprst = new IntermediarioPrstXmn(
                    self::$SQL_UPDATE[$tipoGravacao]);
                $iprst->atribuiTexto(1, $modeloMdl->pegaNome());
                $iprst->atribuiInteiro(2, $modeloMdl->pegaMarcaMdl()->pegaId());
                $iprst->atribuiBinario(3, GravuraXmn::retornaImagem(
                    $modeloMdl->pegaGravuraXMdl(), 'JPEG'), 'NULL');
                $iprst->atribuiInteiro(4, $modeloMdl->pegaId());
                
            } else {
                $iprst = new IntermediarioPrstXmn(
                    self::$SQL_UPDATE[self::GRAVACAO_MODELO_SEM_FOTO]);
                $iprst->atribuiTexto(1, $modeloMdl->pegaNome());
                $iprst->atribuiInteiro(2, $modeloMdl->pegaMarcaMdl()->pegaId());
                $iprst->atribuiInteiro(3, $modeloMdl->pegaId());
            }
            break;
                
        case self::REMOVE_FOTO:
            $iprst = new IntermediarioPrstXmn(self::$SQL_UPDATE[$tipoGravacao]);
            $iprst->atribuiInteiro(1, $modeloMdl->pegaId());
            break;

        default:
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'PARAMETRO_ALTERACAO_INVALIDO'));
            throw $tratXmn;
        }
        
        try {
            if(!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            
            unset($iprst);
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(
                array('codigo' => 'FALHA_ALTERACAO'));
            throw $tratXmn; 
        }
    }
    
    public static function removeModelo(ModeloMdl $modeloMdl, 
        $tipoExclusao = self::EXCLUSAO_MODELO_CODIGO)
    {
        self::inicia();
        
        switch($tipoExclusao) {
        case self::EXCLUSAO_MODELO_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_DELETE[$tipoExclusao]);
            $iprst->atribuiInteiro(1, $modeloMdl->pegaId());
            break;
            
        default:
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'PARAMETRO_EXCLUSAO_INVALIDO'));
            throw $tratXmn;
        }
        
        try {
            if(!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            unset($iprst);
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(
                array('codigo' => 'EXCLUSAO_NAO_REALIZADA'));
            throw $tratXmn; 
        }
    }
    
    private static function validaModelo(ModeloMdl $modeloMdl, $tipoValidacao)
    {
        if ($tipoValidacao == self::REMOVE_FOTO) {
            if($modeloMdl->pegaId() < 1) {
                $tratXmn = new TratamentoPrstXmn(
                    array('codigo' => 'INFORME_ID'));
                throw $tratXmn;
            }
            return true;
        }
        
        if ($modeloMdl->pegaNome() == '') {
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'INFORME_NOME'));
            throw $tratXmn;
            
        } else if ($modeloMdl->pegaMarcaMdl()->pegaId() < 1) {
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'INFORME_MARCA'));
            throw $tratXmn;
        }
        
        $modeloLstMdl = new ModeloLstMdl();

        try {
            if ($modeloMdl->pegaId() > 0) {
                $pesquisa = self::PESQUISA_MODELOS_NOME_MARCA_CODIGO_DIFERENTE;
            } else {
                $pesquisa = self::PESQUISA_MODELOS_NOME_MARCA;
            }
            
            $modeloLstMdl->adicionaModelo($modeloMdl);

            self::consultaListaModelo($modeloLstMdl, $pesquisa);
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(array('codigo' => 'FALHA_PESQUISA_MODELO_DUPLICADO'));
            throw $tratXmn;
        }
        
        if ($modeloLstMdl->pegaTotalModelo() > 0) {
            $tratXmn = new TratamentoPrstXmn(array('codigo' =>'MODELO_DUPLICADO'));
            throw $tratXmn;
        }
        
    }
}