<?php
namespace Admin\Entity;

class UserObj
{
    /**
     * @var string
     \*/
    protected $firstname;
    protected $lastname;
    protected $middleinit;
    protected $email;
    protected $datatelid;
    protected $roles;
    protected $unitprivs;

    /**
     * @param string $name
     * @return Product
     \*/
    public function setFirstName($name)
    {
        $this->firstname = $name;
        return $this;
    }

    /**
     * @return string
     \*/
    public function getFirstName()
    {
        return $this->firstname;
    }

        /**
     * @param string $name
     * @return Product
     \*/
    public function setLastName($name)
    {
        $this->lastname = $name;
        return $this;
    }

    /**
     * @return string
     \*/
    public function getLastName()
    {
        return $this->lastname;
    }
    
    /**
     * @param string $name
     * @return Product
     \*/
    public function setMiddleInit($name)
    {
        $this->middleinit = $name;
        return $this;
    }

    /**
     * @return string
     \*/
    public function getMiddleInit()
    {
        return $this->middleinit;
    }
    /**
     * @param array $roles
     * @return Product
     \*/
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return Roles
     \*/
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $unitprivs
     * @return Product
     \*/
    public function setUnitPrivs(array $unitprivs)
    {
        $this->unitprivs = $unitprivs;
        return $this;
    }

    /**
     * @return array
     \*/
    public function getUnitPrivs()
    {
        return $this->unitprivs;
    }
}