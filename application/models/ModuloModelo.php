<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModuloModelo extends MY_Model {

	function __construct(){
        parent::__construct();
    }

	public function reporteModulos($modulo = "", $habilitado = "",$paginaInicio=0,$numeroRegistros=10,$total=FALSE){
		$modulo = $this->limpiaCampo($modulo);
		$habilitado = $this->limpiaCampo($habilitado);
		$paginaInicio = $this->limpiaCampo($paginaInicio);
		$numeroRegistros = $this->limpiaCampo($numeroRegistros);

		if($total===FALSE)
			$instruccion = "SELECT * FROM modulo";
		else
			$instruccion = "SELECT count(*) as total FROM modulo";

		$busqueda = "";

		if($modulo != "" || $habilitado != ""){
			$instruccion .= " WHERE";

			if($modulo != "")
				$busqueda = " modulo LIKE '%$modulo%'";

			if($habilitado != ""){
				if($busqueda != "")
					$busqueda .= " AND";
				$busqueda .= " habilitado = '$habilitado'"; 
			}
		}

		$instruccion .= $busqueda;
		if($total===FALSE)
			$instruccion .= " ORDER BY modulo ASC LIMIT $numeroRegistros OFFSET $paginaInicio";
	
		//die($instruccion);

		$query = $this->db->query($instruccion);

		if($total === FALSE){
			if(isset($query) && $query->num_rows()>0)
				return $query->result();
		}
		else{
			if(isset($query) && $query->num_rows()>0){
				$arreglo = $query->result();
				return $arreglo[0]->total;
			}
			return 0;
		}

		return null;
	}


	
	public function insertaModulo($modulo = "", $habilitado = "", $usernameModificacion = ""){
		$modulo = $this->limpiaCampo($modulo);
		$habilitado = $this->limpiaCampo($habilitado);
		$usernameModificacion = $this->limpiaCampo($usernameModificacion);

		if($modulo == "" || $habilitado == "" || $usernameModificacion == "")
			return FALSE;

		$fecha = date('Y-m-d H:i:s'); // Para no depender de la fecha del servidor de base de datos

		$instruccion = "INSERT INTO modulo (modulo,habilitado,usernameModificacion,fechaModificacion) 
						VALUES 
						('$modulo','$habilitado','$usernameModificacion','$fecha')";

		$this->db->trans_start();

		$this->db->query($instruccion);

		if($this->db->affected_rows() != 1){
			$this->db->trans_rollback();
			return FALSE;
		}

		$instruccion = "SELECT username FROM usuario";

		$query = $this->db->query($instruccion);

		// Si no hay usuarios, que sería muy raro
		if(!isset($query) || $query->num_rows()<=0){
			$this->db->trans_commit();
			return TRUE;
		}
		
		$usuarios = $query->result();

		foreach ($usuarios as $usuario) {
			$username = $usuario->username;
			$instruccion = "INSERT INTO permisos
	       					(username, modulo,altas,bajas,cambios,consultas,usernameModificacion,fechaModificacion) 
	       					VALUES
	                     	('$username','$modulo',0,0,0,0,'$usernameModificacion','$fecha')";
	        $this->db->query($instruccion);
	        // Si no se pudieron insertar los permisos al usuario se cancela la transacción
	        if($this->db->affected_rows() != 1){
				$this->db->trans_rollback();
				return FALSE;
			}
		}

		$this->db->trans_commit();
		return TRUE;
	}


	public function obtenDatosModulo($modulo = ""){
		$modulo = $this->limpiaCampo($modulo);
		
		if($modulo == "")
			return FALSE;

		$instruccion = "SELECT * FROM modulo WHERE modulo='$modulo'";

		$query = $this->db->query($instruccion);

		if(isset($query) && $query->num_rows()>0)
			return $query->row();
		return null;
	}

	
	public function editarModulo($moduloAnterior = "", $habilitado = "", $moduloNuevo = "", $usernameModificacion = ""){
		$moduloAnterior = $this->limpiaCampo($moduloAnterior);
		$habilitado = $this->limpiaCampo($habilitado);
		$moduloNuevo = $this->limpiaCampo($moduloNuevo);
		$usernameModificacion = $this->limpiaCampo($usernameModificacion);

		if($moduloAnterior == "" || $usernameModificacion == "")
			return FALSE;

		$instruccion = "UPDATE modulo SET ";
		$valores = "";
		if($moduloNuevo != "" && $moduloAnterior != $moduloNuevo){
			$valores = "modulo='$moduloNuevo'";
		}

		if($habilitado != ""){
			if($valores != "")
				$valores .= ", ";
			$valores .= "habilitado='$habilitado'"; 
		}

		$fecha = date('Y-m-d H:i:s'); // Para no depender de la fecha del servidor de base de datos
		if($valores != "")
			$valores .= ", ";
		$valores .= "fechaModificacion='$fecha', usernameModificacion='$usernameModificacion'";

		$instruccion = $instruccion.$valores." WHERE modulo='$moduloAnterior'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


	public function borrarModulo($modulo=""){
		$modulo = $this->limpiaCampo($modulo);

		if($modulo == "")
			return FALSE;

		$instruccion = "DELETE FROM modulo WHERE modulo='$modulo'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


}












