<?php
namespace Admin\Entity;

class UnitPriv
{
    /**
     * @var string
     \*/
    protected $unit_id;

    /**
     * @param string $name
     * @return Category
     \*/
    public function setPriv($id)
    {
        $this->unit_id = $id;
        return $this;
    }

    /**
     * @return string
     \*/
    public function getPriv()
    {
        return $this->unit_id;
    }
}