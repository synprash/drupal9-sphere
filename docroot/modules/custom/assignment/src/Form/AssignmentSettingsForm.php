<?php
/**
 * Class file for AssignmentSettingsForm
 * php Y&me3xjp4s7Y8e9
 */
namespace Drupal\assignment\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form to configure Assignment settings.
 */
class AssignmentSettingsForm extends ConfigFormBase
{


    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'assignment_settings_form';

    }//end getFormId()


    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ['assignment.settings'];

    }//end getEditableConfigNames()


    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('assignment.settings');

        $form['api_url']         = [
            '#type'          => 'textfield',
            '#title'         => $this->t('API URL'),
            '#default_value' => $config->get('api_url'),
            '#required'      => true,
        ];
        $form['bitcoin_api_url'] = [
            '#type'          => 'textfield',
            '#title'         => $this->t('Bitcoin API URL'),
            '#default_value' => $config->get('bitcoin_api_url'),
            '#required'      => true,
        ];

        return parent::buildForm($form, $form_state);

    }//end buildForm()


    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->config('assignment.settings')->set('api_url', $form_state->getValue('api_url'))->set('bitcoin_api_url', $form_state->getValue('bitcoin_api_url'))->save();

        parent::submitForm($form, $form_state);

    }//end submitForm()


}//end class
