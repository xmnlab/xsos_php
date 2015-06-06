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

include_once $SOS . 'modelo/MarcaMdl.php';
include_once $SOS . 'modelo/MarcaLstMdl.php';
include_once $SOS . 'persistencia/MarcaPrst.php';

class MarcaFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoMdl)
    {
        @$pedido     = $direcaoMdl->requisicao['pedido'];
        @$requisicao = $direcaoMdl->requisicao;
        
        $url        = '';
        $modoPortal = true;

        switch ($pedido) {
        /* ARMAZENAR */
        case 'armazenar':
            $marcaCopiaMdl = new MarcaMdl();
            $marcaMdl      = new MarcaMdl();
            $marcaLstMdl   = new MarcaLstMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador']:'';
            $nome = isset($requisicao['nome']) ? $requisicao['nome']:'';

            $marcaMdl->atribuiId($identificador);
            $marcaMdl->atribuiNome($nome);

            try {
                MarcaPrst::armazenaModelo($marcaMdl);
                $marcaLstMdl->adicionaModelo($marcaMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
            
        /* LISTAR */
        case '':
        case 'listar':
            $marcaLstMdl = new MarcaLstMdl();
            $marcaMdl = new MarcaMdl();
            
            try{
                MarcaPrst::consultaListaModelo($marcaLstMdl, 
                    MarcaPrst::PESQUISA_MARCAS);
            }catch (TratamentoXmn $tratXmn){
                throw $tratXmn;
            }
            
            MascaraXmn::adicionaModelo($marcaLstMdl);
            $url = 'marca/marca.phtml';
            break;
            
        /* PESQUISAR */
        case 'consultar':
            $marcaMdl    = new MarcaMdl();
            $marcaLstMdl = new MarcaLstMdl();
            
            try {
                $valPesquisa  = isset($requisicao['valPesquisa']) ? 
                    $requisicao['valPesquisa'] : '';
                $pesquisa     = isset($requisicao['pesquisa'])    ? 
                    $requisicao['pesquisa']    : '';
                $tipoPesquisa = '';
                
                switch ($pesquisa){
                case 'nome':
                    $marcaMdl->atribuiNome($valPesquisa);
                    $tipoPesquisa = MarcaPrst::PESQUISA_MARCAS_NOME_APROXIMADO;
                    break;
                    
                case 'ativos':
                    $marcaMdl->atribuiNome($valPesquisa);
                    $tipoPesquisa = MarcaPrst::PESQUISA_MARCAS_ATIVAS;
                    break;
                    
                default:
                    $xml  = '<processo estado="false"' 
                          . ' mensagem="PARAMETRO_PESQUISA_INVALIDO"/>';
                  
                    return MensageiroXmn::xml($xml, 'SOS');
                }
                
                $marcaLstMdl->adicionaModelo($marcaMdl);
                MarcaPrst::consultaListaModelo($marcaLstMdl, $tipoPesquisa);
                
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
            
            $xml = ListaModeloXmn::pegaXml($marcaLstMdl, $lstAtributo);
            
            unset($marcaMdl);
            unset($marcaLstMdl);
            
            return MensageiroXmn::xml($xml, 'SOS');
        
        /* INCLUIR */
        case 'incluir':
            $marcaMdl = new MarcaMdl();
            $url      = 'marca/cad_marca.phtml';
            
            MascaraXmn::adicionaModelo($marcaMdl);
            break;
            
        /* ALTERAR */
        case 'alterar':
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador']:'0';
            
            $marcaMdl = new MarcaMdl();
            $marcaMdl->atribuiId($identificador);
            
            try {
                MarcaPrst::consultaModelo($marcaMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $mensagemXMdl = new MensagemMdl();
                $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
                $marcaLstMdl = new MarcaLstMdl();
                
                MascaraXmn::exibeMascara('marca/marca.phtml');
                return;
            }
            $url = 'marca/cad_marca.phtml';
            
            MascaraXmn::adicionaModelo($marcaMdl);
            break;
            
        /* REMOVER */
        case 'remover':
            $marcaLstMdl = new MarcaLstMdl();
            $marcaMdl    = new MarcaMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador']:'0';
            $marcaMdl->atribuiId($identificador);
            
            try {
                MarcaPrst::removeModelo($marcaMdl);
                
            } catch(TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');

        default:
            $tratXmn = new TratamentoXmn('INFORME_UM_PARAMETRO_VALIDO');

            $mensagemXMdl = new MensagemXMdl();
            $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
            
            MascaraXmn::adicionaModelo($mensagemXMdl);
            
            self::mudaFluxo('sos.php?entidade=marca&pedido=listar');
        }
        
        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
}