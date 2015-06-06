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

include_once $SOS . 'modelo/OsMdl.php';
include_once $SOS . 'modelo/MarcaMdl.php';
include_once $SOS . 'modelo/ClienteMdl.php';
include_once $SOS . 'modelo/ModeloMdl.php';
include_once $SOS . 'modelo/OsMdl.php';

class OsPrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SQL_SELECT = array();
    private static $SQL_INSERT = array();
    private static $SQL_UPDATE = array();
    private static $SQL_DELETE = array();
    
    const PESQUISA_OS_CODIGO                   = 1;
    
    const PESQUISA_OSS_ESTADO                  = 101;
    const PESQUISA_OSS_ESTADO_PERIODO          = 102;
    const PESQUISA_OSS_CLIENTE_NOME            = 103;
    const PESQUISA_OSS_APARELHO_MODELO         = 104;
    const PESQUISA_OSS_APARELHO_MARCA          = 105;
    const PESQUISA_OSS_APARELHO_HEXADECIMAL    = 106;
    const PESQUISA_OSS_TOPO5                   = 107;
    
    const GRAVACAO_OS = 1;
    const REMOVE_OS = 1;
    
    public static function inicia() {
        parent::inicia();
        self::preparaPersistencia();
    }
    
    private static function preparaPersistencia() {
        self::$SQL_SELECT[self::PESQUISA_OS_CODIGO] = 
            ' SELECT *' .
            ' FROM Os' .
            ' WHERE osxId=?';

        self::$SQL_SELECT[self::PESQUISA_OSS_APARELHO_HEXADECIMAL] = 
            ' SELECT *' .
            ' FROM Os' .
            ' WHERE osxNumeroHexadecimalAparelho=?';
            
        self::$SQL_SELECT[self::PESQUISA_OSS_ESTADO] = 
              ' SELECT *'
            . ' FROM Os INNER JOIN ('
            .        ' SELECT * FROM EstadoOs'
            .        ' WHERE etoId=?'
            .    ' ) AS StatusOs ON (etoId = osxEstado)'
            . ' ORDER BY desc osxId';
            
       self::$SQL_SELECT[self::PESQUISA_OSS_TOPO5] = 
              ' SELECT *'
            . ' FROM Os'
            . ' ORDER BY osxId DESC'
            . ' LIMIT 0, 5';

        self::$SQL_SELECT[self::PESQUISA_OSS_CLIENTE_NOME] = 
            ' SELECT Os.*' .
            ' FROM Os INNER JOIN (' .
                    ' SELECT cliId FROM Cliente' .
                    ' WHERE cliNome like ?' . 
                ' ) AS ClienteOs ON (osxCliente = cliId)';
        
        self::$SQL_SELECT[self::PESQUISA_OSS_APARELHO_MARCA] = 
            ' SELECT Os.*' .
            ' FROM Os INNER JOIN (' .
                    ' SELECT modId FROM Modelo' .
                    ' WHERE modMarca IN (?)' . 
                ' ) AS ModeloAparelhoOs ON (osxModeloAparelho = modId)';
        
        self::$SQL_SELECT[self::PESQUISA_OSS_APARELHO_MODELO] = 
            ' SELECT Os.*' .
            ' FROM Os INNER JOIN (' .
                    ' SELECT modId FROM Modelo' .
                    ' WHERE modNome LIKE ?' . 
                ' ) AS ModeloAparelhoOs ON (osxModeloAparelho = modId)';
        
        self::$SQL_INSERT[self::GRAVACAO_OS] =
            ' INSERT INTO Os ('
                . ' osxId,'
                . ' osxCliente,'
                . ' osxNumeroHexadecimalAparelho,'
                . ' osxNumeroLinhaAparelho,'
                . ' osxPlanoAparelho,'
                . ' osxOperadoraAparelho,'
                . ' osxModeloAparelho,'
                . ' osxDtAbertura,'
                . ' osxDtEntregaPrevista,'
                . ' osxDtEntrega,'
                . ' osxEstado,'
                . ' osxAtendente,'
                . ' osxObsCliente,'
                . ' osxObsTecnica,'
                . ' osxDescricaoServico,'
                . ' osxValorServico,'
                . ' osxEstadoAparelho,'
                . ' osxCustoMaterial'
            . ' ) VALUES ('
                . ' 0, ?, ?, ?, ?, ?, ?, now(), ?,'
                . ' ?, ?, ?, ?, ?, ?, ?, ?, ?'
            . ' )';
        
        self::$SQL_DELETE[self::REMOVE_OS] =
            ' DELETE FROM Os'.
            ' WHERE'.
                ' osxId=?';
        
        self::$SQL_UPDATE[self::GRAVACAO_OS] =
            ' UPDATE Os SET'
                . ' osxCliente=?,'
                . ' osxNumeroHexadecimalAparelho=?,'
                . ' osxNumeroLinhaAparelho=?,'
                . ' osxPlanoAparelho=?,'
                . ' osxOperadoraAparelho=?,'
                . ' osxModeloAparelho=?,'
                . ' osxDtEntregaPrevista=?,'
                . ' osxDtEntrega=?,'
                . ' osxEstado=?,'
                . ' osxAtendente=?,'
                . ' osxObsCliente=?,'
                . ' osxObsTecnica=?,'
                . ' osxDescricaoServico=?,'
                . ' osxValorServico=?,'
                . ' osxEstadoAparelho=?,'
                . ' osxCustoMaterial=?'
            . ' WHERE'
                . ' osxId=?';
    }
        
    public function carregaModelo(OsMdl $osMdl, $registro) 
    {
        if (is_array($registro) && count($registro) == 0) {
            return false;
        }
        
        $osMdl->atribuiId($registro['osxId']);
        $osMdl->atribuiCustoMaterial($registro['osxCustoMaterial']);
        
        $osMdl->atribuiDtAbertura(new DataHoraArquetipoXmn(
            $registro['osxDtAbertura'], 'Y-m-d H:i:s'));
        $osMdl->atribuiDtEntregaPrevista(new DataHoraArquetipoXmn(
            $registro['osxDtEntregaPrevista'], 'Y-m-d H:i:s'));
        $osMdl->atribuiDtEntrega(new DataHoraArquetipoXmn(
            $registro['osxDtEntrega'], 'Y-m-d'));
        
        $osMdl->atribuiObsCliente($registro['osxObsCliente']);
        $osMdl->atribuiObsTecnica($registro['osxObsTecnica']);
        $osMdl->atribuiDescricaoServico($registro['osxDescricaoServico']);
        $osMdl->atribuiValorServico($registro['osxValorServico']);

        $osMdl->pegaAtendenteMdl()->atribuiId($registro['osxAtendente']);
        $osMdl->pegaClienteMdl()->atribuiId($registro['osxCliente']);
        $osMdl->pegaEstadoMdl()->atribuiId($registro['osxEstado']);
        
        $osMdl->pegaAparelhoMdl()->atribuiHexadecimal(
            $registro['osxNumeroHexadecimalAparelho']);
        $osMdl->pegaAparelhoMdl()->atribuiNumeroLinha(
            $registro['osxNumeroLinhaAparelho']);
        $osMdl->pegaAparelhoMdl()->atribuiPlanoLinha(
            $registro['osxPlanoAparelho']);
        $osMdl->pegaAparelhoMdl()->atribuiEstadoAparelho(
            $registro['osxEstadoAparelho']);
        $osMdl->pegaAparelhoMdl()->pegaModeloMdl()->atribuiId(
            $registro['osxModeloAparelho']);
        $osMdl->pegaAparelhoMdl()->pegaOperadoraMdl()->atribuiId(
            $registro['osxOperadoraAparelho']);
            
        try {
            ClientePrst::consultaModelo($osMdl->pegaClienteMdl());
        } catch (TratamentoPrstXmn $tratXmn) { }
        
        try {
            ModeloPrst::consultaModelo($osMdl->pegaAparelhoMdl()->pegaModeloMdl());
        } catch (TratamentoPrstXmn $tratXmn) { }
        
        try {
            EstadoOsPrst::consultaModelo($osMdl->pegaEstadoMdl());
        } catch (TratamentoPrstXmn $tratXmn) { }
        
        try {
            OperadoraPrst::consultaModelo($osMdl->pegaAparelhoMdl()
                ->pegaOperadoraMdl());
        } catch (TratamentoPrstXmn $tratXmn) { }
        
        try {
            AtendentePrst::consultaModelo($osMdl->pegaAtendenteMdl());
        } catch (TratamentoPrstXmn $tratXmn) { }
        
    }
    
    public function consultaModelo(OsMdl $osMdl, 
        $tipoPesquisa = self::PESQUISA_OS_CODIGO)
    {
        self::inicia();
        
        switch ($tipoPesquisa) {
        case self::PESQUISA_OS_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiInteiro(1, $osMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_CONSULTA_NAO_INFORMADO');
        }
        
        try {
            parent::consultaModeloXmn(__CLASS__, $osMdl, $iprst);
        }catch (TratamentoPrstXmn $e) {
            throw $e;
        }
    }
    
    public function consultaListaModelo(OsLstMdl $osLstMdl,
        $tipoPesquisa = self::PESQUISA_OS_CODIGO)
    {
        self::inicia();
        
        $osLstMdl->movePrimeiro(true);
        $osMdl = $osLstMdl->pegaModelo();
        
        switch ($tipoPesquisa) {
        case self::PESQUISA_OS_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiInteiro(1, $osMdl->pegaId());
            break;
            
        case self::PESQUISA_OSS_APARELHO_HEXADECIMAL:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $osMdl->
                pegaAparelhoMdl()->pegaHexadecimal());
            break;
            
        case self::PESQUISA_OSS_CLIENTE_NOME:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, '%' . $osMdl->
                pegaClienteMdl()->pegaNome() . '%');
            break;
            
        case self::PESQUISA_OSS_APARELHO_MARCA:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
                
            $marcaLstMdl = new MarcaLstMdl();
            $marcaLstMdl->adicionaModelo($osMdl->pegaAparelhoMdl()->
                pegaModeloMdl()->pegaMarcaMdl());
            
            MarcaPrst::consultaListaModelo($marcaLstMdl, 
                MarcaPrst::PESQUISA_MARCAS_NOME_APROXIMADO);
                
            if ($marcaLstMdl->pegaTotalModelo() > 0) {
                $iprst->atribuiParametro(1, $marcaLstMdl->pegaIds());
                
            } else {
            	$osLstMdl->zeraLista();
            	return;
            }
            
            break;
            
        case self::PESQUISA_OSS_APARELHO_MODELO:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, '%' . $osLstMdl->pegaModelo()->
                pegaAparelhoMdl()->pegaModeloMdl()->pegaNome() . '%');
            break;
        
        case self::PESQUISA_OSS_TOPO5:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_SELECT[$tipoPesquisa]); 
            break;
            
        default:
            throw new TratamentoPrstXmn(array('CARREGA_INFORMACOES'));
        }
        
        try {
            parent::consultaListaModeloXmn(__CLASS__, $osLstMdl, $iprst);
            
        }catch (TratamentoPrstXmn $e) {
            throw $e;
        }
    }
    
    public function armazenaModelo(OsMdl $osMdl, $tipoArmazenamento = null)
    {
        self::inicia();
    
        if ($osMdl->pegaId() > 0) {
            self::alteraModelo($osMdl, $tipoArmazenamento);
        } else {
            self::insereModelo($osMdl, $tipoArmazenamento);
        }
    }
    public function insereModelo(OsMdl $osMdl, $tipoArmazenamento)
    {
        if ($tipoArmazenamento == null) {
            $tipoArmazenamento = self::GRAVACAO_OS;
        }
        
        try {
            self::validaModelo($osMdl);
            
        }catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoArmazenamento) {
        case self::GRAVACAO_OS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_INSERT[$tipoArmazenamento]);
            
            $iprst->atribuiInteiro(1, $osMdl->pegaClienteMdl()->pegaId());
            $iprst->atribuiTexto(2, $osMdl->pegaAparelhoMdl()->pegaHexadecimal(), 
                null);
            $iprst->atribuiTexto(3, $osMdl->pegaAparelhoMdl()->pegaNumeroLinha());
            $iprst->atribuiTexto(4, $osMdl->pegaAparelhoMdl()->pegaPlanoLinha());
            $iprst->atribuiInteiro(5, $osMdl->pegaAparelhoMdl()->pegaOperadoraMdl()
                ->pegaId());
            $iprst->atribuiInteiro(6, $osMdl->pegaAparelhoMdl()->pegaModeloMdl()
                ->pegaId());
            $iprst->atribuiTexto(7, $osMdl->pegaDtEntregaPrevista()
                ->pegaDataHora('Ymd', 'NULL'), 'NULL');
            $iprst->atribuiTexto(8, $osMdl->pegaDtEntrega()
                ->pegaDataHora('Ymd', 'NULL'), 'NULL');
            $iprst->atribuiInteiro(9, $osMdl->pegaEstadoMdl()
                ->pegaId());
            $iprst->atribuiInteiro(10, $osMdl->pegaAtendenteMdl()->pegaId());
            $iprst->atribuiTexto(11, $osMdl->pegaObsCliente());
            $iprst->atribuiTexto(12, $osMdl->pegaObsTecnica());
            $iprst->atribuiTexto(13, $osMdl->pegaDescricaoServico());
            $iprst->atribuiTexto(14, $osMdl->pegaValorServico());
            $iprst->atribuiTexto(15, $osMdl->pegaAparelhoMdl()
                ->pegaEstadoAparelho());
            $iprst->atribuiTexto(16, $osMdl->pegaCustoMaterial());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_INCLUSAO_INVALIDO');
        }
        try {
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn('GRAVACAO_NAO_REALIZADA');
            }
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }
    
    public function alteraModelo(OsMdl $osMdl, $tipoArmazenamento)
    {
        try {
            if ($tipoArmazenamento == null) {
                $tipoArmazenamento = self::GRAVACAO_OS;
            }
            
            self::validaModelo($osMdl);
            
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoArmazenamento) {
        case self::GRAVACAO_OS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_UPDATE[$tipoArmazenamento]);
                
            $iprst->atribuiInteiro(1, $osMdl->pegaClienteMdl()->pegaId());
            $iprst->atribuiTexto(2, $osMdl->pegaAparelhoMdl()
                ->pegaHexadecimal(), 'null');
            $iprst->atribuiTexto(3, $osMdl->pegaAparelhoMdl()
                ->pegaNumeroLinha());
            $iprst->atribuiTexto(4, $osMdl->pegaAparelhoMdl()
                ->pegaPlanoLinha());
            $iprst->atribuiInteiro(5, $osMdl->pegaAparelhoMdl()
                ->pegaOperadoraMdl()->pegaId());
            $iprst->atribuiInteiro(6, $osMdl->pegaAparelhoMdl()
                ->pegaModeloMdl()->pegaId());
            $iprst->atribuiTexto(7, $osMdl->pegaDtEntregaPrevista()
                ->pegaDataHora('Ymd'));
            $iprst->atribuiTexto(8, $osMdl->pegaDtEntrega()
                ->pegaDataHora('Ymd', 'NULL'), 'NULL');
            $iprst->atribuiInteiro(9, $osMdl->pegaEstadoMdl()->pegaId());
            $iprst->atribuiInteiro(10, $osMdl->pegaAtendenteMdl()->pegaId());
            $iprst->atribuiTexto(11, $osMdl->pegaObsCliente());
            $iprst->atribuiTexto(12, $osMdl->pegaObsTecnica());
            $iprst->atribuiTexto(13, $osMdl->pegaDescricaoServico());
            $iprst->atribuiTexto(14, $osMdl->pegaValorServico());
            $iprst->atribuitexto(15, $osMdl->pegaAparelhoMdl()
                ->pegaEstadoAparelho());
            $iprst->atribuiTexto(16, $osMdl->pegaCustoMaterial());
            $iprst->atribuiInteiro(17, $osMdl->pegaId());
            
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_ALTERACAO_INVALIDO');
            
        }
        
        
        if (!$iprst->executa()) {
            throw new TratamentoPrstXmn('ALTERACAO_NAO_REALIZADA');
        }
    }
    
    public static function removeModelo(OsMdl $osMdl, $tipoRemocao = self::REMOVE_OS)
    {
        self::inicia();
        
        switch ($tipoRemocao) {
        case self::REMOVE_OS:
            $iprst = new IntermediarioPrstXmn(
                self::$SQL_DELETE[$tipoRemocao]);
            
            $iprst->atribuiInteiro(1, $osMdl->pegaId());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_EXCLUSAO_INVALIDO');
        }
        
        if (!$iprst->executa()) {
            throw new TratamentoPrstXmn('EXCLUSAO_NAO_REALIZADA');
        }
    }
    
    private function validaModelo(OsMdl $osMdl, $tipoValidacao = null)
    {
        if (is_null($osMdl)) {
            throw new TratamentoPrstXmn('OS_NAO_INFORMADA');
            
        } else if (is_null($osMdl->pegaClienteMdl())) {
            throw new TratamentoPrstXmn('CLIENTE_NAO_INFORMADO');
            
        } else if($osMdl->pegaClienteMdl()->pegaId()<1) {
            throw new TratamentoPrstXmn('CLIENTE_INVALIDO');
            
        } else if (is_null($osMdl->pegaAtendenteMdl())) {
            throw new TratamentoPrstXmn('ATENDENTE_NAO_INFORMADO');
            
        } else if($osMdl->pegaAtendenteMdl()->pegaId() < 1) {
            throw new TratamentoPrstXmn('ATENDENTE_INVALIDO');
            
        } else if (is_null($osMdl->pegaAparelhoMdl())) {
            throw new TratamentoPrstXmn('APARELHO_NAO_INFORMADO');
            
        } else if($osMdl->pegaAparelhoMdl()->pegaHexadecimal() == '') {
            throw new TratamentoPrstXmn('HEXADECIMAL_NAO_INFORMADO');
            
        } else if($osMdl->pegaAparelhoMdl()->pegaNumeroLinha() == '') {
            throw new TratamentoPrstXmn('LINHA_DO_APARELHO_NAO_INFORMADO');
            
        } else if($osMdl->pegaAparelhoMdl()->pegaPlanoLinha() == '') {
            throw new TratamentoPrstXmn('PLANO_DO_APARELHO_NAO_INFORMADO');
            
        } else if(is_null($osMdl->pegaAparelhoMdl()->pegaOperadoraMdl())) {
            throw new TratamentoPrstXmn('OPERADORA_DO_APARELHO_INVALIDA');
            
        } else if($osMdl->pegaAparelhoMdl()->pegaOperadoraMdl()->pegaId() < 1) {
            throw new TratamentoPrstXmn('OPERADORA_DO_APARELHO_NAO_INFORMADA');
            
        } else if(is_null($osMdl->pegaAparelhoMdl()->pegaModeloMdl())) {
            throw new TratamentoPrstXmn('MODELO_APARELHO_INVALIDO');
            
        } else if($osMdl->pegaAparelhoMdl()->pegaModeloMdl()->pegaId() < 1) {
            throw new TratamentoPrstXmn('MODELO_APARELHO_NAO_INFORMADO');
            
        } else if($osMdl->pegaDtEntregaPrevista()->pegaDataHora('timestamp', 
            null) == null) {
             
            throw new TratamentoPrstXmn('DATA_ENTREGA_PREVISTA_OS_INVALIDA');

        } else if($osMdl->pegaEstadoMdl()->pegaId() < 0) {
            throw new TratamentoPrstXmn('ESTADO_OS_NAO_INFORMADO');

        } else if($osMdl->pegaObsCliente() == '') {
            throw new TratamentoPrstXmn('OBS_CLIENTE_NAO_INFORMADO');
        }
    }
}
