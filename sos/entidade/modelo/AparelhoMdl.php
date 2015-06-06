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
 * @category   modelo
 * @package    sos
 * @subpackage sos.modelo
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

class AparelhoMdl extends ModeloXmn
{
    private $hexadecimal;
    private $numeroLinha;
    private $planoLinha;
    private $modeloMdl;
    private $operadoraMdl;
    private $estadoAparelho;
        
    public function __construct()
    {
        $this->modeloMdl    = new ModeloMdl();
        $this->operadoraMdl = new OperadoraMdl();
    }
    
    public function pegaHexadecimal()
    {
        return $this->hexadecimal;
    }
    public function pegaNumeroLinha()
    {
        return $this->numeroLinha;
    }
    public function pegaPlanoLinha()
    {
        return $this->planoLinha;
    }
    public function &pegaModeloMdl()
    {
        return $this->modeloMdl;
    }
    public function &pegaOperadoraMdl()
    {
        return $this->operadoraMdl;
    }
    public function pegaEstadoAparelho()
    {
        return $this->estadoAparelho;
    }

    public function atribuiHexadecimal($hexadecimal)
    {
        $this->hexadecimal = $hexadecimal;
    }
    public function atribuiNumeroLinha($numeroLinha)
    {
        $this->numeroLinha = $numeroLinha;
    }
    public function atribuiPlanoLinha($planoLinha)
    {
        $this->planoLinha = $planoLinha;
    }
    public function atribuiModeloMdl(ModeloMdl $modeloMdl)
    {
        $this->modeloMdl = $modeloMdl;
    }
    public function atribuiOperadoraMdl(OperadoraMdl $operadoraMdl)
    {
        $this->operadoraMdl = $operadoraMdl;
    }
    public function atribuiEstadoAparelho($estadoAparelho)
    {
        $this->estadoAparelho=$estadoAparelho;
    }   
}
