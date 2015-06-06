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

class RelatorioFlx extends SosFlx
{
    public static function iniciaFluxo(DirecaoXMdl &$direcaoXMdl)
    {
        MascaraXmn::adicionaModelo($direcaoXMdl);
        
        $pedido     = &$direcaoXMdl->requisicao['pedido'];
        $requisicao = &$direcaoXMdl->requisicao;
        $modoPortal = true;
        $url        = '';
        $xml        = '';
        
        switch ($pedido) {
        /* TELA PRINCIPAL */
        case 'listar':
        case '':
            $url = 'relatorio/lista_relatorio.phtml';
            break;
            
        case 'listar_tabela':
            $xml = RelatorioPrst::consulta(RelatorioPrst::__BD_TABELA);
            $xml = str_replace('Tables_in_xsos', 'tabela', $xml);
            
            header("Content-type: application/xml; charset=utf-8");
            echo $xml;
            return;
            
        case 'listar_campo':
            $xml = RelatorioPrst::consulta(RelatorioPrst::__TABELA_CAMPO, 
                array('tabela' => $requisicao['tabela']));
            $xml = str_replace('Field', 'campo',      $xml);
            $xml = str_replace('Type',  'arquetipo',  $xml);
            
            header("Content-type: application/xml; charset=utf-8");
            echo $xml;
            return;
            
        case 'listar_relatorio':
            $relatorio = simplexml_load_file(SosXmn::pegaAldeia('mascara_real', 
                'relatorio/base/relatorio.xmn'));
            
            header("Content-type: application/xml; charset=utf-8");
            echo $relatorio->asXML();
            return;
            
        case 'relatorio':
            $url        = 'relatorio/relatorio.phtml';
            $modoPortal = false;
            
        case 'consultar':
            $requisicao['sql'] = str_replace("\\'", "'", $requisicao['sql']);
            
            $xml = RelatorioPrst::consulta(RelatorioPrst::__VAZIO, 
                array('consulta' => $requisicao['sql']));
            
            header("Content-type: application/xml; charset=utf-8");
            echo $xml;
            return;
            
        case 'carga_abertura':
            $url = 'relatorio/carga_relatorio.phtml';
            break;
            
        case 'carga_armazenar':
            $relatorio = file_get_contents(
              $direcaoXMdl->artefato['relatorio']['tmp_name']);
            
            file_put_contents(SosXmn::pegaAldeia('mascara_real', 
                'relatorio/base/relatorio.xmn'), $relatorio);
            
            $mensagemXMdl = new MensagemXMdl();
            $mensagemXMdl->atribuiMensagem('RELATORIO_CARREGADO');
            
            MascaraXmn::adicionaModelo($mensagemXMdl);
            
            $url = 'relatorio/carga_relatorio.phtml';
            break;
            
        default:
            break;
        }
        
        
        MascaraXmn::exibeMascara($url, $modoPortal);
        return;
    }
    
}
