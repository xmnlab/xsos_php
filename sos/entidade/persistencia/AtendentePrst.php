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

include_once $SOS . 'modelo/AtendenteMdl.php';
    
class AtendentePrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SELECIONA = array();
    private static $INSERE    = array();
    private static $ALTERA    = array();
    private static $REMOVE    = array();
    
    const PESQUISA_ATENDENTE_LOGIN  = 1;
    const PESQUISA_ATENDENTE_CODIGO = 2;
    
    const PESQUISA_ATENDENTES                       = 100;
    const PESQUISA_ATENDENTES_ATIVOS                = 101;
    const PESQUISA_ATENDENTES_NOME_APROXIMADO       = 102;
    const PESQUISA_ATENDENTES_LOGIN_APROXIMADO      = 103;
    const PESQUISA_ATENDENTES_NOME_LISTA            = 104;
    const PESQUISA_ATENDENTES_NOME                  = 105;
    const PESQUISA_ATENDENTES_NOME_CODIGO_DIFERENTE = 106;
    
    const GRAVACAO_ATENDENTE        = 1;
    const EXCLUSAO_ATENDENTE_CODIGO = 1;
    
    public static function inicia() {
        parent::inicia();
        self::preparaPersistencia();
    }
    
    public static function preparaPersistencia()
    {
        self::$SELECIONA[self::PESQUISA_ATENDENTE_LOGIN] = 
            ' SELECT *'.
            ' FROM Atendente'.
            ' WHERE ateLogin=? and ateSenha=?';
            
        self::$SELECIONA[self::PESQUISA_ATENDENTE_CODIGO] = 
            ' SELECT *'.
            ' FROM Atendente'.
            ' WHERE ateId=?';
        
        self::$SELECIONA[self::PESQUISA_ATENDENTES_ATIVOS] = 
              ' SELECT *'
            . ' FROM Atendente'
            . ' WHERE ateAtivo=1 ORDER BY ateNome';
        
        self::$SELECIONA[self::PESQUISA_ATENDENTES_NOME] = 
            ' SELECT *'.
            ' FROM Atendente'.
            ' WHERE ateNome=?';

        self::$SELECIONA[self::PESQUISA_ATENDENTES_NOME_CODIGO_DIFERENTE] = 
            ' SELECT *'.
            ' FROM Atendente'.
            ' WHERE ateNome=? AND ateId<>?';
            
        self::$SELECIONA[self::PESQUISA_ATENDENTES] = 
            ' SELECT *'.
            ' FROM Atendente';
            
        self::$SELECIONA[self::PESQUISA_ATENDENTES_NOME_APROXIMADO] = 
            ' SELECT *'.
            ' FROM Atendente'.
            ' WHERE ateNome LIKE ?';
            
        self::$SELECIONA[self::PESQUISA_ATENDENTES_LOGIN_APROXIMADO] = 
            ' SELECT *'.
            ' FROM Atendente'.
            ' WHERE ateLogin LIKE ?';
        
        self::$SELECIONA[self::PESQUISA_ATENDENTES_NOME_LISTA] = 
            ' SELECT *'.
            ' FROM Atendente'.
            ' WHERE ateNome IN (?)';
        self::$INSERE[self::GRAVACAO_ATENDENTE] =
            ' INSERT INTO Atendente (ateId, ateNome, ateLogin, ateSenha, ateGrupo)' .
            ' VALUES (0, ?, ?, ?, ?)';
        self::$ALTERA[self::GRAVACAO_ATENDENTE] =
            ' UPDATE Atendente SET ateNome=?, ateLogin=?, ateSenha=?, ateGrupo=?' .
            ' WHERE ateId=?';
        self::$REMOVE[self::EXCLUSAO_ATENDENTE_CODIGO] =
            ' DELETE FROM Atendente'.
            ' WHERE ateId=?';
    }
    
    public function carregaModelo(AtendenteMdl $atendenteMdl, $registro) {
        $atendenteMdl->atribuiId($registro['ateId']);
        $atendenteMdl->atribuiNome($registro['ateNome']);
        $atendenteMdl->atribuiGrupo($registro['ateGrupo']);
        $atendenteMdl->atribuiLogin($registro['ateLogin']);
        $atendenteMdl->atribuiSenha($registro['ateSenha']);
    }

    public function consultaModelo(AtendenteMdl $atendenteMdl, 
        $tipoPesquisa = self::PESQUISA_ATENDENTE_CODIGO)
    {
        
        self::inicia();
        switch ($tipoPesquisa) {
            case self::PESQUISA_ATENDENTE_LOGIN:
                $iprst = new IntermediarioPrstXmn(
                    self::$SELECIONA[$tipoPesquisa]
                );
                
                $iprst->atribuiTexto(1, $atendenteMdl->pegaLogin());
                $iprst->atribuiTexto(2, $atendenteMdl->pegaSenha());
                break;
            case self::PESQUISA_ATENDENTE_CODIGO:
                $iprst = new IntermediarioPrstXmn(
                    self::$SELECIONA[$tipoPesquisa]
                );
                
                $iprst->atribuiInteiro(1, $atendenteMdl->pegaId());
                break;
            default:
                throw new TratamentoPrstXmn('PARAMETRO_PESQUISA_INVALIDO');
        }
        
        return parent::consultaModeloXmn(__CLASS__, $atendenteMdl, $iprst);
    }
    
    public function consultaListaModelo(AtendenteLstMdl $atendenteLstMdl, 
        $tipoPesquisa = self::PESQUISA_ATENDENTES)
    {
        self::inicia();
        
        $atendenteLstMdl->movePrimeiro(true);
        switch($tipoPesquisa) {
        case self::PESQUISA_ATENDENTES:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECIONA[$tipoPesquisa]
            );
            break;
            
        case self::PESQUISA_ATENDENTES_ATIVOS:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECIONA[$tipoPesquisa]
            );
            break;
            
        case self::PESQUISA_ATENDENTES_LOGIN_APROXIMADO:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECIONA[$tipoPesquisa]);
                
            $iprst->atribuiTexto(
                1, '%'.$atendenteLstMdl->pegaModelo()->pegaLogin().'%');
                
            break;
            
        case self::PESQUISA_ATENDENTES_NOME:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECIONA[$tipoPesquisa]);
                
            $iprst->atribuiTexto(1, 
                $atendenteLstMdl->pegaModelo()->pegaNome());
                
            break;
            
        case self::PESQUISA_ATENDENTES_NOME_CODIGO_DIFERENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECIONA[$tipoPesquisa]);
                
            $iprst->atribuiTexto(1, 
                $atendenteLstMdl->pegaModelo()->pegaNome());
                
            $iprst->atribuiInteiro(2, $atendenteLstMdl->pegaModelo()->pegaId());
            
            break;
            
        case self::PESQUISA_ATENDENTES_NOME_APROXIMADO:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECIONA[$tipoPesquisa]);
                
            $iprst->atribuiTexto(1, 
                '%'.$atendenteLstMdl->pegaModelo()->pegaNome().'%');
                
            break;
            
        case self::PESQUISA_ATENDENTES_NOME_LISTA:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECIONA[$tipoPesquisa]);
                
            $iprst->atribuiArray(1, $atendenteLstMdl->pegaIDs());
            break;
            
        default:
            $tratXmn = new TratamentoPrstXmn(
                array('codigo' => 'PARAMETRO_PESQUISA_INVALIDO')
            );
            throw $tratXmn;
        }
        
        try {
            parent::consultaListaModeloXmn(__CLASS__, $atendenteLstMdl, $iprst);
            
        }catch(TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }

    public static function armazenaModelo(AtendenteMdl $atendenteMdl, 
        $tipoGravacao = null)
    {
        self::inicia();
        try{
            if ($atendenteMdl->pegaId() > 0) {
                self::alteraModelo($atendenteMdl, $tipoGravacao);
                
            } else {
                self::insereModelo($atendenteMdl, $tipoGravacao);
            }
        }catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }

    private static function insereModelo(AtendenteMdl $atendenteMdl, 
        $tipoGravacao = self::GRAVACAO_ATENDENTE)
    {
        try{
            self::validaModelo($atendenteMdl);
            
        }catch(TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoGravacao) {
        case self::GRAVACAO_ATENDENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$INSERE[$tipoGravacao]);
                
            $iprst->atribuiTexto(1, $atendenteMdl->pegaNome());
            $iprst->atribuiTexto(2, $atendenteMdl->pegaLogin());
            $iprst->atribuiTexto(3, $atendenteMdl->pegaSenha());
            $iprst->atribuiTexto(4, $atendenteMdl->pegaGrupo());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_INCLUSAO_INVALIDO');
        }
        
        try {
            $iprst->executa();
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(
                array('codigo' => 'INCLUSAO_NAO_REALIZADA')
            );
            throw $tratXmn;
        }
        
    }

    private static function alteraModelo(AtendenteMdl $atendenteMdl,
        $tipoGravacao = self::GRAVACAO_ATENDENTE)
    {
        self::inicia();
        
        try{
            self::validaModelo($atendenteMdl);
        }catch(TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoGravacao) {
        case self::GRAVACAO_ATENDENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$ALTERA[$tipoGravacao]);
                
            $iprst->atribuiTexto(1, $atendenteMdl->pegaNome());
            $iprst->atribuiTexto(2, $atendenteMdl->pegaLogin());
            $iprst->atribuiTexto(3, $atendenteMdl->pegaSenha());
            $iprst->atribuiTexto(4, $atendenteMdl->pegaGrupo());
            $iprst->atribuiInteiro(5, $atendenteMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_ALTERACAO_INVALIDO');
        }
        
        try {
            $iprst->executa();
        }catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(
                array('codigo' => 'GRAVACAO_NAO_REALIZADA')
            );
            throw $tratXmn;
        }
    }
    
    public static function removeModelo(AtendenteMdl $atendenteMdl, 
        $tipoExclusao = self::EXCLUSAO_ATENDENTE_CODIGO)
    {
        self::inicia();

        switch ($tipoExclusao) {
        case self::EXCLUSAO_ATENDENTE_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$REMOVE[$tipoExclusao]);
                
            $iprst->atribuiInteiro(1, $atendenteMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_EXCLUSAO_INVALIDO');
        }

        try {
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(
                array('codigo' => 'FALHA_EXCLUSAO')
            );
            throw $tratXmn;
        }
    }
    
    private static function validaModelo(AtendenteMdl $atendenteMdl)
    {
        if (is_null($atendenteMdl)) {
            throw new TratamentoPrstXmn('ATENDENTE_INVALIDO');
            
        } else if ($atendenteMdl->pegaNome() == '') {
            throw new TratamentoPrstXmn('INFORME_NOME');
            
        } else if ($atendenteMdl->pegaLogin() == '') {
            throw new TratamentoPrstXmn('INFORME_USUARIO');
            
        }
        
        $atendenteLstMdl = new AtendenteLstMdl();
        $atendenteLstMdl->adicionaModelo($atendenteMdl);

        try{
            if ($atendenteMdl->pegaId() > 0) {
                $pesquisa = self::PESQUISA_ATENDENTES_NOME_CODIGO_DIFERENTE;

            } else {
                $pesquisa = self::PESQUISA_ATENDENTES_NOME;
            }

            self::consultaListaModelo($atendenteLstMdl, $pesquisa);
            
        }catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento(
                array('codigo' => 'FALHA_PESQUISA_ATENDENTE_DUPLICADA')
            );
            throw $tratXmn;
        }
        
        if ($atendenteLstMdl->pegaTotalModelo() > 0) {
            throw new TratamentoPrstXmn('ATENDENTE_DUPLICADA');
        }
    }
}