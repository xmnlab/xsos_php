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
 * @category   mascara
 * @package    sos
 * @subpackage sos.mascara
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$text = file_get_contents(realpath(dirname(__FILE__)) . '/template.txt', true);

$text = str_replace('{{ os_numero }}', $osMdl->pegaId(), $text);
$text = str_replace('{{ atend_nome }}', $osMdl->pegaAtendenteMdl()->pegaNome(), $text);
$text = str_replace('{{ cli_nome }}', $osMdl->pegaClienteMdl()->pegaNome(), $text);
$text = str_replace('{{ cli_cpf }}', $osMdl->pegaClienteMdl()->pegaCpf(), $text);
$text = str_replace('{{ dt_abertura }}', $osMdl->pegaDtAbertura()->pegaDataHora('d/m/Y'), $text);
$text = str_replace('{{ dt_entrega_prev }}', $osMdl->pegaDtEntregaPrevista()->pegaDataHora('d/m/Y'), $text);
$text = str_replace('{{ os_estado }}', $osMdl->pegaEstadoMdl()->pegaNome(), $text);
$text = str_replace('{{ ap_hexa }}', $osMdl->pegaAparelhoMdl()->pegaHexadecimal(), $text);
$text = str_replace('{{ ap_linha_numero }}', $osMdl->pegaAparelhoMdl()->pegaNumeroLinha(), $text);
$text = str_replace('{{ ap_modelo }}', $osMdl->pegaAparelhoMdl()->pegaModeloMdl()->pegaNome(), $text);
$text = str_replace('{{ ap_marca }}', $osMdl->pegaAparelhoMdl()->pegaModeloMdl()->pegaMarcaMdl()->pegaNome(), $text);
$text = str_replace('{{ ap_operadora }}', $osMdl->pegaAparelhoMdl()->pegaOperadoraMdl()->pegaNome(), $text);
$text = str_replace('{{ ap_linha_plano }}', $osMdl->pegaAparelhoMdl()->pegaPlanoLinha(), $text);
$text = str_replace('{{ cli_obs }}', $osMdl->pegaObsCliente(), $text);
$text = str_replace('{{ serv_descricao }}', $osMdl->pegaDescricaoServico(), $text);
$text = str_replace('{{ serv_valor }}', "R$ ".$osMdl->pegaValorServico(), $text);
$text = str_replace('{{ cli_telefone }}', $osMdl->pegaClienteMdl()->pegaFone1(), $text);
$text = str_replace('{{ cli_email }}', $osMdl->pegaClienteMdl()->pegaEmail(), $text);
$text = str_replace('{{ tec_obs }}', $osMdl->pegaObsTecnica(), $text);
$text = str_replace('{{ ap_estado }}', $osMdl->pegaAparelhoMdl()->pegaEstadoAparelho(), $text);

$text = iconv('UTF-8', 'ISO-8859-1', $text);

$fields = array('text' => $text, 'device' => $SOS_PRINT_DEVICE);
$url = $SOS_PRINT_SERVER;

try {
    print('starting curl');

    $curl = curl_init($url);

    $fields_string = http_build_query($fields);

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);

    $response = curl_exec($curl);

    curl_close($curl);
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

echo '<script>window.close();</script>';