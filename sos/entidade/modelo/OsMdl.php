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

class OsMdl extends ModeloXmn
{
    private $clienteMdl;
    private $aparelhoMdl;
    private $custoMaterial;
    private $dtAbertura;
    private $dtEntregaPrevista;
    private $dtEntrega;
    private $estadoMdl;
    private $atendenteMdl;
    private $obsCliente;
    private $obsTecnica;
    private $descricaoServico;
    private $valorServico;

    public function __construct(){
        $this->clienteMdl        = new ClienteMdl();
        $this->atendenteMdl      = new AtendenteMdl();
        $this->aparelhoMdl       = new AparelhoMdl();
        $this->dtAbertura        = new DataHoraArquetipoXmn();
        $this->dtEntrega         = new DataHoraArquetipoXmn();
        $this->dtEntregaPrevista = new DataHoraArquetipoXmn();
        $this->estadoMdl         = new EstadoMdl();
    }

    public function &pegaClienteMdl(){
        return $this->clienteMdl;
    }
    public function pegaCustoMaterial(){
        return $this->custoMaterial;
    }
    public function &pegaAparelhoMdl(){
        return $this->aparelhoMdl;
    }
    public function pegaDtAbertura(){
        return $this->dtAbertura;
    }
    public function pegaDtEntregaPrevista(){
        return $this->dtEntregaPrevista;
    }
    public function pegaDtEntrega(){
        return $this->dtEntrega;
    }
    public function &pegaEstadoMdl(){
        return $this->estadoMdl;
    }
    public function &pegaAtendenteMdl(){
        return $this->atendenteMdl;
    }
    public function pegaObsCliente(){
        return $this->obsCliente;
    }
    public function pegaObsTecnica(){
        return $this->obsTecnica;
    }
    public function pegaDescricaoServico(){
        return $this->descricaoServico;
    }
    public function pegaValorServico(){
        return $this->valorServico;
    }
    
    public function atribuiClienteMdl(ClienteMdl $clienteMdl){
        $this->clienteMdl = $clienteMdl;
    }
    public function atribuiCustoMaterial($custoMaterial){
        $this->custoMaterial = (string) $custoMaterial;
    }
    public function atribuiAparelhoMdl(AparelhoMdl $aparelhoMdl){
        $this->aparelhoMdl = $aparelhoMdl;
    }
    public function atribuiDtAbertura(DataHoraArquetipoXmn $dtAbertura){
        $this->dtAbertura = $dtAbertura;
    }
    public function atribuiDtEntregaPrevista(DataHoraArquetipoXmn $dtEntregaPrevista){
        $this->dtEntregaPrevista = $dtEntregaPrevista;
    }
    public function atribuiDtEntrega(DataHoraArquetipoXmn $dtEntrega){
        $this->dtEntrega = $dtEntrega;
    }
    public function atribuiEstadoMdl(EstadoMdl $estadoMdl){
        $this->estadoMdl = $estadoMdl;
    }
    public function atribuiAtendenteMdl(AtendenteMdl $atendenteMdl){
        $this->atendenteMdl = $atendenteMdl;
    }
    public function atribuiObsCliente($obsCliente){
        $this->obsCliente = (string) $obsCliente;
    }
    public function atribuiObsTecnica($obsTecnica){
        $this->obsTecnica = (string) $obsTecnica;
    }
    public function atribuiDescricaoServico($descricaoServico){
        $this->descricaoServico = (string) $descricaoServico;
    }
    public function atribuiValorServico($valorServico){
        $this->valorServico = (string) $valorServico;
    }
    
}
