<?php 
class Customer extends CustomerCore { 
//Nouveaux paramètres de la surcharge de la classe CustomerCore 
public $code_banque;
public $numero_identification; 
public $numero_commercant;  
public $numero_american_express; 
public $numero_vente_a_distance; 
 
public function __construct($id = null) { 
//Définition des nouveau champs supplémentaire 
       self::$definition['fields']['code_banque'] = [ 'type' => self::TYPE_INT,
            'required' => true, 'size' => 5
        ];

        self::$definition['fields']['numero_identification'] = [ 'type' => self::TYPE_INT,
            'required' => true, 'size' => 7
        ];

        self::$definition['fields']['numero_commercant'] = [ 'type' => self::TYPE_INT,
            'required' => false, 'size' => 7
        ];

        self::$definition['fields']['numero_american_express'] = [ 'type' => self::TYPE_INT,
            'required' => false, 'size' => 7
        ];

        self::$definition['fields']['numero_vente_a_distance'] = [ 'type' => self::TYPE_INT,
            'required' => false, 'size' => 7
        ];

        parent::__construct($id);
    }
}