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
 * Description of FluxoPosto
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class FluxoPosto {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idFluxoPosto;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $diasUteis;
    /**
     * @ORM\ManyToOne(targetEntity="Workflow", inversedBy="fluxosPostos")
     * @ORM\JoinColumn(name="Workflow_idWorkflow",
     *                 referencedColumnName="idWorkflow", nullable=false)
     * @var Workflow
     */
    private $workflow;
    /**
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="fluxosSetor")
     * @ORM\JoinColumn(name="Setor_idSetor",
     *                 referencedColumnName="idSetor", nullable=true)
     * @var Setor
     */
    private $setor;
    /**
     * @ORM\ManyToOne(targetEntity="OrgaoExterno", inversedBy="fluxosOrgaoExterno")
     * @ORM\JoinColumn(name="OrgaoExterno_idOrgaoExterno",
     *                 referencedColumnName="idOrgaoExterno", nullable=true)
     * @var OrgaoExterno
     */
    private $orgaoExterno;
    
    public function __construct() {
        
    }
    
    public function getIdFluxoPosto() {
        return $this->idFluxoPosto;
    }

    public function getDiasUteis() {
        return $this->diasUteis;
    }

    public function getWorkflow() {
        return $this->workflow;
    }

    public function getSetor() {
        return $this->setor;
    }

    public function getOrgaoExterno() {
        return $this->orgaoExterno;
    }

    public function setIdFluxoPosto($idFluxoPosto) {
        $this->idFluxoPosto = $idFluxoPosto;
    }

    public function setDiasUteis($diasUteis) {
        $this->diasUteis = $diasUteis;
    }

    public function setWorkflow(Workflow $workflow) {
        $this->workflow = $workflow;
    }

    public function setSetor(Setor $setor) {
        $this->setor = $setor;
    }

    public function setOrgaoExterno(OrgaoExterno $orgaoExterno) {
        $this->orgaoExterno = $orgaoExterno;
    }
}
