<?PHP
namespace Reports\forms;

use Zend\Form\Form;
class SelectForm extends Form
{
    public function init()
    {
        // Only using one form item for outcomes for now
        $this->add(array(
            'name' => 'action',
            'attributes' => array(
                'type'  => 'option',
                'name' => 'action',
            ),
        ));
        
        $this->add(array(
            'name' => 'unit',
            'attributes' => array(
                'type'  => 'option',
                'name' => 'unit',
            ),
        ));
        
        $this->add(array(
            'name' => 'program',
            'attributes' => array(
                'type'  => 'option',
                'name' => 'program',
            ),
        ));
    }
}
?>
