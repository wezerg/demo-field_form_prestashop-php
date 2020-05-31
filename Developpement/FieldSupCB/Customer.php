<?php 
class Customer extends CustomerCore { 
//Nouveaux paramètres de classe 
public $numero_carte_bleue; 
 
public function __construct($id = null) { 
//Définition du nouveau champ numero_carte_bleue 
       self::$definition['fields']['numero_carte_bleue'] = [ 'type' => self::TYPE_INT,
            'required' => true, 'size' => 7
        ];

        parent::__construct($id);
    }
}