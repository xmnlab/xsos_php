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

class AtendenteMdl extends ModeloXmn
{
    private $id     = 0;
    private $nome   = '';
    private $login  = '';
    private $grupo  = '';
    private $senha  = '';

    public function pegaId()
    {
        return $this->id;
    }
    public function pegaNome()
    {
        return $this->nome;
    }
    public function pegaLogin()
    {
        return $this->login;
    }
    public function pegaGrupo()
    {
        return $this->grupo;
    }
    public function pegaSenha()
    {
        return $this->senha;
    }

    public function atribuiId($id)
    {
        $this->id = $id;
    }
    public function atribuiNome($nome)
    {
        $this->nome = $nome;
    }
    public function atribuiLogin($login)
    {
        $this->login = $login;
    }
    public function atribuiGrupo($grupo)
    {
        $this->grupo = $grupo;
    }
    public function atribuiSenha($senha)
    {
        $this->senha = $senha;
    }
}