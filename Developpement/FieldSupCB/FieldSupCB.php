<?php 
class FieldSupCB extends Module { 
    
    public function __construct() { 
        
        $this->name = 'FieldSupCB';
        $this->tab = 'others';
        $this->author = 'VGrimaux';
        $this->version = '1.0.0';
        $this->need_instance = 0;
        $this->bootstrap = true;
 
        parent::__construct();
 
        $this->displayName = $this->l('FieldSupCB');
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
                . "ADD numero_carte_bleue INT(7) NULL";
 
        return Db::getInstance()->execute($sqlInstall);
    }
 
    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    protected function _unInstallSql() {
        $sqlUnInstall = "ALTER TABLE " . _DB_PREFIX_ . "customer "
                . "DROP numero_carte_bleue";
 
        return Db::getInstance()->execute($sqlUnInstall);
    }
 
    /**
     * Modification du formulaire d'édition d'un client en BO
     * @param type $params
     */
    public function hookActionAdminCustomersFormModifier($params) {
 
        $params['fields'][0]['form']['input'][] = [
            'type' => 'text',
            'label' => $this->l('Numéro de carte bleue'),
            'name' => 'numero_carte_bleue',
            'class' => 'input fixed-width-xxl',
            'hint' => $this->l('Numéro de carte bleue')
        ];
 
        //Définition de la valeur du champ supplémentaire
        $params['fields_value']['numero_carte_bleue'] = $params['object']->numero_carte_bleue;
    }
 
    /**
     * Ajout d'un champ client supplémentaire en FO
     * @param type $params
     */
    public function hookAdditionalCustomerFormFields($params) {
 
        return [
                    (new FormField)
                    ->setName('numero_carte_bleue')
                    ->setType('text')
                    ->setLabel($this->l('Numéro de carte bleue'))
                    ->setRequired(true),
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
            //Validation custom du champ numero_carte_bleue si celui-ci n'est pas vide
            if ( $field->getName() == 'numero_carte_bleue' && $field->getValue() != "") {
 
                //Mise en place de notre vérification ( ex: 7 caractère pour le champ)
                if ( strlen($field->getValue()) != 7 ){
                    $field->setErrors(array($this->l('This code must have 7 characters.')));
                }
            }
        }
 
        //Renvoi du tableau des paramètres au validateur
        return $params['fields'];
    }
 
}