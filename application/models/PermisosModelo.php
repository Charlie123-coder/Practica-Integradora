<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PermisosModelo extends MY_Model { 

	public function __construct() {
		parent::__construct();
	}
   
	public function insertarPermiso($username="", $modulo ="", $usernameModificacion = ""){
        
		$username = $this->limpiaCampo($username);
        $modulo = $this->limpiaCampo($modulo);
        $usernameModificacion = $this->limpiaCampo($usernameModificacion);

        if($username == "" || $modulo == "" || $usernameModificacion == "")
   			return FALSE;

        $fecha = date('Y-m-d H:i:s');
        $instruccion = "INSERT INTO permisos
       					(username, modulo,altas,bajas,cambios,consultas,usernameModificacion, fechaModificacion) 
       					VALUES
                     	('$username','$modulo',0,0,0,0,'$usernameModificacion','$fecha')";
		//die($instruccion);       
       	$this->db->query($instruccion); 
       	
       	return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}



   	public function tienePermiso($username = "",$modulo="", $accion=""){

   		$username = $this->limpiaCampo($username);
        $modulo = $this->limpiaCampo($modulo);
        $accion = $this->limpiaCampo($accion);

   		if($username == "" || $modulo == "" || $accion == "")
   			return FALSE;
   		
      	$instruccion = "SELECT * FROM permisos, modulo 
      					WHERE 
      					modulo.modulo = permisos.modulo AND 
      					modulo.habilitado = '1' AND 
      					username='$username' AND 
      					permisos.modulo='$modulo' AND 
      					$accion = '1'";

      	//die($instruccion);
      	$query = $this->db->query($instruccion);
      	if(isset($query) && $query->num_rows()>0)
        	return  TRUE;
        	
      	return FALSE;
   	}



   	public function cambiarEstatusPermiso($username="", $modulo="", $accion="", $estatus = "", $usernameModificacion = ""){

   		$username = $this->limpiaCampo($username);
        $modulo = $this->limpiaCampo($modulo);
        $accion = $this->limpiaCampo($accion);
        $estatus = $this->limpiaCampo($estatus);
        $usernameModificacion = $this->limpiaCampo($usernameModificacion);

        if($username == "" || $modulo == "" || $accion == "" || $usernameModificacion == "")
   			return FALSE;
   
   		$paraTodos = FALSE;
   		if($modulo == "all")
   			$paraTodos = TRUE;

   		$fecha = date('Y-m-d H:i:s');

   		if(!$paraTodos){
			$instruccion = "UPDATE permisos SET 
							$accion = $estatus, 
							usernameModificacion = '$usernameModificacion', 
							fechaModificacion = '$fecha' 
							WHERE 
							username = '$username' AND 
							modulo = '$modulo'";
   		}
		else{
			$instruccion = "UPDATE permisos SET 
							$accion = $estatus, 
							usernameModificacion = '$usernameModificacion', 
							fechaModificacion = '$fecha' 
							WHERE 
							username = '$username'";
		}

		//return ($instruccion);
		$this->db->query($instruccion);
		
		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
   }
   
   
   public function reportePermisos($nombreUsuario="",$paginaInicio=0, $numeroRegistros=10, $total=FALSE){
   
   		$nombreUsuario = $this->limpiaCampo($nombreUsuario);
		
		if($numeroRegistros<=0)
			$numeroRegistros = 10;
		
		$instruccion = "SELECT usuario.username, modulo.modulo, altas, bajas, cambios, consultas, 
						habilitado AS esDisponible, permisos.usernameModificacion, permisos.fechaModificacion 
						FROM usuario, permisos, modulo 
						WHERE 
						usuario.username = permisos.username AND 
						modulo.modulo = permisos.modulo AND 
						modulo.habilitado = '1' ";
			
		$donde = "";	
		if($nombreUsuario != "")
			$donde = "AND usuario.username LIKE '%$nombreUsuario%' "; 
		
			
		$limite = "";
		if(!$total)
			$limite = " ORDER BY username ASC limit $numeroRegistros OFFSET $paginaInicio";
		
		if($donde!="")
			$instruccion .= " $donde ";
		
		$instruccion .= $limite;
        
        //die($instruccion);			
		$query = $this->db->query($instruccion);
		if(!$total && isset($query) && $query->num_rows()>0)
			return $query->result();
		else if($total){
			if(isset($query) && $query->num_rows()>0)
				return $query->num_rows();
			else
				return 0;
		}
		return null;
   }
}



