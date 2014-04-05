<?php

/*
 * Copyright (C) 2014 Irving Fernando de Medeiros Oliveira
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
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of DocumentoRequerente
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Documento {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idDocumento;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $numero;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $digito;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    private $dataEmissao;
    /**
     * @ORM\Column(type="string",length=150, nullable=true)
     * @var string
     */
    private $orgaoEmissor;
    /**
     * @ORM\OneToOne(targetEntity="Requerente", inversedBy="documento")
     * @ORM\JoinColumn(name="Requerente_idRequerente",
     *                 referencedColumnName="idRequerente", nullable=false)
     * @var Requerente
     */
    private $requerenteProcesso;
    /**
     * @ORM\ManyToOne(targetEntity="TipoDocumento", inversedBy="documentos")
     * @ORM\JoinColumn(name="TipoDocumento_idTipoDocumento",
     *                 referencedColumnName="idTipoDocumento")
     * @var TipoDocumento
     */
    private $tipo;
    
    public function __construct() {
        
    }
    
    public function getIdDocumento() {
        return $this->idDocumento;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getDigito() {
        return $this->digito;
    }

    public function getDataEmissao() {
        return $this->dataEmissao;
    }

    public function getOrgaoEmissor() {
        return $this->orgaoEmissor;
    }

    public function getRequerente() {
        return $this->requerenteProcesso;
    }

    public function setIdDocumento($idDocumento) {
        $this->idDocumento = $idDocumento;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function setDigito($digito) {
        $this->digito = $digito;
    }

    public function setDataEmissao(\DateTime $dataEmissao) {
        $this->dataEmissao = $dataEmissao;
    }

    public function setOrgaoEmissor($orgaoEmissor) {
        $this->orgaoEmissor = $orgaoEmissor;
    }

    public function setRequerente(Requerente $requerente) {
        $this->requerenteProcesso = $requerente;
    }

    public function getRequerenteProcesso() {
        return $this->requerenteProcesso;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setRequerenteProcesso(Requerente $requerenteProcesso) {
        $this->requerenteProcesso = $requerenteProcesso;
    }

    public function setTipo(TipoDocumento $tipo) {
        $this->tipo = $tipo;
    }

}
