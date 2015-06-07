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

class ClienteMdl extends ModeloXmn
{
    private $nome              = '';
    private $email             = '';
    private $cnpj              = '';
    private $cpf               = '';
    private $rg                = '';
    private $orgaoEmissorRg    = '';
    private $enderecoXMdl      = null;
    private $fone1             = '';
    private $fone2             = '';
    
    public function __construct()
    {
        $this->enderecoXMdl = new EnderecoXMdl();
    }

    public function pegaCnpj()
    {
        return $this->cnpj;
    }
    public function pegaCpf()
    {
        return $this->cpf;
    }
    public function pegaRg()
    {
        return $this->rg;
    }
    public function pegaOrgaoEmissorRg()
    {
        return $this->orgaoEmissorRg;
    }
    public function &pegaEnderecoXMdl()
    {
        return $this->enderecoXMdl;
    }
    public function pegaBairro()
    {
        return $this->bairro;
    }
    public function pegaCidade()
    {
        return $this->cidade;
    }
    public function pegaUf()
    {
        return $this->uf;
    }
    public function pegaCep()
    {
        return $this->cep;
    }
    public function pegaEmail()
    {
        return $this->email;
    }
    public function pegaFone1()
    {
        return $this->fone1;
    }
    public function pegaFone2()
    {
        return $this->fone2;
    }

    public function atribuiCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
    }
    public function atribuiCpf($cpf)
    {
        $this->cpf = $cpf;
    }
    public function atribuiRg($rg)
    {
        $this->rg = $rg;
    }
    public function atribuiOrgaoEmissorRg($orgaoEmissorRg)
    {
        $this->orgaoEmissorRg = $orgaoEmissorRg;
    }
    public function atribuiEnderecoXMdl(EnderecoXMdl $enderecoMdl)
    {
        $this->enderecoXMdl = $enderecoXMdl;
    }
    public function atribuiCep($cep)
    {
        $this->cep = $cep;
    }
    public function atribuiEmail($email)
    {
        $this->email = $email;
    }
    public function atribuiFone1($fone1)
    {
        $this->fone1 = $fone1;
    }
    public function atribuiFone2($fone2)
    {
        $this->fone2 = $fone2;
    }
}