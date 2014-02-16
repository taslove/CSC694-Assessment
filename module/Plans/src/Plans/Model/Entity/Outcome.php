<?php

namespace Plans\Model\Entity;

class Outcome {

    protected $planId;
    protected $outcomeText;

    public function __construct($planId, $outcomeText) {
        $this->planId = $planId;
        $this->outcomeText = $outcomeText;
    }

    public function getPlanId() {
        return $this->planId;
    }

    public function getOutcomeText() {
        return $this->outcomeText;
    }

/*
    public function setOutcomeText($outcomeText) {
        $this->outcomeText = $outcomeText;
        return $this;
    }
    
    public function setPlanId($planId) {
        $this->planId = $planId;
        return $this;
    }
*/        

}