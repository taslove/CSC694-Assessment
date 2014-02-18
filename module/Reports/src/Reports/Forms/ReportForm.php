<?PHP
namespace Reports\forms;

use Zend\Form\Form;
class ReportForm extends Form
{
    
    // Report form used for capturing report data from view
    public function init()
    {
        // Submit button
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit Changes',
                'id' => 'submit',
                'class' => 'btn btn-primary',
            ),
        ));
        
        // Action data
        $this->add(array(
            'name' => 'actions',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'actions',
                'class' => 'textbox',
                'rows' => '10',
                'cols' => '60',
            ),
        ));
        
        // Conclusion data
        $this->add(array(
            'name' => 'conclusions',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'conclusions',
                'class' => 'textbox',
                'rows' => '10',
                'cols' => '60',
            ),
        ));
        
        // Result data
        $this->add(array(
            'name' => 'results',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'results',
                'class' => 'textbox',
                'rows' => '10',
                'cols' => '60',
            ),
        ));
        
        // Population data
        $this->add(array(
            'name' => 'population',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'population',
                'class' => 'textbox',
                'rows' => '10',
                'cols' => '60',
            ),
        ));
        
        // Method data
        $this->add(array(
            'name' => 'method',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'method',
                'class' => 'textbox',
                'rows' => '10',
                'cols' => '60',
            ),
        ));
        
        // Report id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'textarea',
                'id' => 'id',
                'class' => 'textbox',
                'rows' => '10',
                'cols' => '60',
            ),
        ));
    }

}
?>
