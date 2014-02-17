<?php

namespace Plans\Model\Entity;

class Outcome {

    protected $outcomeId;
    protected $planId;
    protected $outcomeText;

    public function __construct($outcomeId, $planId, $outcomeText) {
        $this->outcomeId = $outcomeId;
        $this->planId = $planId;
        $this->outcomeText = $outcomeText;
    }

    public function getOutcomeId() {
        return $this->outcomeId;
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