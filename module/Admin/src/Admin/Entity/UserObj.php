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
   # protected $datatelid;
    protected $roles;
    protected $unitprivs;


    /* @param string $name
     * @return Product
     */
    public function setEmail($name)
    {
        $this->email = $name;
        return $this;
    }
    //@return string
    public function getEmail()
    {
        return $this->email;
    }
    
     /* @param string $name
     * @return Product
     */
    public function setFirstname($name)
    {
        $this->firstname = $name;
        return $this;
    }
    //@return string
    public function getFirstname()
    {
        return $this->firstname;
    }
    
     /* @param string $name
     * @return Product
     */
    public function setLastname($name)
    {
        $this->lastname = $name;
        return $this;
    }
    //@return string
    public function getLastname()
    {
        return $this->lastname;
    }
    
     /* @param string $name
     * @return Product
     */
    public function setMiddleinit($name)
    {
        $this->middleinit = $name;
        return $this;
    }
    //@return string
    public function getMiddleinit()
    {
        return $this->middleinit;
    }
    
    
    /* @param string $name
     * @return Product
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        return $this;
    }
    //@return string
    public function getRoles()
    {
        return $this->roles;
    }
    
    /* @param string $name
     * @return Product
     */
    public function setUnitPrivs(array $unitprivs)
    {
        $this->unitprivs = $unitprivs;
        return $this;
    }
    //@return string
    public function getUnitPrivs()
    {
        return $this->unitprivs;
    }
}