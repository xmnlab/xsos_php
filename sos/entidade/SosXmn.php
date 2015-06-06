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

class SosXmn extends Xaman implements EstruturaXmn
{
    /**
     * A aldeia armazena o endereço físico da raíz das classes
     * da estrutura de desenvolvimento Xaman
     *
     * @access private
     * @var    array
     * @static 
     */
    private static $aldeia = array();
    
    /**
     * A linguagem armazena a linguagem utilizada pelo Xaman
     *
     * @access private
     * @var    string
     * @static 
     */
    private static $linguagem = '';
    
    /**
     * A reencarnacaoXMdl armazena a quantidades de encarna��o do Xaman
     *
     * @access private
     * @var    string
     * @static 
     */
    private static $reencarnacaoXMdl = '';
    
    public static function iniciaJornada(DirecaoXMdl &$direcaoXMdl)
    {
        MascaraXmn::adicionaModelo($direcaoXMdl);
        
        @$entidade = $direcaoXMdl->requisicao['entidade'];
        @$pedido   = $direcaoXMdl->requisicao['pedido'];
        
        MensageiroXmn::log('/tmp/xmn.log',
              '' . date(DATE_RFC822) 
            . ' ENTIDADE:' . $entidade . ', ' . ' PEDIDO:'. $pedido
        );
    
        if ($entidade == '') {
            $entidade = 'sos';
            $pedido   = 'entrada';
        }
        
        if ($entidade == 'sos') {
            switch ($pedido) {
            case 'entrar':
                $direcaoXMdl->sessao['login'] = false;
                break;
                
            case 'recarregarSessao':
                echo '<!-- SESS�O ATUALIZADA EM: ' . date(DATE_RFC822) . ' -->';
                return;
                
            //LOGOUT    
            case 'encerrar':
                $direcaoXMdl->sessao['login'] = false;
                
                MascaraXmn::exibeMascara('login/login.phtml');
                return;
                
            case 'entrada':
                $direcaoXMdl->sessao['login'] = false;
                
                MascaraXmn::exibeMascara('login/login.phtml');
                return;
                
            case 'listar_ferramenta':
                $xml = file_get_contents(self::pegaAldeia('mascara_real') 
                    . 'estrutura/base/ferramenta.xmn');
                
                return MensageiroXmn::xml($xml, 'SOS');
                
            case 'mensagem':
                $xml = file_get_contents(
                    self::pegaAldeia('mascara_real') . 'estrutura/base/msg/' 
                        . $direcaoXMdl->requisicao['lista'] . '.xmn');
                return MensageiroXmn::xml($xml, 'SOS');
            }
        }

        /* VERIFY VERSION STRUCTURE */
        self::version($direcaoXMdl);
        
        /* AUTHENTICATION */
        try {
            if(!self::autenticaEntrada($direcaoXMdl)) {
                if (@$direcaoXMdl->requisicao['xml'] == 'false') {
                    MascaraXmn::exibeMascara(self::pegaAldeia('mascara_real') .
                      'estrutura/mensageiro.phtml',true);
                    return;
                } 
                
                $xml = '<processo estado="false" mensagem="PEDIDO_NEGADO"/>';
                return MensageiroXmn::xml($xml, 'SOS');
            }
            
        } catch (TratamentoXmn $tratXm) {
            $mensagemXMdl = new MensagemXMdl();

            $mensagemXMdl->atribuiMensagem($tratXm->pegaMensagem());
            MascaraXmn::adicionaModelo($mensagemXMdl);

            MascaraXmn::exibeMascara('login/login.phtml');
            return;
        }

        if ($direcaoXMdl->requisicao['entidade'] == 'sos' &&
            $direcaoXMdl->requisicao['pedido'] == 'entrar') {
                
            $direcaoXMdl->requisicao['entidade'] = 'os';
            $direcaoXMdl->requisicao['pedido']   = '';
        }

        try{
            switch ($entidade) {
            case 'atendente':
                AtendenteFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'marca':
                MarcaFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'modelo':
                ModeloFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'cliente':
                ClienteFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'operadora':
                OperadoraFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'estado_os':
                EstadoOsFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'menu':
                if($pedido == 'listar_menu') {
                    $xml = file_get_contents(SosXmn::pegaAldeia('mascara_real') 
                    . 'estrutura/base/menu.xmn');
                
                }
                return MensageiroXmn::xml($xml, 'SOS');

            case 'relatorio':
                RelatorioFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'sos':
                OsFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            case 'os':
                OsFlx::iniciaFluxo($direcaoXMdl);
                return;
                
            default:
                throw new TratamentoXmn(
                    array('codigo' => 'PARAMETRO_INVALIDO_SOS'));
            }
            
            return;
            
        } catch (TratamentoXmn $tratXm) {
            MascaraXmn::adicionaParametro('requisicao', $direcaoXMdl->requisicao);
            MascaraXmn::exibeMascara(
                  self::pegaAldeia('mascara_real')
                . 'estrutura/xaman/tratamento/tratamento.phtml', false);
            return;
        }
        
    }
    
    function verificaEntrada(DirecaoXMdl &$direcaoXMdl)
    {
        if (@$direcaoXMdl->sessao['permissao'] == '') {
            $direcaoXMdl->sessao['permissao'] = 'publico';
        }
        
        $listaPermissao = simplexml_load_file (
            SosXmn::pegaAldeia('ritual') . 'base/permissao.xmn');

        if(!GuardiaoXmn::verificaPermissaoPedido(
            $direcaoXMdl, $listaPermissao)) {
            
            throw new TratamentoXmn(
                array('codigo' => 'ACCESSO_NEGADO',
                'arquetipo' => 'FALLO_AUTENTICACION'
            ));
        }
        return true;
    }

    /* TODO: VERSION METHOD TO ORGANIZE THE DATABASE STRUCTURE AUTOMATICALLY */
    function version(DirecaoXMdl &$direcaoXMdl)
    {        
        /*$listaPermissao = simplexml_load_file (
            SosXmn::pegaAldeia('ritual') . 'base/permissao.xmn');*/

        
        return true;
    }
    
    function autenticaEntrada(DirecaoXMdl &$direcaoXMdl) {
        $requisicao = &$direcaoXMdl->pegaRequisicao();
        $entidade   = &$requisicao['entidade'];
        $pedido     = &$requisicao['pedido'];
        
       
        if (isset($direcaoXMdl->sessao['login']) && 
            !empty($direcaoXMdl->sessao['login']) 
            && $direcaoXMdl->sessao['login'] == true) {
            
            try {
                return self::verificaEntrada($direcaoXMdl);
            } catch (TratamentoXmn $tratXmn) {
                $mensagemXMdl = new MensagemXMdl();
                $mensagemXMdl->atribuiMensagem($tratXmn->pegaMensagem());
                $mensagemXMdl->atribuiNome('A op&ccedil;&atilde;o solicitada 
                  n&atilde;o est&aacute; dispon&iacute;vel para o seu perfil.');
                
                MascaraXmn::adicionaModelo($mensagemXMdl);
                return false;
            }
            
        } else if ($entidade == 'sos' && $pedido == 'entrar') {
            
            $usuario = $requisicao['usuario'] ? $requisicao['usuario'] : '';
            $senha   = $requisicao['senha']   ? $requisicao['senha']   : '';
            
            $atendenteMdl = new AtendenteMdl();
            $atendenteMdl->atribuiSenha($senha);
            $atendenteMdl->atribuiLogin($usuario);

            try {
                AtendentePrst::consultaModelo($atendenteMdl, 
                    AtendentePrst::PESQUISA_ATENDENTE_LOGIN
                );
            
            }catch (TratamentoXmn $e) {
                throw $e;
            }

            if ($atendenteMdl->pegaId() > 0) {
                $direcaoXMdl->sessao['login']       = true;
                $direcaoXMdl->sessao['permissao']   = $atendenteMdl->pegaGrupo();
                $direcaoXMdl->sessao['usuario']     = $atendenteMdl->pegaLogin();
                return true;
            }
            
            throw new TratamentoXmn(
                array('codigo' => 'USUARIO_SENHA_INVALIDOS',
                    'arquetipo' => 'FALHA_AUTENTICACAO'
            ));
            
        } else {
            throw new TratamentoXmn(
                array('codigo' => 'SESSAO_EXPIRADA',
                    'arquetipo' => 'FALHA_AUTENTICACAO')
            );
        }
        
        throw new TratamentoXmn(
            array('codigo' => 'SESSAO_INVALIDA',
                'arquetipo' => 'FALHA_AUTENTICACAO')
        );
    }

    /**
     * Atribui valor para a raíz das classes da estrutura xaman
     *
     * @access public
     * @param  string $aldeiaXaman
     * @return void
     * @static
     */
    public static function atribuiAldeia($caminho, $aldeiaXaman)
    {
        self::$aldeia[$caminho] = (string) $aldeiaXaman;
        
    }

    /**
     * Pega valor para a raíz das classes da estrutura xaman
     * 
     * @access public
     * @return string
     * @static
     */
    public static function pegaAldeia($caminho, $direcao = '')
    {
        return self::$aldeia[$caminho] . (string) $direcao;
    }
}
