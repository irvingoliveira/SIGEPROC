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
 * Description of OrgaoExterno
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class OrgaoExterno extends PostoDeTrabalho{
    /**
     * @ORM\OneToOne(targetEntity="Endereco",cascade={"persist"}, inversedBy="orgaoExterno")
     * @ORM\JoinColumn(name="Endereco_idEndereco",
     *                 referencedColumnName="idEndereco", nullable=false)
     * @var Endereco
     */
    private $endereco;
    
    public function __construct() {
        parent::__construct();
    }

    public function getIdOrgaoExterno() {
        return parent::getIdPostoDeTrabalho();
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function setEndereco(Endereco $endereco) {
        $this->endereco = $endereco;
        $endereco->setOrgaoExterno($this);
    }
    
    public function __toString() {
        return $this->getNome();
    }
}
