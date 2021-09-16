<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuarioModelo extends MY_Model {

	function __construct(){
        parent::__construct();
    }


    public function esUsuario($username = "", $password = ""){
		$username = $this->limpiaCampo($username);
		$password = $this->limpiaCampo($password);

		if($username == "" || $password == "")
			return null;

		$frase = parent::LLAVE;

		$instruccion = "SELECT * FROM usuario WHERE
						username='$username' AND password = AES_ENCRYPT('$password','$frase')";

		$query = $this->db->query($instruccion);

		if(!isset($query) || $query->num_rows()<=0)
			return null;
			
		$result = $query->row_array();
		$nombre = $result['username'];

		if(empty($nombre) || $nombre == "")
			return null;

		$numero = random_int (0,9999999999);
		$sesion = password_hash($numero, PASSWORD_BCRYPT);

		$usuario = new stdClass();
		$usuario->username = $result['username'];
		$usuario->sesion = $sesion;

		$instruccion = "UPDATE usuario SET sesion='$sesion' WHERE 
						username='$username' AND password = AES_ENCRYPT('$password','$frase')";

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? null : $usuario;
	}
	

	public function revisaSesion($username="",$sesion=""){
		$username = $this->limpiaCampo($username);

		if($username == "" || $sesion == "")
			return FALSE;

		$instruccion = "SELECT sesion AS sesionLeida FROM usuario WHERE username='$username'";

		$query = $this->db->query($instruccion);
		if(!isset($query) || $query->num_rows()<=0)
			return FALSE;

		$result = $query->row_array();
		$sesionLeida = $result['sesionLeida'];

		if($sesionLeida == $sesion)
			return TRUE;

		return FALSE;
	}


	public function borrarSesion($username = ""){
		$username = $this->limpiaCampo($username);

		if($username == "")
			return FALSE;

		$instruccion = "UPDATE usuario SET sesion='' WHERE username='$username'";

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1)? FALSE : TRUE;
	}


	public function insertaUsuario($username = "", $email = "", $password = "", $usernameModificacion = ""){
		$username = $this->limpiaCampo($username);
		$email = $this->limpiaCampo($email);
		$password = $this->limpiaCampo($password);
		$usernameModificacion = $this->limpiaCampo($usernameModificacion);

		if($username == "" || $email == "" || $password == "" || $usernameModificacion == "")
			return FALSE;

		$frase = parent::LLAVE;
		$fecha = date('Y-m-d H:i:s'); // Para no depender de la fecha del servidor de base de datos

		$instruccion = "INSERT INTO 
						usuario (username,email,password,fechaModificacion, usernameModificacion) 
						VALUES 
						('$username','$email',AES_ENCRYPT('$password','$frase'),'$fecha','$usernameModificacion')";

		$this->db->trans_start();

		$this->db->query($instruccion);

		if($this->db->affected_rows() < 1){
			$this->db->trans_rollback();
			return FALSE;
		}

		$instruccion = "SELECT modulo FROM modulo";

		$query = $this->db->query($instruccion);

		// Si no hay modulos se sale
		if(!isset($query) || $query->num_rows()<=0){
			$this->db->trans_commit();
			return TRUE;
		}
		
		$modulos = $query->result();

		foreach ($modulos as $modulo) {
			$modulo = $modulo->modulo;
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


	public function reporteUsuarios($username = "",$email = "",$paginaInicio=0,$numeroRegistros=10,$total=FALSE){
		$username = $this->limpiaCampo($username);
		$email = $this->limpiaCampo($email);
		$paginaInicio = $this->limpiaCampo($paginaInicio);
		$numeroRegistros = $this->limpiaCampo($numeroRegistros);

		if($total===FALSE)
			$instruccion = "SELECT * FROM usuario";
		else
			$instruccion = "SELECT count(*) as total FROM usuario";

		$busqueda = "";
		if($username != "" || $email != ""){
			$instruccion .= " WHERE";

			if($username != "")
				$busqueda = " username LIKE '%$username%'";

			if($email != ""){
				if($busqueda != "")
					$busqueda .= " AND";
				$busqueda .= " email LIKE '%$email%'"; 
			}
		}

		$instruccion .= $busqueda;
		if($total===FALSE)
			$instruccion .= " ORDER BY username ASC LIMIT $numeroRegistros OFFSET $paginaInicio";

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


	public function obtenDatosUsuario($username = ""){
		$username = $this->limpiaCampo($username);
		
		if($username == "")
			return FALSE;

		$instruccion = "SELECT * FROM usuario WHERE username='$username'";

		$query = $this->db->query($instruccion);

		if(isset($query) && $query->num_rows()>0)
			return $query->row();
		return null;
	}


	public function editarUsuario($usernameAnterior = "", $email = "", $password = "", $usernameNuevo = "", $usernameModificacion=""){
		$usernameAnterior = $this->limpiaCampo($usernameAnterior);
		$email = $this->limpiaCampo($email);
		$password = $this->limpiaCampo($password);
		$usernameNuevo = $this->limpiaCampo($usernameNuevo);
		$usernameModificacion = $this->limpiaCampo($usernameModificacion);

		if($usernameAnterior == "" || $usernameModificacion == "")
			return FALSE;

		$instruccion = "UPDATE usuario SET ";
		$valores = "";
		if($usernameNuevo != "" && $usernameAnterior != $usernameNuevo){
			$valores = "username='$usernameNuevo'";
		}

		if($email != ""){
			if($valores != "")
				$valores .= ", ";
			$valores .= "email='$email'"; 
		}

		if($password != ""){
			if($valores != "")
				$valores .= ", ";
			$frase = parent::LLAVE;
			$valores .= "password=AES_ENCRYPT('$password','$frase')"; 
		}

		$fecha = date('Y-m-d H:i:s'); // Para no depender de la fecha del servidor de base de datos
		if($valores != "")
			$valores .= ", ";
		$valores .= "fechaModificacion='$fecha', usernameModificacion='$usernameModificacion'";

		$instruccion = $instruccion.$valores." WHERE username='$usernameAnterior'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1)? FALSE : TRUE;
	}


	public function borrarUsuario($username=""){
		$username = $this->limpiaCampo($username);

		if($username == "")
			return FALSE;

		$instruccion = "DELETE FROM usuario WHERE username='$username'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1)? FALSE : TRUE;
	}

}












