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

include_once $SOS . 'modelo/EstadoMdl.php';
include_once $SOS . 'modelo/EstadoLstMdl.php';
include_once $SOS . 'persistencia/EstadoOsPrst.php';

class EstadoOsFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoMdl)
    {
        @$pedido     = $direcaoMdl->requisicao['pedido'];
        @$requisicao = $direcaoMdl->requisicao;
        $modoPortal = true;

        switch ($pedido) {
        /* ALTERAÇÃO */
        case 'alterar':
            $estadoMdl = new EstadoMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : '0';
            
            $estadoMdl->atribuiId($identificador);
            
            try {
                EstadoOsPrst::consultaModelo($estadoMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $mensagemXMdl = new MensagemXMdl();
                $estadoLstMdl = new EstadoLstMdl();

                $mensagemXMdl->atribuiMensagem(
                    $tratXmn->pegaMensagem()
                );

                $url = 'estado_os/estado_os.phtml';
                
                MascaraXmn::adicionaModelo($estadoLstMdl);
                MascaraXmn::adicionaModelo($mensagemXMdl);
                break;
            }

            $url = 'estado_os/cad_estado_os.phtml';
            
            MascaraXmn::adicionaModelo($estadoMdl);
            break;
        
        /* ARMAMZENAR*/
        case 'armazenar':
            $estadoMdl    = new EstadoMdl();
            $estadoLstMdl = new EstadoLstMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador'] : '';

            $nome = isset($requisicao['nome']) ?
                $requisicao['nome'] : '';

            $estadoMdl->atribuiId($identificador);
            $estadoMdl->atribuiNome($nome);

            try {
                EstadoOsPrst::armazenaModelo($estadoMdl);

            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
        
        /* LISTA DE ESTADOS DE ORDEM DE SERVIÇO */
        case 'listar':
        case '':
            $estadoLstMdl = new EstadoLstMdl();
            try {
                EstadoOsPrst::consultaListaModelo($estadoLstMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                throw $tratXmn;
            }
            
            $url = 'estado_os/estado_os.phtml';
            
            MascaraXmn::adicionaModelo($estadoLstMdl);
            break;

        /* PESQUISA */
        case 'consultar':
            $estadoLstMdl = new EstadoLstMdl();
            $estadoMdl    = new EstadoMdl();
            
            try {
                $valPesquisa  = isset($requisicao['valPesquisa']) ? 
                    $requisicao['valPesquisa'] : '';

                $pesquisa     = isset($requisicao['pesquisa'])    ?
                    $requisicao['pesquisa']    : '';

                $tipoPesquisa = 0;
                
                switch ($pesquisa) {
                case 'nome':
                    $tipoPesquisa = 
                        EstadoOsPrst::PESQUISA_ESTADOS_NOME_APROXIMADO;

                    $estadoMdl->atribuiNome($valPesquisa);
                    break;
                    
                case 'ativos':
                    $tipoPesquisa = 
                        EstadoOsPrst::PESQUISA_ESTADOS_ATIVOS;

                    $estadoMdl->atribuiNome($valPesquisa);
                    break;

                default:
                    $tipoPesquisa = EstadoOsPrst::PESQUISA_ESTADOS;
                    break;
                }

                $estadoLstMdl->adicionaModelo($estadoMdl);
                EstadoOsPrst::consultaListaModelo($estadoLstMdl, $tipoPesquisa);

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
            
            $xml = ListaModeloXmn::pegaXml($estadoLstMdl, $lstAtributo);
            
            unset($estadoMdl);
            unset($estadoLstMdl);
            
            return MensageiroXmn::xml($xml, 'SOS');
            
        /* INCLUSÃO */
        case 'incluir':
            $estadoMdl  = new EstadoMdl();
            $url        = 'estado_os/cad_estado_os.phtml';
            
            MascaraXmn::adicionaModelo($estadoMdl);
            break;

        /* REMOVER */
        case 'remover':
            $estadoMdl    = new EstadoMdl();
            $estadoLstMdl = new EstadoLstMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador']:'0';

            $estadoMdl->atribuiId($identificador);
            
            try {
                EstadoOsPrst::removeModelo($estadoMdl);

            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            unset($estadoMdl);

            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');

        default:
            throw new TratamentoXmn();
            
        }

        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
}