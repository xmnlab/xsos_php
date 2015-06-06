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

include_once $SOS   . 'modelo/AtendenteMdl.php';
include_once $SOS   . 'modelo/AtendenteLstMdl.php';
include_once $SOS   . 'persistencia/AtendentePrst.php';

class AtendenteFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoMdl)
    {
        @$pedido     = $direcaoMdl->requisicao['pedido'];
        @$requisicao = $direcaoMdl->requisicao;
        $modoPortal  = true;
        
        switch ($pedido) {
        /* ALTERAR */
        case 'alterar':
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : '0';
            
            $atendenteMdl = new AtendenteMdl();
            $atendenteMdl->atribuiId($identificador);

            try {
                AtendentePrst::consultaModelo($atendenteMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $atendenteLstMdl = new AtendenteLstMdl();
                $mensagemXmn   = new MensagemXMdl();

                $mensagemXmn->atribuiMensagem(
                    $tratXmn->pegaMensagem()
                );
                
                $url = 'atendente/atendente.phtml';
                break;
            }

            $url = 'atendente/cad_atendente.phtml';
            
            MascaraXmn::adicionaModelo($atendenteMdl);
            break;
        	
        /* ARMAZENAR */
        case 'armazenar':
            $atendenteMdl    = new AtendenteMdl();
            $atendenteLstMdl = new AtendenteLstMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador'] : '';
                
            $nome   = isset($requisicao['nome']) ?
                $requisicao['nome']: '';
                
            $login  = isset($requisicao['login']) ?
                $requisicao['login'] : '';
                
            $senha  = isset($requisicao['senha']) ?
                $requisicao['senha'] : '';
                
            $resenha = isset($requisicao['resenha']) ?
                $requisicao['resenha'] : '';
                
            $grupo = isset($requisicao['grupo']) ?
                $requisicao['grupo']   : '';

            $atendenteMdl->atribuiId($identificador);
            $atendenteMdl->atribuiNome($nome);
            $atendenteMdl->atribuiLogin($login);
            $atendenteMdl->atribuiSenha($senha);
            $atendenteMdl->atribuiGrupo($grupo);
            
            if ($senha != $resenha) {
                $tratXmn = new TratamentoXmn(
                    array('codigo' => 'SENHA_NAO_CONFERE'
                ));

            } else if (strlen($senha) < 4) {
                $tratXmn = new TratamentoXmn(
                    array('codigo' => 'SENHA_DEVE_TER_MININO_4_CARACTERES')
                );
            }

            try {
                if (isset($tratXmn)) {
                    throw $tratXmn;
                }
                
                AtendentePrst::armazenaModelo($atendenteMdl,
                    AtendentePrst::GRAVACAO_ATENDENTE
                );
                    
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
              
            return MensageiroXmn::xml($xml, 'SOS');
            
        /* TELA PRINCIPAL */
        case 'listar':
        case '':
            $atendenteLstMdl = new AtendenteLstMdl();
            
            try {
                AtendentePrst::consultaListaModelo($atendenteLstMdl, 
                    AtendentePrst::PESQUISA_ATENDENTES);
                    
            } catch (TratamentoXmn $tratXmn) {
                throw $tratXmn;
            }
            
            $url = 'atendente/atendente.phtml';
            
            MascaraXmn::adicionaModelo($atendenteLstMdl);
            break;
            
        /* PESQUISAR */
        case 'consultar':
            $atendenteMdl    = new AtendenteMdl();
            $atendenteLstMdl = new AtendenteLstMdl();
            
            try {
                $valPesquisa = isset($requisicao['valPesquisa']) ?
                    $requisicao['valPesquisa']  :'';
                    
                $pesquisa    = isset($requisicao['pesquisa'])    ?
                    $requisicao['pesquisa']     :'';
                
                switch ($pesquisa) {
                case 'ativos':
                    $tipoPesquisa = 
                        AtendentePrst::PESQUISA_ATENDENTES_ATIVOS;
                        
                    break;
                    
                case 'login':
                    $atendenteMdl->atribuiLogin($valPesquisa);
                    
                    $tipoPesquisa = 
                        AtendentePrst::PESQUISA_ATENDENTES_LOGIN_APROXIMADO;
                        
                    break;
                    
                case 'nome':
                    $atendenteMdl->atribuiNome($valPesquisa);
                    
                    $tipoPesquisa = 
                        AtendentePrst::PESQUISA_ATENDENTES_NOME_APROXIMADO;
                        
                    break;
                    
                default:
                    $tratXmn = new TratamentoXmn();
                    $mensagemXmn   = new MensagemXMdl();
                    $atendenteLstMdl = new AtendenteLstMdl();

                    $tratXmn->atribuiArquetipo('PARAMETRO_INVALIDO');
                    $tratXmn->atribuiCodigo(
                        'TIPO_PESQUISA_NAO_ENCONTRADO'
                    );
                    
                    $mensagemXmn->atribuiMensagem(
                        $tratXmn->pegaMensagem());
                                                
                    $url = 'atendente/atendente.phtml';
                    break 2;
                }
                
                $atendenteLstMdl->adicionaModelo($atendenteMdl);
                AtendentePrst::consultaListaModelo($atendenteLstMdl, $tipoPesquisa);
                
            } catch (TratamentoXmn $tratXmn) {
                throw $tratXmn;
            }
            
            $lstAtributo = array();
            
            $lstAtributo[] = array('atributo' => 'codigo',
                'metodo' => 'pegaId()');
            $lstAtributo[] = array('atributo' => 'nome',
                'metodo' => 'pegaNome()');
            $lstAtributo[] = array('atributo' => 'login',
                'metodo' => 'pegaLogin()');
            $lstAtributo[] = array('atributo' => 'grupo',
                'metodo' => 'pegaGrupo()');
            
            $xml = ListaModeloXmn::pegaXml($atendenteLstMdl, $lstAtributo);
            
            unset($atendenteMdl);
            unset($atendenteLstMdl);
            
            return MensageiroXmn::xml($xml, 'SOS');
            
            
        /* INCLUIR */
        case 'incluir':
            $atendenteMdl   = new AtendenteMdl();
            $url            = 'atendente/cad_atendente.phtml';
            
            MascaraXmn::adicionaModelo($atendenteMdl);
            break;
            
        
        /* REMOVER */
        case 'remover':
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador']:'0';

            $atendenteMdl = new AtendenteMdl();
            $atendenteMdl->atribuiId($identificador);
            
            $atendenteLstMdl = new AtendenteLstMdl();
            
            try {
                AtendentePrst::removeModelo($atendenteMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
        
        default:
            $atendenteLstMdl = new AtendenteLstMdl();
            $mensagemXMdl    = new MensagemXMdl();
            $tratXmn         = new TratamentoXmn();

            $tratXmn->atribuiArquetipo('SUBCONTROLLER_INVALIDO');
            $tratXmn->atribuiCodigo('INFORME_SUBCONTROLLER_VALIDA');
            
            $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
            
            MascaraXmn::adicionaModelo($mensagemXMdl);
            MascaraXmn::adicionaModelo($atendenteLstMdl);
            
            $url = 'atendente/atendente.phtml';
            break;
        }
        
        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
    
}
