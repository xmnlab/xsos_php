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

include_once $SOS   . 'modelo/ModeloMdl.php';
include_once $SOS   . 'persistencia/ModeloPrst.php';

class ModeloFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoMdl)
    {
        @$pedido     = $direcaoMdl->requisicao['pedido'];
        @$requisicao = $direcaoMdl->requisicao;
        $modoPortal = true;

        switch ($pedido) {
        /* ARMAZENAR */
        case 'armazenar':
            $modeloMdl    = new ModeloMdl();
            $modeloLstMdl = new ModeloLstMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador'] : '';
            $nome          = isset($requisicao['nome'])          ? 
                $requisicao['nome']          : '';
            $marca         = isset($requisicao['marca'])         ? 
                $requisicao['marca']         : '';

            $modeloMdl->atribuiId($identificador);
            $modeloMdl->atribuiNome($nome);
            $modeloMdl->pegaMarcaMdl()->atribuiId($marca);
            
            if (isset($direcaoMdl->artefato['imagem']) 
                && !is_null($direcaoMdl->artefato['imagem'])
                && !empty($direcaoMdl->artefato['imagem']['tmp_name'])) {
                    
                $gravuraModeloXMdl = new GravuraXMdl();

                $gravuraModeloXMdl->atribuiDetalhes(
                      'localizacao='
                    . $direcaoMdl->artefato['imagem']['tmp_name'] .';');
                GravuraXmn::carrega($gravuraModeloXMdl);
                
                GravuraXmn::redimensiona($gravuraModeloXMdl, 
                    'altura=400;largura=600;');
                
                $modeloMdl->atribuiGravuraXMdl($gravuraModeloXMdl);
            }
            
            try {
                ModeloPrst::armazenaModelo($modeloMdl);
                
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
            $modeloLstMdl = new ModeloLstMdl();
            $url = 'modelo/modelo.phtml';
            
            MascaraXmn::adicionaModelo($modeloLstMdl);
            break;
            
        /* CONSULTA POR FILTRO */
        case 'consultar':
            $modeloLstMdl = new ModeloLstMdl();
            $modeloMdl    = new ModeloMdl();

            $pesquisa = isset($requisicao['pesquisa'])     ? 
                $requisicao['pesquisa']     : '';
            $valor    = isset($requisicao['valPesquisa']) ? 
                $requisicao['valPesquisa']  : '';

            switch ($pesquisa) {
            case 'marca':
                $modeloMdl->pegaMarcaMdl()->atribuiNome($valor);
                $modeloLstMdl->adicionaModelo($modeloMdl);
                $tipoPesquisa = ModeloPrst::PESQUISA_MODELOS_MARCA_APROXIMADA;
                break;
                
            case 'marca_codigo':
                $modeloMdl->pegaMarcaMdl()->atribuiId($valor);
                $modeloLstMdl->adicionaModelo($modeloMdl);
                $tipoPesquisa = ModeloPrst::PESQUISA_MODELOS_MARCA_CODIGO;
                break;
                
            case 'nome':
                $modeloMdl->atribuiNome($valor);
                $modeloLstMdl->adicionaModelo($modeloMdl);
                $tipoPesquisa = ModeloPrst::PESQUISA_MODELOS_NOME_APROXIMADO;
                break;
            
            default:
                $xml = '<processo estado="false" mensagem="INFORME_UMA_PESQUISA_VALIDA"/>';
                return MensageiroXmn::xml($xml, 'SOS');
            }

            try {
                ModeloPrst::consultaListaModelo($modeloLstMdl, $tipoPesquisa);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml .= '<processo estado="false" mensagem="' 
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
            $lstAtributo[] = array(
                'atributo' => 'marca',
                'metodo'   => 'pegaMarcaMdl()->pegaNome()'
            );
            
            $xml = ListaModeloXmn::pegaXml($modeloLstMdl, $lstAtributo);
            
            unset($modeloMdl);
            unset($modeloLstMdl);
            
             return MensageiroXmn::xml($xml, 'SOS');
        
        /* EXIBIR FOTO */
        case 'exibirFoto':
            $modeloMdl = new ModeloMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : 0;
            
            $modeloMdl->atribuiId($identificador);
            try {
                ModeloPrst::consultaModelo($modeloMdl, 
                    ModeloPrst::PESQUISA_MODELO_IMAGEM_CODIGO);

            } catch (TratamentoPrstXmn $tratXmn) {
                return;
            }
            
            header("Content-type: image/jpeg");
            echo $modeloMdl->pegaGravuraXMdl()->pegaConteudo();
            return;
            
        /* INCLUIR */
        case 'incluir':
            $marcaLstMdl = new MarcaLstMdl();
            
            try {
                MarcaPrst::consultaListaModelo($marcaLstMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $mensagemXMdl   = new MensagemXMdl();
                $modeloLstMdl    = new ModeloLstMdl();

                $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
                
                $url = 'modelo/modelo.phtml';
                MascaraXmn::adicionaModelo($marcaLstMdl);
                break;
            }
            
            $url = 'modelo/cad_modelo.phtml';
            
            MascaraXmn::adicionaModelo($marcaLstMdl);
            MascaraXmn::adicionaModelo(new ModeloMdl());
            break;
            
        /* ALTERAR */
        case 'alterar':
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : 0;
            
            $modeloMdl = new ModeloMdl();
            $modeloMdl->atribuiId($identificador);
            
            try {
                ModeloPrst::consultaModelo($modeloMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $mensagemXMdl   = new MensagemXMdl();
                $modeloLstMdl    = new ModeloLstMdl();

                $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
                
                include_once SosXmn::pegaRaizMascaraFisica() . 'modelo/modelo.phtml';
                
                return;
            }

            $marcaLstMdl = new MarcaLstMdl();
            
            try {
                MarcaPrst::consultaListaModelo($marcaLstMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $mensagemXMdl   = new MensagemXMdl();
                $modeloLstMdl    = new ModeloLstMdl();

                $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
                
                $url = 'modelo/modelo.phtml';
                break;
            }

            $url = 'modelo/cad_modelo.phtml';
            
            MascaraXmn::adicionaModelo($modeloMdl);
            MascaraXmn::adicionaModelo($marcaLstMdl);
            break;
            
        /* REMOVER */
        case 'remover':
            $modeloMdl = new ModeloMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : 0;
            
            $modeloMdl->atribuiId($identificador);
            
            try{
                ModeloPrst::removeModelo($modeloMdl);
                
            }catch (TratamentoXmn $tratXmn){
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
               
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');
        
        /*
         * REMOVER FOTO
         */
        case 'removerFoto':
            $modeloMdl = new ModeloMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : 0;
            
            $modeloMdl->atribuiId($identificador);
            
            try{
                ModeloPrst::armazenaModelo($modeloMdl,
                    ModeloPrst::REMOVE_FOTO);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<resposta>falha</resposta>';
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<resposta>concluido</resposta>';
            return MensageiroXmn::xml($xml, 'SOS');
        
        /*
         * VERIFICAR FOTO
         */
        case 'verificarFoto':
            $modeloMdl = new ModeloMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : '0';
            
            $modeloMdl->atribuiId($identificador);
            
            try{
                ModeloPrst::consultaModelo($modeloMdl,
                    ModeloPrst::PESQUISA_MODELO_VERIFICA_IMAGEM_CODIGO);
                
            }catch (TratamentoXmn $tratXmn){
                $xml =  '<resposta>falha</resposta>';
                return MensageiroXmn::xml($xml, 'SOS');
            }

            if ($modeloMdl->pegaNome() == '') {
                $xml = '<resposta>falha</resposta>';
                return MensageiroXmn::xml($xml, 'SOS');
            }

            $xml  = '<imagemXml>';
            $xml .= '<codigo>' . $modeloMdl->pegaId() . '</codigo>';
            $xml .= 
                  '<direcao>' 
                . 'sos.php?entidade=modelo&amp;pedido=exibirFoto&amp;'
                . 'identificador=' . $modeloMdl->pegaId() 
                . '</direcao>';
            $xml .= '<titulo>' . $modeloMdl->pegaNome() . '</titulo>';
            $xml .= '</imagemXml>';
            
            return MensageiroXmn::xml($xml, 'SOS');
            
        default:
            $mensagemXMdl    = new MensagemXMdl();
            $modeloLstMdl    = new ModeloLstMdl();

            $tratXmn   = new TratamentoXmn('INFORME_UM_PEDIDO_VALIDO');
            
            $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
            
            $url = 'modelo/modelo.phtml';
            break;
        }
        
        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
}