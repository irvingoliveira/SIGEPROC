<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Setor extends \Application\Entity\Setor implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'idSetor', 'nome', 'sigla', 'setorPai', 'setoresFilhos', 'tipoSetor', 'secretaria', 'usuarios', 'fluxosSetor', 'guiasDeRemessa', 'requerentes', 'assuntos');
        }

        return array('__isInitialized__', 'idSetor', 'nome', 'sigla', 'setorPai', 'setoresFilhos', 'tipoSetor', 'secretaria', 'usuarios', 'fluxosSetor', 'guiasDeRemessa', 'requerentes', 'assuntos');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Setor $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getIdSetor()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getIdSetor();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdSetor', array());

        return parent::getIdSetor();
    }

    /**
     * {@inheritDoc}
     */
    public function getNome()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNome', array());

        return parent::getNome();
    }

    /**
     * {@inheritDoc}
     */
    public function getSigla()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSigla', array());

        return parent::getSigla();
    }

    /**
     * {@inheritDoc}
     */
    public function getSetorPai()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSetorPai', array());

        return parent::getSetorPai();
    }

    /**
     * {@inheritDoc}
     */
    public function getTipo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTipo', array());

        return parent::getTipo();
    }

    /**
     * {@inheritDoc}
     */
    public function getSecretaria()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSecretaria', array());

        return parent::getSecretaria();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdSetor($idSetor)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdSetor', array($idSetor));

        return parent::setIdSetor($idSetor);
    }

    /**
     * {@inheritDoc}
     */
    public function setNome($nome)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNome', array($nome));

        return parent::setNome($nome);
    }

    /**
     * {@inheritDoc}
     */
    public function setSigla($sigla)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSigla', array($sigla));

        return parent::setSigla($sigla);
    }

    /**
     * {@inheritDoc}
     */
    public function setSetorPai(\Application\Entity\Setor $setorPai)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSetorPai', array($setorPai));

        return parent::setSetorPai($setorPai);
    }

    /**
     * {@inheritDoc}
     */
    public function setTipo(\Application\Entity\TipoSetor $tipo)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTipo', array($tipo));

        return parent::setTipo($tipo);
    }

    /**
     * {@inheritDoc}
     */
    public function setSecretaria(\Application\Entity\Secretaria $secretaria)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSecretaria', array($secretaria));

        return parent::setSecretaria($secretaria);
    }

    /**
     * {@inheritDoc}
     */
    public function addSetorFilho(\Application\Entity\Setor $setor)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addSetorFilho', array($setor));

        return parent::addSetorFilho($setor);
    }

    /**
     * {@inheritDoc}
     */
    public function getSetorFilho($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSetorFilho', array($key));

        return parent::getSetorFilho($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeSetorFilho($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeSetorFilho', array($key));

        return parent::removeSetorFilho($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getSetoresFilhos()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSetoresFilhos', array());

        return parent::getSetoresFilhos();
    }

    /**
     * {@inheritDoc}
     */
    public function addGuiaDeRemessa(\Application\Entity\GuiaDeRemessa $guiaDeRemessa)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addGuiaDeRemessa', array($guiaDeRemessa));

        return parent::addGuiaDeRemessa($guiaDeRemessa);
    }

    /**
     * {@inheritDoc}
     */
    public function getGuiaDeRemessa($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGuiaDeRemessa', array($key));

        return parent::getGuiaDeRemessa($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeGuiaDeRemessa($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeGuiaDeRemessa', array($key));

        return parent::removeGuiaDeRemessa($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getGuiasDeRemessa()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGuiasDeRemessa', array());

        return parent::getGuiasDeRemessa();
    }

    /**
     * {@inheritDoc}
     */
    public function addFluxoPosto(\Application\Entity\FluxoPosto $fluxoPosto)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addFluxoPosto', array($fluxoPosto));

        return parent::addFluxoPosto($fluxoPosto);
    }

    /**
     * {@inheritDoc}
     */
    public function getFluxoPosto($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFluxoPosto', array($key));

        return parent::getFluxoPosto($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeFluxoPosto($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeFluxoPosto', array($key));

        return parent::removeFluxoPosto($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getFluxosPostos()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFluxosPostos', array());

        return parent::getFluxosPostos();
    }

    /**
     * {@inheritDoc}
     */
    public function addRequerente(\Application\Entity\Requerente $requerente)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addRequerente', array($requerente));

        return parent::addRequerente($requerente);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequerente($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRequerente', array($key));

        return parent::getRequerente($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeRequerente($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeRequerente', array($key));

        return parent::removeRequerente($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequerentes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRequerentes', array());

        return parent::getRequerentes();
    }

    /**
     * {@inheritDoc}
     */
    public function addAssunto(\Application\Entity\Assunto $assunto)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addAssunto', array($assunto));

        return parent::addAssunto($assunto);
    }

    /**
     * {@inheritDoc}
     */
    public function getAssunto($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAssunto', array($key));

        return parent::getAssunto($key);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAssunto($key)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeAssunto', array($key));

        return parent::removeAssunto($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getAssuntos()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAssuntos', array());

        return parent::getAssuntos();
    }

}
