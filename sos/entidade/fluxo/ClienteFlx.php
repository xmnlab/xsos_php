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

include_once $SOS . 'modelo/ClienteMdl.php';
include_once $SOS . 'persistencia/ClientePrst.php';

class ClienteFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoMdl)
    {
        @$pedido     = &$direcaoMdl->requisicao['pedido'];
        @$requisicao = &$direcaoMdl->requisicao;
        $modoPortal = true;
        
        if (isset($requisicao['mensagem'])) {
            $mensagemXMdl = new MensagemXMdl();
            $mensagemXMdl->atribuiMensagem($requisicao['mensagem']);
        }

        switch ($pedido) {
        /* LISTAGEM DE CLIENTES */
        case 'listar':
        case '':
            $clienteLstMdl = new ClienteLstMdl();

            $url = 'cliente/cliente.phtml';
            
            MascaraXmn::adicionaModelo($clienteLstMdl);
            break;
        
        /* PESQUISA */
        case 'consultar':
            $clienteLstMdl  = new ClienteLstMdl();
            $clienteMdl     = new ClienteMdl();
            
            try {
                $valPesquisa = $requisicao['valPesquisa'] ?
                    $requisicao['valPesquisa'] : '';
                    
                $pesquisa    = $requisicao['pesquisa']    ?
                    $requisicao['pesquisa']    : '';

                switch ($pesquisa) {
                case 'codigo':
                    $clienteMdl->atribuiId($valPesquisa);
                    
                    ClientePrst::consultaModelo($clienteMdl, 
                        ClientePrst::PESQUISA_CLIENTE_CODIGO);
                        
                    $clienteLstMdl->adicionaModelo($clienteMdl);
                    break;
                    
                case 'nome':
                    $clienteMdl->atribuiNome($valPesquisa);
                    $clienteLstMdl->adicionaModelo($clienteMdl);
                    
                    ClientePrst::consultaListaModelo($clienteLstMdl, 
                        ClientePrst::PESQUISA_CLIENTES_NOME_APROXIMADO);
                        
                    break;

                case 'cpf':
                    $clienteMdl->atribuiCPF($valPesquisa);
                    $clienteLstMdl->adicionaModelo($clienteMdl);
                    
                    ClientePrst::consultaListaModelo($clienteLstMdl, 
                        ClientePrst::PESQUISA_CLIENTES_CPF);
                        
                    break;

                case 'cnpj':
                    $clienteMdl->atribuiCNPJ($valPesquisa);
                    $clienteLstMdl->adicionaModelo($clienteMdl);
                    
                    ClientePrst::consultaListaModelo($clienteLstMdl, 
                        ClientePrst::PESQUISA_CLIENTES_CNPJ);
                        
                    break;

                case 'rg':
                    $clienteMdl->atribuiRG($valPesquisa);
                    $clienteLstMdl->adicionaModelo($clienteMdl);
                    
                    ClientePrst::consultaListaModelo($clienteLstMdl, 
                        ClientePrst::PESQUISA_CLIENTES_RG);
                        
                    break;
                }

            } catch (TratamentoXmn $tratXmn) {
                throw $tratXmn;
                
            }
            
            
            $lstAtributo = array();
            
            $lstAtributo[] = array('atributo' => 'codigo',
                'metodo' => 'pegaId()');
            $lstAtributo[] = array('atributo' => 'nome',
                'metodo' => 'pegaNome()');
            $lstAtributo[] = array('atributo' => 'cpf',
                'metodo' => 'pegaCpf()');
            $lstAtributo[] = array('atributo' => 'cnpj',
                'metodo' => 'pegaCnpj()');
            $lstAtributo[] = array('atributo' => 'telefone1',
                'metodo' => 'pegaFone1()');
            $lstAtributo[] = array('atributo' => 'telefone2',
                'metodo' => 'pegaFone2()');
            $lstAtributo[] = array('atributo' => 'rg',
                'metodo' => 'pegaRg()');
            $lstAtributo[] = array('atributo' => 'endereco',
                'metodo' => 'pegaEnderecoXMdl()->pegaLogradouro()');
            $lstAtributo[] = array('atributo' => 'bairro',
                'metodo' => 'pegaEnderecoXMdl()->pegaBairroXMdl()->pegaNome()');
            $lstAtributo[] = array('atributo' => 'cidade',
                'metodo' => 'pegaEnderecoXMdl()->pegaBairroXMdl()'
                    . '->pegaCidadeXMdl()->pegaNome()');
            $lstAtributo[] = array('atributo' => 'uf',
                'metodo' => 'pegaEnderecoXMdl()->pegaBairroXMdl()'
                    . '->pegaCidadeXMdl()->pegaUfXMdl()->pegaNome()');
            $lstAtributo[] = array('atributo' => 'cep',
                'metodo' => 'pegaEnderecoXMdl()->pegaCep()');
            
            $xml = ListaModeloXmn::pegaXml($clienteLstMdl, $lstAtributo);
            
            unset($clienteMdl);
            unset($clienteLstMdl);
            
            return MensageiroXmn::xml($xml, 'SOS');

        /* LISTA DE DADOS */
        case 'listaDados':
            $id = isset($requisicao['id']) ? $requisicao['id'] : '0';

            $clienteMdl = new ClienteMdl();
            
            $clienteMdl->atribuiId($id);
            
            try {
                ClientePrst::consultaModelo($clienteMdl);
            } catch (TratamentoXmn $tratXmn) {
                return;
            }

            MascaraXmn::adicionaModelo($clienteMdl);
            MascaraXmn::exibeMascara(SosXmn::pegaAldeia('mascara_real')
              . 'cliente/dados_cliente.phtml', false);
            return;

        /* INCLUSÃO */
        case 'incluir':
            $clienteMdl = new ClienteMdl();
            
            if ($clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->
                pegaCidadeXMdl()->pegaNome() == '') {
                
                $clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->
                    pegaCidadeXMdl()->atribuiNome('São Paulo');
            }

            if ($clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->
                    pegaCidadeXMdl()->pegaUfXMdl()->pegaNome() == '') {
                $clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->
                    pegaCidadeXMdl()->pegaUfXMdl()->atribuiSigla('SP');
            }
            
            $url = 'cliente/cad_cliente.phtml';
            
            MascaraXmn::adicionaModelo($clienteMdl);
            break;

        /* ALTERAÇÃO */
        case 'alterar':
            $clienteMdl = new ClienteMdl();
            
            $identificador = isset($requisicao['identificador']) ?
                $requisicao['identificador']:'0';
            
            $clienteMdl->atribuiId($identificador);
            
            try {
                ClientePrst::consultaModelo($clienteMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $mensagemXMdl   = new MensagemXMdl();
                $clienteLstMdl   = new ClienteLstMdl();

                $mensagemXMdl->atribuiMensagem(
                    $tratXmn->pegaMensagem());
                
                self::mudaFluxo('sos.php?entidade=cliente&pedido=listar');
                return;
            }
            
            $url = 'cliente/cad_cliente.phtml';
            
            MascaraXmn::adicionaModelo($clienteMdl);
            break;

        /* REMOVER */
        case 'remover':
            $clienteMdl = new ClienteMdl();
            
            $identificador = isset($requisicao['identificador']) ? 
                $requisicao['identificador'] : '0'; 

            $clienteMdl->atribuiId($identificador);
            
            try {
                ClientePrst::removeModelo($clienteMdl);
                
            } catch(TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" mensagem=""/>';
            return MensageiroXmn::xml($xml, 'SOS');

        /* GRAVAÇÃO */
        case 'armazenar':
            $clienteMdl = new ClienteMdl();
            $clienteLstMdl = new ClienteLstMdl();
            
            $requisicao['identificador'] = isset($requisicao['identificador']) ?
                $requisicao['identificador'] : '';

            $requisicao['nome']     = isset($requisicao['nome']) ?
                $requisicao['nome'] : '';

            $requisicao['cnpj']     = isset($requisicao['cnpj']) ?
                $requisicao['cnpj'] : '';

            $requisicao['cpf']      = isset($requisicao['cpf']) ?
                $requisicao['cpf'] : '';

            $requisicao['rg']       = isset($requisicao['rg']) ?
                $requisicao['rg'] : '';

            $requisicao['endereco'] = isset($requisicao['endereco']) ?
                $requisicao['endereco'] : '';

            $requisicao['bairro']   = isset($requisicao['bairro']) ?
                $requisicao['bairro'] : '';

            $requisicao['cidade']   = isset($requisicao['cidade']) ?
                $requisicao['cidade'] : '';

            $requisicao['uf']       = isset($requisicao['uf']) ?
                $requisicao['uf'] : '';

            $requisicao['cep']      = isset($requisicao['cep']) ?
                $requisicao['cep'] : '';

            $requisicao['fone1']    = isset($requisicao['fone1']) ?
                $requisicao['fone1'] : '';

            $requisicao['fone2']    = isset($requisicao['fone2']) ?
                $requisicao['fone2'] : '';

            $requisicao['email']    = isset($requisicao['email']) ?
                $requisicao['email'] : '';
            
            $requisicao['cep']  = preg_replace('/[\-]/i', '', $requisicao['cep']);
            $requisicao['cpf']  = preg_replace('/[\.\-]/i', '', $requisicao['cpf']);
            $requisicao['cnpj'] = preg_replace('/[\.\-\/]/i', '', $requisicao['cnpj']);

            $clienteMdl->atribuiId($requisicao['identificador']);
            $clienteMdl->atribuiNome($requisicao['nome']);
            $clienteMdl->atribuiCnpj($requisicao['cnpj']);
            $clienteMdl->atribuiCpf($requisicao['cpf']);
            $clienteMdl->atribuiRg($requisicao['rg']);
            $clienteMdl->atribuiFone1($requisicao['fone1']);
            $clienteMdl->atribuiFone2($requisicao['fone2']);
            
            $clienteMdl->pegaEnderecoXMdl()->atribuiLogradouro($requisicao['endereco']);
            $clienteMdl->pegaEnderecoXMdl()->atribuiCep($requisicao['cep']);
            $clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->
                atribuiNome($requisicao['bairro']);
            $clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->pegaCidadeXMdl()->
                atribuiNome($requisicao['cidade']);
            $clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->pegaCidadeXMdl()->
                pegaUfXMdl()->atribuiSigla($requisicao['uf']);
            
            try {
                ClientePrst::armazenaModelo($clienteMdl);
                
            } catch (TratamentoXmn $tratXmn) {
                $xml = '<processo estado="false" mensagem="' 
                  . $tratXmn->pegaMensagem()
                  . '"/>';
                  
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
            $xml = '<processo estado="true" codigo="' 
                . $clienteMdl->pegaId() . '"/>';
                
            return MensageiroXmn::xml($xml, 'SOS');
        }
        
        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
}