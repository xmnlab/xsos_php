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
 * @category   persistencia
 * @package    sos
 * @subpackage sos.persistencia
 * @version    Release: @package_version@
 * @author     Ivan Ogassavara <ivan.ogassavara@gmail.com>
 */

include_once $SOS . 'modelo/ClienteMdl.php';
include_once $SOS . 'modelo/ClienteLstMdl.php';

class ClientePrst extends SosPrst implements EstruturaPrstXmn
{
    private static $SELECT = array();
    private static $INSERT = array();
    private static $UPDATE = array();
    private static $DELETE = array();

    const PESQUISA_CLIENTE_CODIGO = 1;

    const PESQUISA_CLIENTES                         = 100;
    const PESQUISA_CLIENTES_NOME_APROXIMADO         = 101;
    const PESQUISA_CLIENTES_CNPJ                    = 102;
    const PESQUISA_CLIENTES_CPF                     = 103;
    const PESQUISA_CLIENTES_RG                      = 104;
    const PESQUISA_CLIENTES_NOME                    = 105;
    const PESQUISA_CLIENTES_NOME_CODIGO_DIFERENTE   = 106;
    
    const ULTIMO_CODIGO            = 107;

    const GRAVACAO_CLIENTE        = 1;
    const EXCLUSAO_CLIENTE_CODIGO = 1;

    public static function inicia() {
        parent::inicia();
        self::preparaPersistencia();
    }

    public static function preparaPersistencia()
    {
        self::$SELECT[self::PESQUISA_CLIENTE_CODIGO] = 
            ' SELECT *' .
            ' FROM Cliente' .
            ' WHERE cliId=?';

        self::$SELECT[self::PESQUISA_CLIENTES] = 
              ' SELECT *'
            . ' FROM Cliente'
            . ' ORDER BY cliNome';

        self::$SELECT[self::PESQUISA_CLIENTES_NOME_APROXIMADO] = 
              ' SELECT *'
            . ' FROM Cliente'
            . ' WHERE cliNome LIKE ?'
            . ' ORDER BY cliNome';

        self::$SELECT[self::PESQUISA_CLIENTES_CPF] = 
            ' SELECT *' .
            ' FROM Cliente' .
            ' WHERE cliCpf = ?';

        self::$SELECT[self::PESQUISA_CLIENTES_CNPJ] = 
            ' SELECT *' .
            ' FROM Cliente' .
            ' WHERE cliCNPJ = ?';

        self::$SELECT[self::PESQUISA_CLIENTES_NOME] = 
            ' SELECT *' .
            ' FROM Cliente' .
            ' WHERE cliNome=?';

        self::$SELECT[self::PESQUISA_CLIENTES_NOME_CODIGO_DIFERENTE] = 
            ' SELECT *' .
            ' FROM Cliente' .
            ' WHERE cliNome=? AND cliId<>?';

        self::$SELECT[self::PESQUISA_CLIENTES_RG] = 
              ' SELECT *'
            . ' FROM Cliente'
            . ' WHERE cliRg=?'
            . ' ORDER BY cliNome';
            
        self::$SELECT[self::ULTIMO_CODIGO] =
              'SELECT MAX(cliId) as cliId from Cliente';

        self::$UPDATE[self::GRAVACAO_CLIENTE] = 
            ' update Cliente set'.
                ' cliNome=?,'.
                ' cliCnpj=?,'.
                ' cliCpf=?,'.
                ' cliRg=?,'.
                ' cliEndereco=?,'.
                ' cliBairro=?,'.
                ' cliCidade=?,'.
                ' cliUf=?,'.
                ' cliCep=?,'.
                ' cliFone1=?,'.
                ' cliFone2=?'.
            ' where'.
                ' cliId=?';

        self::$INSERT[self::GRAVACAO_CLIENTE] =
            ' insert into Cliente ('.
                ' cliId, cliNome,'.
                ' cliCnpj, cliCpf, '.
                ' cliRg, cliEndereco, '.
                ' cliBairro, cliCidade, '.
                ' cliUf, cliCep, '.
                ' cliFone1, cliFone2 '.
            ' )values('.
                ' 0, ?,'.
                ' ?, ?,'.
                ' ?, ?,'.
                ' ?, ?,'.
                ' ?, ?,'.
                ' ?, ?'.
            ')';

        self::$DELETE[self::EXCLUSAO_CLIENTE_CODIGO] = 
        ' DELETE' .
        ' FROM Cliente' .
        ' WHERE cliId=?';
    }

    /**
     * Popula modelo com as informa��es recebidas da base de dados
     *
     * @param ClienteMdl $clienteMdl
     * @param array $registro
     */
    public static function carregaModelo(ClienteMdl $clienteMdl, $registro)
    {
        @$clienteMdl->atribuiId($registro['cliId']);
        @$clienteMdl->atribuiNome($registro['cliNome']);
        @$clienteMdl->atribuiCnpj($registro['cliCnpj']);
        @$clienteMdl->atribuiCpf($registro['cliCpf']);
        @$clienteMdl->atribuiRg($registro['cliRg']);
        @$clienteMdl->atribuiFone1($registro['cliFone1']);
        @$clienteMdl->atribuiFone2($registro['cliFone2']);
        
        @$clienteMdl->pegaEnderecoXMdl()->atribuiLogradouro(
            $registro['cliEndereco']);
        @$clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->atribuiNome(
            $registro['cliBairro']);
        @$clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->pegaCidadeXMdl()->
            atribuiNome($registro['cliCidade']);
        @$clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->pegaCidadeXMdl()->
            pegaUfXMdl()->atribuiSigla($registro['cliUf']);
        @$clienteMdl->pegaEnderecoXMdl()->atribuiCep($registro['cliCep']);
    }

    /**
     * Consulta uma determinada fun��o e popula o objeto modelo
     *
     * @param ClienteMdl $clienteMdl
     * @param unknown_type $tipoGetMdl
     * @return unknown
     */
    public function consultaModelo(ClienteMdl $clienteMdl, 
       $tipoGetMdl = self::PESQUISA_CLIENTE_CODIGO)
    {
        self::inicia();
        switch ($tipoGetMdl) {
            case self::PESQUISA_CLIENTE_CODIGO:
                $iprst = new IntermediarioPrstXmn(
                    self::$SELECT[$tipoGetMdl]);
                $iprst->atribuiInteiro(1, $clienteMdl->pegaId());
                break;
                
            case self::ULTIMO_CODIGO:
                $iprst = new IntermediarioPrstXmn(
                    self::$SELECT[$tipoGetMdl]);
                break;
            
            default:
                break;
        }
        return parent::consultaModeloXmn(__CLASS__, $clienteMdl, $iprst);
    }

    public function consultaListaModelo(ClienteLstMdl $clienteLstMdl, 
        $tipoPesquisa = self::PESQUISA_CLIENTES_NOME_APROXIMADO)
    {
        self::inicia();
        
        $clienteLstMdl->movePrimeiro(true);
        
        switch($tipoPesquisa) {
        case self::PESQUISA_CLIENTES:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECT[$tipoPesquisa]);
            break;
            
        case self::PESQUISA_CLIENTES_CPF:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $clienteLstMdl->pegaModelo()->pegaCpf());
            break;
            
        case self::PESQUISA_CLIENTES_NOME_APROXIMADO:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, '%' . $clienteLstMdl->pegaModelo()->pegaNome() . '%');
            break;
            
        case self::PESQUISA_CLIENTES_NOME:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $clienteLstMdl->pegaModelo()->pegaNome());
            break;
            
        case self::PESQUISA_CLIENTES_NOME_CODIGO_DIFERENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $clienteLstMdl->pegaModelo()->pegaNome());
            $iprst->atribuiInteiro(2, $clienteLstMdl->pegaModelo()->pegaId());
            break;
            
        case self::PESQUISA_CLIENTES_CNPJ:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $clienteLstMdl->pegaModelo()->pegaCnpj());
            break;
            
        case self::PESQUISA_CLIENTES_RG:
            $iprst = new IntermediarioPrstXmn(
                self::$SELECT[$tipoPesquisa]);
            $iprst->atribuiTexto(1, $clienteLstMdl->pegaModelo()->pegaRg());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_CARREGAMENTO_INVALIDO');
        }

        try {
            parent::consultaListaModeloXmn(__CLASS__, $clienteLstMdl, $iprst);
        } catch(TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }

    public static function armazenaModelo(ClienteMdl $clienteMdl, 
        $tipoGravacao = self::GRAVACAO_CLIENTE)
    {
        self::inicia();
        
        try{
            if ($clienteMdl->pegaId() > 0) {
                self::alteraModelo($clienteMdl, $tipoGravacao);
            } else {
                self::insereModelo($clienteMdl, $tipoGravacao);
            }
            
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
    }

    private static function insereModelo(ClienteMdl $clienteMdl, 
        $tipoGravacao = self::GRAVACAO_CLIENTE)
    {
        try{
            self::validaModelo($clienteMdl);
            
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoGravacao) {
        case self::GRAVACAO_CLIENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$INSERT[$tipoGravacao]);
            $iprst->atribuiTexto(1 , strtoupper($clienteMdl->pegaNome()));
            $iprst->atribuiTexto(2 , $clienteMdl->pegaCnpj());
            $iprst->atribuiTexto(3 , $clienteMdl->pegaCpf());
            $iprst->atribuiTexto(4 , strtoupper($clienteMdl->pegaRg()));
            $iprst->atribuiTexto(5 , strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaLogradouro()));
            $iprst->atribuiTexto(6 , strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaBairroXMdl()->pegaNome()));
            $iprst->atribuiTexto(7 , strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaBairroXMdl()->pegaCidadeXMdl()->pegaNome()));
            $iprst->atribuiTexto(8 , strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaBairroXMdl()->pegaCidadeXMdl()->pegaUfXMdl()->pegaSigla()));
            $iprst->atribuiTexto(9 , $clienteMdl->pegaEnderecoXMdl()->pegaCep());
            $iprst->atribuiTexto(10 , $clienteMdl->pegaFone1());
            $iprst->atribuiTexto(11 , $clienteMdl->pegaFone2());
            break;
            
        default:
            throw new TratamentoPrstXmn('PARAMETRO_INCLUSAO_INVALIDO'); 
        }
        
        try {
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            $iprst = null;
            
            $cliente2Mdl = new ClienteMdl();
            
            self::consultaModelo($cliente2Mdl, self::ULTIMO_CODIGO);
            
            $clienteMdl->atribuiId($cliente2Mdl->pegaId());
            
        } catch (TratamentoPrstXmn $tratXmn) {
            MensageiroXmn::log('/tmp/xmn.log',
                  mysql_error()
            );
            
            $tratXmn->atribuiTratamento('INCLUSAO_NAO_REALIZADA');
            throw $tratXmn;
        }
    }

    private static function alteraModelo(ClienteMdl $clienteMdl, 
        $tipoGravacao = self::GRAVACAO_CLIENTE)
    {
        try{
            self::validaModelo($clienteMdl);
        } catch (TratamentoPrstXmn $tratXmn) {
            throw $tratXmn;
        }
        
        switch ($tipoGravacao) {
        case self::GRAVACAO_CLIENTE:
            $iprst = new IntermediarioPrstXmn(
                self::$UPDATE[$tipoGravacao]);
            $iprst->atribuiTexto(1, strtoupper($clienteMdl->pegaNome()));
            $iprst->atribuiTexto(2, $clienteMdl->pegaCnpj());
            $iprst->atribuiTexto(3, $clienteMdl->pegaCpf());
            $iprst->atribuiTexto(4, strtoupper($clienteMdl->pegaRg()));
            $iprst->atribuiTexto(5, strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaLogradouro()));
            $iprst->atribuiTexto(6, strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaBairroXMdl()->pegaNome()));
            $iprst->atribuiTexto(7, strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaBairroXMdl()->pegaCidadeXMdl()->pegaNome()));
            $iprst->atribuiTexto(8, strtoupper($clienteMdl->pegaEnderecoXMdl()->
                pegaBairroXMdl()->pegaCidadeXMdl()->pegaUfXMdl()->pegaSigla()));
            $iprst->atribuiTexto(9, $clienteMdl->pegaEnderecoXMdl()->pegaCep());
            $iprst->atribuiTexto(10, $clienteMdl->pegaFone1());
            $iprst->atribuiTexto(11, $clienteMdl->pegaFone2());
            $iprst->atribuiInteiro(12, $clienteMdl->pegaId());
            break;

        default:
            throw new TratamentoPrstXmn('PARAMETRO_ALTERACAO_INVALIDO');
        }

        try {
            
            if (!$iprst->executa()) {
                throw new TratamentoPrstXmn();
            }
            
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento('GRAVACAO_NAO_REALIZADA');
            throw $tratXmn;
        }
    }

    public function removeModelo(ClienteMdl $clienteMdl, 
        $tipoExclusao = self::EXCLUSAO_CLIENTE_CODIGO)
    {
        self::inicia();
        
        switch ($tipoExclusao) {
        case self::EXCLUSAO_CLIENTE_CODIGO:
            $iprst = new IntermediarioPrstXmn(
                self::$DELETE[$tipoExclusao]);
            $iprst->atribuiInteiro(1, $clienteMdl->pegaId());
            break;

        default:
            throw new TratamentoPrstXmn('PARAMETRO_EXCLUSAO_INVALIDO');
        }

        if (!$iprst->executa()) {
            throw new TratamentoPrstXmn('EXCLUSAO_NAO_REALIZADA');
        }
         
    }

    private static function validaModelo(ClienteMdl $clienteMdl, 
        $listaVerificacao = null)
    {
        if ($clienteMdl->pegaNome() == '') {
            throw new TratamentoPrstXmn('INFORME_NOME');

        }
        if ($clienteMdl->pegaCnpj() != '' && $clienteMdl->pegaCpf() != '') {
            throw new TratamentoPrstXmn('INFORME_CPF_OU_CNPJ');

        } else if ($clienteMdl->pegaCpf() == '' && $clienteMdl->pegaCnpj() != '') {
            if (!ValidaFormula::validaCpf($clienteMdl->pegaCnpj())) {
                throw new TratamentoPrstXmn('CNPJ_INVALIDO');
            }

        } else if ($clienteMdl->pegaCpf() != '' && $clienteMdl->pegaCnpj() == '') {
            if (!ConsultorXmn::validaCpf($clienteMdl->pegaCpf())) {
                throw new TratamentoPrstXmn('CPF_INVALIDO');
            }
        }

        if ($clienteMdl->pegaEnderecoXMdl()->pegaLogradouro() == '') {
            throw new TratamentoPrstXmn('INFORME_ENDERECO');

        } else if ($clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()
            ->pegaNome() == '') {
            
            throw new TratamentoPrstXmn('INFORME_BAIRRO');
            
        } else if ($clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()->
            pegaCidadeXMdl()->pegaNome() == '') {
            
            throw new TratamentoPrstXmn('INFORME_CIDADE');

        } else if ($clienteMdl->pegaEnderecoXMdl()->pegaBairroXMdl()
            ->pegaCidadeXMdl()->pegaUfXMdl()->pegaSigla() == '') {
            
            throw new TratamentoPrstXmn('INFORME_UF');

        } else if ($clienteMdl->pegaEnderecoXMdl()->pegaCep() == '') {
            throw new TratamentoPrstXmn('INFORME_CEP');

        } else if ($clienteMdl->pegaFone1() == '') {
            throw new TratamentoPrstXmn('INFORME_TELEFONE1');
        }
        
        $clienteLstMdl = new ClienteLstMdl();
        $clienteLstMdl->adicionaModelo($clienteMdl);

        try{
            if ($clienteMdl->pegaId() > 0) {
                $pesquisa = self::PESQUISA_CLIENTES_NOME_CODIGO_DIFERENTE;
            } else {
                $pesquisa = self::PESQUISA_CLIENTES_NOME;
            }

            self::consultaListaModelo($clienteLstMdl, $pesquisa);
        } catch (TratamentoPrstXmn $tratXmn) {
            $tratXmn->atribuiTratamento('FALHA_PESQUISA_CLIENTE_DUPLICADA');
            throw $tratXmn;
        }

        if ($clienteLstMdl->pegaTotalModelo() > 0) {
            throw new TratamentoPrstXmn('CLIENTE_DUPLICADO');
        }
        
    }
}