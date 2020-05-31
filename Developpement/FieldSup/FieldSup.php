<?php 
class FieldSup extends Module { 
    
    public function __construct() { 
        
        $this->name = 'FieldSup';
        $this->tab = 'others';
        $this->author = 'VGrimaux';
        $this->version = '1.0.0';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();
 
        $this->displayName = $this->l('FieldSup');
        $this->description = $this->l('add new fields to customer');
        $this->ps_versions_compliancy = array('min' => '1.7.1', 'max' => _PS_VERSION_);
    }
 
    /**
     * @return boolean
     */
    public function install() {
        if (!parent::install() || !$this->_installSql()
                //Hooks Admin
                || !$this->registerHook('actionAdminCustomersControllerSaveAfter') 
                || !$this->registerHook('actionAdminCustomersFormModifier')
                //Hooks Front        
                || !$this->registerHook('additionalCustomerFormFields')
                //Hooks objects 
                || !$this->registerHook('actionObjectCustomerAddAfter') 
                || !$this->registerHook('actionObjectCustomerUpdateAfter')
                //Hook validation des champs
                || !$this->registerHook('validateCustomerFormFields')
        ) {
            return false;
        }
 
        return true;
    }
 
    public function uninstall() {
        return parent::uninstall() && $this->_unInstallSql();
    }
 
    /**
     * Modifications sql du module
     * @return boolean
     */
    protected function _installSql() {
        $sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "customer "
                . "ADD code_banque INT(5) NULL, "
                . "ADD numero_identification INT(7) NULL, "
                . "ADD numero_commercant INT(7) NULL, "
                . "ADD numero_american_express INT(7) NULL, "
                . "ADD numero_vente_a_distance INT(7) NULL";
 
        return Db::getInstance()->execute($sqlInstall);
    }
 
    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    protected function _unInstallSql() {
        $sqlUnInstall = "ALTER TABLE " . _DB_PREFIX_ . "customer "
                . "DROP code_banque, "
                . "DROP numero_identification, "
                . "DROP numero_commercant, "
                . "DROP numero_american_express, "
                . "DROP numero_vente_a_distance";
 
        return Db::getInstance()->execute($sqlUnInstall);
    }
 
    /**
     * Modification du formulaire d'édition d'un client en BO
     * @param type $params
     */
    public function hookActionAdminCustomersFormModifier($params) {
 
        $params['fields'][0]['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Code banque'),
            'name' => 'code_banque',
            'class' => 'input fixed-width-xxl',
            'hint' => $this->l('Code banque')
        ];

        $params['fields'][0]['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Numéro d\'identification'),
            'name' => 'numero_identification',
            'class' => 'input fixed-width-xxl',
            'hint' => $this->l('Numéro d\'identification')
        ];

        $params['fields'][0]['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Numéro de commercant'),
            'name' => 'numero_commercant',
            'class' => 'input fixed-width-xxl',
            'hint' => $this->l('Numéro de commercant')
        ];

        $params['fields'][0]['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Numéro American Express (AMEX)'),
            'name' => 'numero_american_express',
            'class' => 'input fixed-width-xxl',
            'hint' => $this->l('Numéro American Express (AMEX)')
        ];

        $params['fields'][0]['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Numéro de vente à distance'),
            'name' => 'numero_vente_a_distance',
            'class' => 'input fixed-width-xxl',
            'hint' => $this->l('Numéro de vente à distance (VAD)')
        ];
 
        //Définition de la valeur du champ supplémentaire
        $params['fields_value']['Code banque'] = $params['object']->code_banque;
        $params['fields_value']['Numéro d\'identification'] = $params['object']->numero_identification;
        $params['fields_value']['Numéro de commercant'] = $params['object']->numero_commercant;
        $params['fields_value']['Numéro American Express (AMEX)'] = $params['object']->numero_american_express;
        $params['fields_value']['Numéro de vente à distance (VAD)'] = $params['object']->numero_vente_a_distance;
    }
 
    /**
     * Ajout d'un champ client supplémentaire en FO
     * @param type $params
     */
    public function hookAdditionalCustomerFormFields($params) {
 
        return [
                    (new FormField)
                    ->setName('code_banque')
                    ->setType('text')
                    ->setLabel($this->l('Code banque'))
                    ->setRequired(true),
                    (new FormField)
                    ->setName('numero_identification')
                    ->setType('text')
                    ->setLabel($this->l('Numéro d\'identification'))
                    ->setRequired(true),
                    (new FormField)
                    ->setName('numero_commercant')
                    ->setType('text')
                    ->setLabel($this->l('Numéro de commercant')),
                    (new FormField)
                    ->setName('numero_american_express')
                    ->setType('text')
                    ->setLabel($this->l('Numéro American Express (AMEX)')),
                    (new FormField)
                    ->setName('numero_vente_a_distance')
                    ->setType('text')
                    ->setLabel($this->l('Numéro de vente à distance (VAD)')),
        ];
    }
 
   /**
     * Validation des champs du formulaire client
     * @param type $params array : Tableau des champs du formulaire client lié au module
     * Instance de Field
     * cf. https://github.com/PrestaShop/PrestaShop/pull/6374
     */
    public function hookValidateCustomerFormFields($params)
    {
        foreach ( $params['fields'] as $field){
 
            /** @var FormField $field */          
            //Validation des champs supplémentaire si celui-ci n'est pas vide
            if ( $field->getName() == 'code_banque' && $field->getValue() != "") {
 
                //Mise en place de notre vérification ( ex: 5 caractère pour ce champ)
                if ( strlen($field->getValue()) != 5 ){
                    $field->setErrors(array($this->l('Ce numéro doit comporter 5 caractères.')));
                }
            }

            if ( $field->getName() == 'numero_identification' && $field->getValue() != "") {
 
                if ( strlen($field->getValue()) != 7 ){
                    $field->setErrors(array($this->l('Ce numéro doit comporter 7 caractères.')));
                }
            }

            if ( $field->getName() == 'numero_commercant' && $field->getValue() != "") {
 
                if ( strlen($field->getValue()) != 7 ){
                    $field->setErrors(array($this->l('Ce numéro doit comporter 7 caractères.')));
                }
            }

            if ( $field->getName() == 'numero_american_express' && $field->getValue() != "") {
 
                if ( strlen($field->getValue()) != 7 ){
                    $field->setErrors(array($this->l('Ce numéro doit comporter 7 caractères.')));
                }
            }

            if ( $field->getName() == 'numero_vente_a_distance' && $field->getValue() != "") {
 
                if ( strlen($field->getValue()) != 7 ){
                    $field->setErrors(array($this->l('Ce numéro doit comporter 7 caractères.')));
                }
            }
        }
 
        //Renvoi du tableau des paramètres au validateur
        return $params['fields'];
    }
 
}