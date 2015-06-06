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

include_once $SOS   . 'modelo/OperadoraMdl.php';
include_once $SOS   . 'modelo/OperadoraLstMdl.php';
include_once $SOS   . 'persistencia/OperadoraPrst.php';

class OperadoraFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoMdl)
    {
        @$pedido     = $direcaoMdl->requisicao['pedido'];
        @$requisicao = $direcaoMdl->requisicao;
        $modoPortal = true;

        switch ($pedido) {
        /* LISTAR */
        case 'listar':
        case '':
            $operadoraLstMdl = new OperadoraLstMdl();
            try {
                OperadoraPrst::consultaListaModelo($operadoraLstMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                throw $tratXmn;
            }
            
            $url = 'operadora/operadora.phtml';
            
            MascaraXmn::adicionaModelo($operadoraLstMdl);
            break;
            
        /* PESQUISAR */
        case 'consultar':
            $operadoraLstMdl = new OperadoraLstMdl();
            $operadoraMdl    = new OperadoraMdl();
            
            try {
                $valPesquisa  = isset($requisicao['valPesquisa']) ? 
                    $requisicao['valPesquisa'] : '';
                $pesquisa     = isset($requisicao['pesquisa'])    ? 
                    $requisicao['pesquisa']    : '';
                $tipoPesquisa = 0;
                
                switch ($pesquisa) {
                case 'nome':
                    $tipoPesquisa = 
                        OperadoraPrst::PESQUISA_OPERADORAS_NOME_APROXIMADO;
                    $operadoraMdl->atribuiNome($valPesquisa);
                    break;
                    
                case 'ativos':
                    $tipoPesquisa = 
                        OperadoraPrst::PESQUISA_OPERADORAS_ATIVAS;
                    $operadoraMdl->atribuiNome($valPesquisa);
                    break;
                    
                default:
                    $tipoPesquisa = 0;
                    break;
                }

                $operadoraLstMdl->adicionaModelo($operadoraMdl);
                OperadoraPrst::consultaListaModelo($operadoraLstMdl, $tipoPesquisa);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $lstAtributo = array();
            $lstAtributo[] = array(
                'atributo' => 'codigo',
                'metodo'   => 'pegaId()'
            );
            $lstAtributo[] = array(
                'atributo' => 'nome',
                'metodo'   => 'pegaNome()'
            );
            
            $xml = ListaModeloXmn::pegaXml($operadoraLstMdl, $lstAtributo);
            
            unset($operadoraMdl);
            unset($operadoraLstMdl);
            
            return MensageiroXmn::xml($xml, 'SOS');
          
        /* INCLUIR */
        case 'incluir':
            $operadoraMdl = new OperadoraMdl();
            $url          = 'operadora/cad_operadora.phtml';
            
            MascaraXmn::adicionaModelo($operadoraMdl);
            break;
            
        /* ALTERAR */
        case 'alterar':
            $operadoraMdl = new OperadoraMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador'] : '0';
            
            $operadoraMdl->atribuiId($identificador);
            
            try {
                OperadoraPrst::consultaModelo($operadoraMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                self::mudaFluxo('sos.php?entidade=operadora&pedido=listar&' 
                    . 'mensagem=' . $tratXmn->pegaMensagem());
                return;
            }

            $url = 'operadora/cad_operadora.phtml';
            
            MascaraXmn::adicionaModelo($operadoraMdl);
            break;
            
        /* REMOVER */
        case 'remover':
            $operadoraMdl    = new OperadoraMdl();
            $operadoraLstMdl = new OperadoraLstMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador']:'0';
            $operadoraMdl->atribuiId($identificador);
            
            try {
                OperadoraPrst::removeModelo($operadoraMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
            
        /* ARMAZENAR */
        case 'armazenar':
            $operadoraMdl    = new OperadoraMdl();
            $operadoraLstMdl = new OperadoraLstMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador'] : '';
            $nome          = isset($requisicao['nome'])          ? 
                $requisicao['nome']          : '';

            $operadoraMdl->atribuiId($identificador);
            $operadoraMdl->atribuiNome($nome);
            
            try {
                OperadoraPrst::armazenaModelo($operadoraMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
            
        default:
            throw new TratamentoXmn();

        }

        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
}