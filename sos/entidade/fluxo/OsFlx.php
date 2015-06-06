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
 * @category   fluxo
 * @package    sos
 * @subpackage sos.fluxo
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

include_once $SOS . 'modelo/OsMdl.php';
include_once $SOS . 'modelo/OsLstMdl.php';
include_once $SOS . 'persistencia/OsPrst.php';
include_once $SOS . 'persistencia/EstadoOsPrst.php';

class OsFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoXMdl)
    {
        $requisicao = &$direcaoXMdl->requisicao;
        $pedido     = &$requisicao['pedido'];
        $modoPortal = true;
        
        if (isset($requisicao['mensagem'])) {
            $mensagemXMdl = new MensagemXMdl();
            $mensagemXMdl->atribuiMensagem($requisicao['mensagem']);
            MascaraXmn::adicionaModelo($mensagemXMdl);
        }

        switch ($pedido){
        /* LISTA */
        case 'listar':
        case '':
            $osLstMdl     = new OsLstMdl();
            $estadoMdl    = new EstadoMdl();
            $estadoLstMdl = new EstadoLstMdl();

            try {
                EstadoOsPrst::consultaListaModelo($estadoLstMdl);
            } catch (TratamentoXmn $tratamentoXmn){
                throw $tratamentoXmn;
            }
            
            $url = 'os/os.phtml';
            
            MascaraXmn::adicionaModelo($osLstMdl);
            MascaraXmn::adicionaModelo($estadoLstMdl);
            break;
            
        /* PESQUISA*/
        case 'consultar':
            $osLstMdl = new OsLstMdl();
            $osMdl    = new OsMdl();
            $estadoMdl    = new EstadoMdl();
            $estadoLstMdl = new EstadoLstMdl();
                
            try {
                $valPesquisa = isset($requisicao['valPesquisa']) ?
                $requisicao['valPesquisa']:'';

                $pesquisa = isset($requisicao['pesquisa']) ?
                $requisicao['pesquisa']:'';
                        
                $tipoPesquisa = '';
                    
                switch($pesquisa){
                case 'os':
                    $osMdl->atribuiId($valPesquisa);
                    $tipoPesquisa = OsPrst::PESQUISA_OS_CODIGO;
                    break;
                        
                case 'cliente':
                    $osMdl->pegaClienteMdl()->atribuiNome($valPesquisa);
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_CLIENTE_NOME;
                    break;
                    
                case 'topo5':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_TOPO5;
                    break;
                
                case 'orcamento':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_ORCAMENTO;
                    break;
                        
                case 'orcamentoFinalizado':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_ORCAMENTO_FINALIZADO;
                    break;
                        
                case 'garantia':
                   $tipoPesquisa = OsPrst::PESQUISA_OSS_GARANTIA;
                   break;
                        
                case 'execucao':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_EXECUCAO;
                    break;
                    
                case 'execucaoFinalizada':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_EXECUCAO_FINALIZADA;
                    break;
                    
                case 'reprovado':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_REPROVADO;
                    break;
                    
                case 'entregue':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_ENTREGUE;
                    break;
                    
                case 'hexadecimal':
                    $osMdl->pegaAparelhoMdl()->atribuiHexadecimal($valPesquisa);
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_APARELHO_HEXADECIMAL;
                    break;
                    
                case 'marca':
                    $osMdl->pegaAparelhoMdl()->pegaModeloMdl()->
                        pegaMarcaMdl()->atribuiNome($valPesquisa);
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_APARELHO_MARCA;
                    break;
                    
                case 'modelo':
                	$osMdl->pegaAparelhoMdl()->pegaModeloMdl()->
                        atribuiNome($valPesquisa);
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_APARELHO_MODELO;
                    break;
                    
                case 'retornosHoje':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_RETORNO_HOJE;
                    break;
                    
                case 'retornosAtrasados':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_RETORNO_ATRASADO;
                    break;
                    
                case 'retornosPara':
                    $tipoPesquisa = OsPrst::PESQUISA_OSS_RETORNO_PARA;
                    break;
                    
                default:
                    throw new TratamentoXmn();
                }

                $osLstMdl->adicionaModelo($osMdl);
                OsPrst::consultaListaModelo($osLstMdl, $tipoPesquisa);
                
            } catch (TratamentoXmn $tratamentoXmn){
                throw $tratamentoXmn;
            }
            
            $lstAtributo = array();
            
            $lstAtributo[] = array(
                'atributo' => 'codigo',
                'metodo'   => 'pegaId()');
            $lstAtributo[] = array(
                'atributo' => 'cliente',
                'metodo'   => 'pegaClienteMdl()->pegaNome()');
            $lstAtributo[] = array(
                'atributo' => 'estado',
                'metodo'   => 'pegaEstadoMdl()->pegaNome()');
            $lstAtributo[] = array(
                'atributo' => 'dtAbertura',
                'metodo'   => 'pegaDtAbertura()->pegaDataHora("d/m/Y", null)');
            $lstAtributo[] = array(
                'atributo' => 'dtEntPrevista',
                'metodo'   => 
                    'pegaDtEntregaPrevista()->pegaDataHora("d/m/Y", null)');
            $lstAtributo[] = array(
                'atributo' => 'dtEntrega',
                'metodo'   => 'pegaDtEntrega()->pegaDataHora("d/m/Y", "")');
            $lstAtributo[] = array(
                'atributo' => 'marca',
                'metodo'   => 'pegaAparelhoMdl()->pegaModeloMdl()->'
                    . 'pegaMarcaMdl()->pegaNome()');
            $lstAtributo[] = array(
                'atributo' => 'modelo',
                'metodo'   => 'pegaAparelhoMdl()->pegaModeloMdl()->pegaNome()');
            $lstAtributo[] = array(
                'atributo' => 'hexadecimal',
                'metodo' =>   'pegaAparelhoMdl()->pegaHexadecimal()');
            $lstAtributo[] = array(
                'atributo' => 'linha',
                'metodo'   => 'pegaAparelhoMdl()->pegaNumeroLinha()');
            
            $xml  = '';
            $xml .= ListaModeloXmn::pegaXml($osLstMdl, $lstAtributo);
            
            unset($osMdl);
            unset($osLstMdl);
            
            return MensageiroXmn::xml($xml, 'SOS');

            
        //INCLUIR NOVA OS DO CLIENTE
        case 'incluirParaNovoCliente':
        case 'incluir':
            $osMdl = new OsMdl();
            
            if (isset($requisicao['cliente'])
              && (int) $requisicao['cliente'] > 0) {
                
                $clienteMdl = new ClienteMdl();
                
                $clienteMdl->atribuiId($requisicao['cliente']);
                $osMdl->atribuiClienteMdl($clienteMdl);
                
            }
            
            $osMdl->pegaDtEntregaPrevista()->atribuiDataHora(
                time() + (5 * 60 * 60 * 24));
            
            MascaraXmn::adicionaModelo($osMdl);
            
            $url = 'os/cad_os.phtml';
            break;
            
        //ALTERAR
        case 'alterar':
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador'] : '0';

            $atendenteLstMdl = new AtendenteLstMdl();
            $clienteLstMdl   = new ClienteLstMdl();
            $operadoraLstMdl = new OperadoraLstMdl();
            $osMdl           = new OsMdl();
            $modeloLstMdl    = new ModeloLstMdl();
            $marcaLstMdl     = new MarcaLstMdl();
            $estadoLstMdl    = new EstadoLstMdl();

            $osMdl->atribuiId($identificador);
            
            try {
                OsPrst::consultaModelo($osMdl);
                
                $modeloLstMdl->adicionaModelo($osMdl->pegaAparelhoMdl()
                    ->pegaModeloMdl());

                
            } catch (TratamentoPrstXmn $tratXmn){
                throw $tratXmn;
            }
            
            $clienteLstMdl->adicionaModelo($osMdl->pegaClienteMdl());
            
            MascaraXmn::adicionaModelo($osMdl);
            
            $url = 'os/cad_os.phtml';
            break;
            
        /* REMOVER */
        case 'remover':
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador']:'0';
            
            $osMdl = new OsMdl();
            $osMdl->atribuiId($identificador);
            
            try {
                OsPrst::removeModelo($osMdl);
                
            } catch (TratamentoXmn $tratXmn){
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
            
        /* ARMAZENAR */
        case 'armazenar':
            $clienteMdl   = new ClienteMdl();
            $modeloMdl    = new ModeloMdl();
            $operadoraMdl = new OperadoraMdl();
            $aparelhoMdl  = new AparelhoMdl();
            $atendenteMdl = new AtendenteMdl();
            $osMdl        = new OsMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador']    : '';

            $cliente   = isset($requisicao['cliente']) ?
                $requisicao['cliente']          : '';

            $custoMaterial = isset($requisicao['custoMaterial']) ?
                $requisicao['custoMaterial']     : '';
            $custoMaterial = str_replace('R$ ', '', $custoMaterial);
            $custoMaterial = str_replace(',', '.', $custoMaterial);
            $custoMaterial = (float) $custoMaterial;
                
            $atendente = isset($requisicao['atendente']) ?
                $requisicao['atendente']        : '';
            
            $hexadecimalAparelho =  isset($requisicao['hexadecimal']) ?
                $requisicao['hexadecimal']      : '';
                
            $numeroLinhaAparelho =  isset($requisicao['numeroLinha']) ?
                $requisicao['numeroLinha']      : '';
                
            $modeloAparelho =  isset($requisicao['modelo']) ?
                $requisicao['modelo']           : '';
                
            $marcaAparelho =  isset($requisicao['marca']) ?
                $requisicao['marca']            : '';
                
            $operadoraAparelho =  isset($requisicao['operadora']) ?
                $requisicao['operadora']        : '';
                
            $planoAparelho =  isset($requisicao['planoLinha']) ?
                $requisicao['planoLinha']       : '';
                
            $estadoAparelho =  isset($requisicao['estadoAparelho']) ?
                $requisicao['estadoAparelho']   : '';
            
            $dtAbertura = isset($requisicao['dtAbertura']) ?
                $requisicao['dtAbertura']       : '';
                
            $dtEntregaPrevista = isset($requisicao['dtEntregaPrevista']) ?
                $requisicao['dtEntregaPrevista'] : '';
                
            $dtEntrega = isset($requisicao['dtEntrega']) ?
                $requisicao['dtEntrega']        : '';
           
            $estadoOs = isset($requisicao['estado']) ?
                $requisicao['estado']         : '';
                
            $obsCliente = isset($requisicao['obsCliente']) ?
                $requisicao['obsCliente']       : '';
                
            $obsTecnica = isset($requisicao['obsTecnica']) ?
                $requisicao['obsTecnica']       : '';
                
            $descricaoServico = isset($requisicao['descricaoServico']) ?
                $requisicao['descricaoServico'] : '';

            $valorServico = isset($requisicao['valorServico']) ?
                $requisicao['valorServico']     : '';
                
            $valorServico = str_replace('R$ ', '', $valorServico);
            $valorServico = str_replace(',', '.', $valorServico);
            $valorServico = (float) $valorServico;

            $osMdl->atribuiId($identificador);
            $osMdl->atribuiCustoMaterial($custoMaterial);
            $osMdl->atribuiObsCliente($obsCliente);
            $osMdl->atribuiObsTecnica($obsTecnica);
            $osMdl->atribuiDescricaoServico($descricaoServico);
            $osMdl->atribuiValorServico($valorServico);
            
            $osMdl->pegaDtAbertura()->atribuiDataHora($dtAbertura, 'd/m/y');
            $osMdl->pegaDtEntregaPrevista()->atribuiDataHora($dtEntregaPrevista, 'd/m/y');

            $osMdl->pegaDtEntrega()->atribuiDataHora($dtEntrega, 'd/m/y');
            
            $osMdl->pegaEstadoMdl()->atribuiId($estadoOs);
            
            $operadoraMdl->atribuiId($operadoraAparelho);
            $atendenteMdl->atribuiId($atendente);
            $clienteMdl->atribuiId($cliente);
            $modeloMdl->atribuiId($modeloAparelho);
            $modeloMdl->pegaMarcaMdl()->atribuiId($marcaAparelho);
            
            $aparelhoMdl->atribuiHexadecimal($hexadecimalAparelho);
            $aparelhoMdl->atribuiNumeroLinha($numeroLinhaAparelho);
            $aparelhoMdl->atribuiModeloMdl($modeloMdl);
            $aparelhoMdl->atribuiOperadoraMdl($operadoraMdl);
            $aparelhoMdl->atribuiPlanoLinha($planoAparelho);
            $aparelhoMdl->atribuiEstadoAparelho($estadoAparelho);
            
            $osMdl->atribuiAparelhoMdl($aparelhoMdl);
            $osMdl->atribuiClienteMdl($clienteMdl);
            $osMdl->atribuiAtendenteMdl($atendenteMdl);
            
            try {
                OsPrst::armazenaModelo($osMdl);
                
            } catch(TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
        
        /* IMPRIMIR */
        case 'imprimir':
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : '0';
                
            $osMdl = new OsMdl();
            $osMdl->atribuiId($identificador);
            
            try {
                OsPrst::consultaModelo($osMdl);
            } catch (TratamentoXmn $tratamentoXmn){
                throw $tratamentoXmn;
            }
            
            try {
            
            } catch (TratamentoXmn $tratamentoXmn){
                $tratamentoXmn->atribuiSistema('os','');
                throw $tratamentoXmn;
            }
            
            $url        = 'os/protocolo_os.phtml';
            $modoPortal = false;
            
            MascaraXmn::adicionaModelo($osMdl);
            break;
 
       /* IMPRIMIR */
        case 'imprimir-mini':
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : '0';
                
            $osMdl = new OsMdl();
            $osMdl->atribuiId($identificador);
            
            try {
                OsPrst::consultaModelo($osMdl);
            } catch (TratamentoXmn $tratamentoXmn){
                throw $tratamentoXmn;
            }
            
            try {
            
            } catch (TratamentoXmn $tratamentoXmn){
                $tratamentoXmn->atribuiSistema('os','');
                throw $tratamentoXmn;
            }
            
            $url        = 'os/mini_protocolo.php';
            $modoPortal = false;
            
            MascaraXmn::adicionaModelo($osMdl);
            break;
           
        default:
            throw new TratamentoXmn(
                array('codigo' => 'PEDIDO_NAO_REALIZADO'));
        }
        
        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
}
