<?PHP
namespace Reports\forms;

use Zend\Form\Form;
class PlanForm extends Form
{
    public function init()
    {
        // Only using one form item for outcomes for now
        $this->add(array(
            'name' => 'outcome',
            'attributes' => array(
                'type'  => 'textarea',
                'name' => 'outcome',
                'rows' => '5',
                'cols' => '60',
            ),
        ));
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'label',
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Select',
                'class' => 'btn btn-primary btn-xs', 
            ),
        ));
    }
}
?>
