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
 * @category   ritual
 * @package    sos
 * @subpackage sos.ritual
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

session_start();

$PRINT_SERVER = 'http://localhost/os/print.php';
$PRINT_DEVICE = '/dev/ttyUSB0';

if (file_exists ('./local_settings.php')) {
    import('./local_settings.php');
}

#error_reporting(E_ALL);
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
ini_set("display_errors", 1); 

static $XAMAN_PACHAMAMA    = '/var/www/xaman/xmn/';

/** RITUAL DE INVOCAÇÃO DO XAMAN GRANDE XAMAN */
include_once $XAMAN_PACHAMAMA . 'ritual/iniciacao.php';

$origem = simplexml_load_file ('../ritual/base/origem.xmn');

eval(Xaman::preparaJornada($origem));

MascaraXmn::adicionaParametro('MASCARA', 
    SosXmn::pegaAldeia('mascara_real', 'estrutura/modelo/modelo.phtml'));

SosXmn::iniciaJornada($direcaoXMdl);
