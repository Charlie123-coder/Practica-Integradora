<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LugarModelo extends MY_Model {

	function __construct(){
        parent::__construct();
    }

	public function reporteLugares($nombre="",$latitud="",$longitud="",$radio="",$paginaInicio=0,$numeroRegistros=10,$total=FALSE){
		$nombre = $this->limpiaCampo($nombre);
		$latitud = $this->limpiaCampo($latitud);
		$longitud = $this->limpiaCampo($longitud);
		$radio = $this->limpiaCampo($radio);
		$paginaInicio = $this->limpiaCampo($paginaInicio);
		$numeroRegistros = $this->limpiaCampo($numeroRegistros);

		if($total===FALSE)
			$instruccion = "SELECT * FROM lugar";
		else
			$instruccion = "SELECT count(*) as total FROM lugar";

		$busqueda = "";

		if($nombre != "" || $latitud != "" || $longitud != ""){
			$instruccion .= " WHERE";

			if($nombre != "")
				$busqueda = " nombre LIKE '%$nombre%'";

			// Si se quiere buscar lugares a partir de un punto y un radio
			if(!empty($latitud) && !empty($longitud) && !empty($radio)){
			
				if($busqueda != "")
					$busqueda .= " AND ";
				$busqueda .= "(6371 * acos(	
												cos(radians($latitud)) 
												* cos(radians(latitud)) 
												* cos(radians(longitud) - radians($longitud)) + sin(radians($latitud))
												* sin(radians(latitud))
												      )
										) <= $radio/1000";
			}
			else{

				if($latitud != ""){
					if($busqueda != "")
						$busqueda .= " AND";
					$busqueda .= " latitud = '$latitud'"; 
				}

				if($longitud != ""){
					if($busqueda != "")
						$busqueda .= " AND";
					$busqueda .= " longitud = '$longitud'"; 
				}
			}
		}

		$instruccion .= $busqueda;
		if($total===FALSE){
			$instruccion .= " ORDER BY nombre ASC LIMIT $numeroRegistros OFFSET $paginaInicio";
			//die($instruccion);
		}
	
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


	
	public function insertaLugar($nombre="",$latitud="",$longitud=""){
		$nombre = $this->limpiaCampo($nombre);
		$latitud = $this->limpiaCampo($latitud);
		$longitud = $this->limpiaCampo($longitud);

		if($nombre == "" || $latitud == "" || $longitud == "")
			return FALSE;


		$instruccion = "INSERT INTO lugar (nombre,latitud,longitud) 
						VALUES 
						('$nombre','$latitud','$longitud')";

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


	public function borrarLugar($id=""){
		$id = $this->limpiaCampo($id);

		if($id == "")
			return FALSE;

		$instruccion = "DELETE FROM lugar WHERE id='$id'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


	public function obtenDatosLugar($id = ""){
		$id = $this->limpiaCampo($id);
		
		if($id == "")
			return FALSE;

		$instruccion = "SELECT * FROM lugar WHERE id='$id'";

		$query = $this->db->query($instruccion);

		if(isset($query) && $query->num_rows()>0)
			return $query->row();
		return null;
	}

	
	public function editarLugar($idLugarAnterior = "", $id = "", $nombre = "", $latitud = "", $longitud = ""){
		$idLugarAnterior = $this->limpiaCampo($idLugarAnterior);
		$id = $this->limpiaCampo($id);
		$nombre = $this->limpiaCampo($nombre);
		$latitud = $this->limpiaCampo($latitud);
		$longitud = $this->limpiaCampo($longitud);

		if($idLugarAnterior == "" || $id == "" || $nombre == "" || $latitud == "" || $longitud == "")
			return FALSE;

		$instruccion = "UPDATE lugar SET 
						id='$id',
						nombre='$nombre',
						latitud='$latitud',
						longitud='$longitud'
						WHERE
						id='$idLugarAnterior'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


}
