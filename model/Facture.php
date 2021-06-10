<?php
class Facture{

	//attributes
	private $_id;
	private $_date;
	private $_idClient;
	private $_numero;
    private $_code;
	private $_created;
	private $_createdBy;
	private $_updated;
	private $_updatedBy;

	//le constructeur
    public function __construct($data){
        $this->hydrate($data);
    }
    
    //la focntion hydrate sert à attribuer les valeurs en utilisant les setters d\'une façon dynamique!
    public function hydrate($data){
        foreach ($data as $key => $value){
            $method = 'set'.ucfirst($key);
            
            if (method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }

	//setters
	public function setId($id){
    	$this->_id = $id;
    }
	
	public function setDate($date){
		$this->_date = $date;
   	}

	public function setIdClient($idClient){
		$this->_idClient = $idClient;
   	}

	public function setNumero($numero){
		$this->_numero = $numero;
   	}
    
    public function setCode($code){
        $this->_code = $code;
    }

	public function setCreated($created){
        $this->_created = $created;
    }

	public function setCreatedBy($createdBy){
        $this->_createdBy = $createdBy;
    }

	public function setUpdated($updated){
        $this->_updated = $updated;
    }

	public function setUpdatedBy($updatedBy){
        $this->_updatedBy = $updatedBy;
    }

	//getters
	public function id(){
    	return $this->_id;
    }
    
	public function date(){
		return $this->_date;
   	}

	public function idClient(){
		return $this->_idClient;
   	}

	public function numero(){
		return $this->_numero;
   	}
    
    public function code(){
        return $this->_code;
    }

	public function created(){
        return $this->_created;
    }

	public function createdBy(){
        return $this->_createdBy;
    }

	public function updated(){
        return $this->_updated;
    }

	public function updatedBy(){
        return $this->_updatedBy;
    }

}