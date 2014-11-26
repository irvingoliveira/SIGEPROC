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

namespace Application\DAL;

use Zend\ServiceManager\ServiceManager;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of AssuntoDAO
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class WorkflowDAO extends GenericDAO {

    public function __construct(ServiceManager $serviceManager) {
        parent::__construct($serviceManager);
    }

    public function getNomeDaClasse() {
        return 'Workflow';
    }

    public function salvar(ArrayCollection $params) {
        $assuntoDAO = new AssuntoDAO($this->getServiceManager());
        $assunto = $assuntoDAO->lerPorId($params->get('assuntoTxt'));
        $fluxoPostoDAO = new FluxoPostoDAO($this->getServiceManager());
        $setorDAO = new SetorDAO($this->getServiceManager());
        $orgaoExternoDAO = new OrgaoExternoDAO($this->getServiceManager());

        $parametros = new ArrayCollection();
        $parametros->set('assunto', $assunto);
        $parametros->set('descricao', $params->get('descricao'));
        $workflow = parent::salvar($parametros);

        $fluxoPostos = new ArrayCollection();
        
        $i = 0;
        while (TRUE) {
            $postos = new ArrayCollection();
            $postos->set('workflow', $workflow);

            if($params->get('posto'.$i) == NULL)
                    break;
            
            if (substr($params->get('posto' . $i), 0, 1) == "S") {
                $postos->set('posto', $setorDAO->lerPorId(substr($params->get('posto' . $i), 1)));
            } else {
                $postos->set('posto', $orgaoExternoDAO->lerPorId(substr($params->get('posto' . $i), 1)));
            }
            $postos->set('workflow', $workflow);
            $fluxoPostos->add($fluxoPostoDAO->salvar($postos));

            $i++;
        }
        $parametros->set('fluxosPostos', $fluxoPostos);
        $this->editar($workflow->getIdWorkflow(), $parametros);
        return $workflow;
    }

}
