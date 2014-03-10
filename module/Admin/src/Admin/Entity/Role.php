<?php
namespace Admin\Entity;

class Role
{
    /**
     * @var string
     \*/
    protected $role;

    /**
     * @param string $name
     * @return Category
     \*/
    public function setRole($id)
    {
        $this->role = $id;
        return $this;
    }

    /**
     * @return string
     \*/
    public function getRole()
    {
        return $this->role;
    }
}