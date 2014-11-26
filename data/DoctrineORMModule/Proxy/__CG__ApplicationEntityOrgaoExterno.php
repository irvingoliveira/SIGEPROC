<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class OrgaoExterno extends \Application\Entity\OrgaoExterno implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', 'idOrgaoExterno', 'nome', 'abreviacao', 'endereco', 'fluxosOrgaoExterno', 'guiasDeRemessa');
        }

        return array('__isInitialized__', 'idOrgaoExterno', 'nome', 'abreviacao', 'endereco', 'fluxosOrgaoExterno', 'guiasDeRemessa');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (OrgaoExterno $proxy) {
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
    public function getIdOrgaoExterno()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getIdOrgaoExterno();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdOrgaoExterno', array());

        return parent::getIdOrgaoExterno();
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
    public function getAbreviacao()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAbreviacao', array());

        return parent::getAbreviacao();
    }

    /**
     * {@inheritDoc}
     */
    public function getEndereco()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEndereco', array());

        return parent::getEndereco();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdOrgaoExterno($idOrgaoExterno)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdOrgaoExterno', array($idOrgaoExterno));

        return parent::setIdOrgaoExterno($idOrgaoExterno);
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
    public function setAbreviacao($abreviacao)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAbreviacao', array($abreviacao));

        return parent::setAbreviacao($abreviacao);
    }

    /**
     * {@inheritDoc}
     */
    public function setEndereco(\Application\Entity\Endereco $endereco)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEndereco', array($endereco));

        return parent::setEndereco($endereco);
    }

    /**
     * {@inheritDoc}
     */
    public function addGuiaDeRemessa(\Application\Entity\GuiaDeRemessa $guiaDeremessa)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addGuiaDeRemessa', array($guiaDeremessa));

        return parent::addGuiaDeRemessa($guiaDeremessa);
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
    public function getGuiaDeRemessas()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGuiaDeRemessas', array());

        return parent::getGuiaDeRemessas();
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

}
