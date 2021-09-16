<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {

	const LLAVE = LLAVE_ENCRIPTAR;

	function __construct(){
        parent::__construct();
        $this->load->database();
    }

    public function limpiaCampo($campo = ""){
    	if(is_numeric($campo) === true || is_bool($campo) === true)
    		return $campo;

    	if($campo == null || $campo == "")
    		return "";

    	if($campo != ""){
    		$campo = $this->db->escape($campo);
    		$campo = substr($campo,1,-1); // Quitale los apóstrofes
    	}
    	return $campo;
    }
}