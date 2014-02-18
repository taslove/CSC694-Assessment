<?php

namespace Plans\Model\Entity;

/**
 * Entity used to store the outcome results from the database
 */
class Outcome {

    protected $outcomeId;
    protected $planId;
    protected $outcomeText;

    public function __construct($programName, $outcomeId, $planId, $outcomeText) {
        $this->programName = $programName;
        $this->outcomeId = $outcomeId;
        $this->planId = $planId;
        $this->outcomeText = $outcomeText;
    }

    public function getProgramName() {
        return $this->programName;
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
}