<?php

namespace Proxies\__CG__\models\RBAC;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Resource extends \models\RBAC\Resource implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function includes(\models\RBAC\Entity $entity)
    {
        $this->__load();
        return parent::includes($entity);
    }

    public function excludes(\models\RBAC\Entity $entity)
    {
        $this->__load();
        return parent::excludes($entity);
    }

    public function countEntities()
    {
        $this->__load();
        return parent::countEntities();
    }

    public function hasEntities()
    {
        $this->__load();
        return parent::hasEntities();
    }

    public function getEntities()
    {
        $this->__load();
        return parent::getEntities();
    }

    public function hasEntity(\models\RBAC\Entity $entity)
    {
        $this->__load();
        return parent::hasEntity($entity);
    }

    public function getRules()
    {
        $this->__load();
        return parent::getRules();
    }

    public function countRules()
    {
        $this->__load();
        return parent::countRules();
    }

    public function hasRules()
    {
        $this->__load();
        return parent::hasRules();
    }

    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function getDescription()
    {
        $this->__load();
        return parent::getDescription();
    }

    public function setDescription($description)
    {
        $this->__load();
        return parent::setDescription($description);
    }

    public function getGranular()
    {
        $this->__load();
        return parent::getGranular();
    }

    public function setGranular($boolean)
    {
        $this->__load();
        return parent::setGranular($boolean);
    }

    public function isGranular()
    {
        $this->__load();
        return parent::isGranular();
    }

    public function getLeftValue()
    {
        $this->__load();
        return parent::getLeftValue();
    }

    public function setLeftValue($left_key)
    {
        $this->__load();
        return parent::setLeftValue($left_key);
    }

    public function getRightValue()
    {
        $this->__load();
        return parent::getRightValue();
    }

    public function setRightValue($right_key)
    {
        $this->__load();
        return parent::setRightValue($right_key);
    }

    public function setRootValue($root)
    {
        $this->__load();
        return parent::setRootValue($root);
    }

    public function getRootValue()
    {
        $this->__load();
        return parent::getRootValue();
    }

    public function __toString()
    {
        $this->__load();
        return parent::__toString();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'description', 'granular', 'lft', 'rgt', 'root', 'entities', 'rules');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}