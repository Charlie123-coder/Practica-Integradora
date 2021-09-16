.
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LibroModelo extends MY_Model {

	public function __construct()
	{
		parent::__construct();
		//Do your magic here

	}

	public function reporteLibros($nombre="",$categoria="",$paginaInicio=0,$numeroRegistros=10,$total=FALSE){
		$nombre = $this->limpiaCampo($nombre);
		$categoria = $this->limpiaCampo($categoria);
		
		$paginaInicio = $this->limpiaCampo($paginaInicio);
		$numeroRegistros = $this->limpiaCampo($numeroRegistros);

		if($total===FALSE)
			$instruccion = "SELECT * FROM libro";
		else
			$instruccion = "SELECT count(*) as total FROM libro";

		$busqueda = "";

		if($nombre != "" || $categoria != "" ){
			$instruccion .= " WHERE";

			if($nombre != "")
				$busqueda = " nombre_lib LIKE '%$nombre%'";
			if($categoria != "")
				$busqueda = " categoria_lib LIKE '%$categoria%'";

			// Si se quiere buscar lugares a partir de un punto y un radio
			
		}

		$instruccion .= $busqueda;
		if($total===FALSE){
			$instruccion .= " ORDER BY nombre_lib ASC LIMIT $numeroRegistros OFFSET $paginaInicio";
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

	public function reporteLibrosGraficas(){
			
		$paginaInicio = 0;
		$numeroRegistros = 10;

		$instruccion = "SELECT categoria_lib, COUNT(*) as cantidad FROM libro GROUP BY categoria_lib";	
		//die($instruccion);

		$query = $this->db->query($instruccion);

			if(isset($query) && $query->num_rows()>0){
				$arreglo = $query->result();
				return $arreglo;
			}
		return null;
	}

	public function obtenDatosLibro($libro_id = ""){
		$libro_id = $this->limpiaCampo($libro_id);
		
		if($libro_id == "")
			return FALSE;

		$instruccion = "SELECT * FROM libro WHERE libro_id like '$libro_id'";

		$query = $this->db->query($instruccion);

		if(isset($query) && $query->num_rows()>0)
			return $query->row();
		return null;
	}

	public function insertaLibro($nombre="",$descripcion="",$categoria="",$copias=""){
		$nombre = $this->limpiaCampo($nombre);
		$descripcion = $this->limpiaCampo($descripcion);
		$categoria = $this->limpiaCampo($categoria);
		$copias = $this->limpiaCampo($copias);

		if($nombre == "" || $descripcion == "" || $categoria == "" || $copias=="")
			return FALSE;


		$instruccion = "INSERT INTO libro (nombre_lib,descr_lib,copias_lib,categoria_lib) 
						VALUES 
						('$nombre','$descripcion','$copias','$categoria')";

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}

	public function editarLibro($idLibroAnterior = "", $libro_id = "", $nombre="",$descripcion = "", $categoria = "", $copias=""){
		$idLibroAnterior = $this->limpiaCampo($idLibroAnterior);
		$libro_id = $this->limpiaCampo($libro_id);
		$nombre = $this->limpiaCampo($nombre);
		$descripcion = $this->limpiaCampo($descripcion);
		$categoria = $this->limpiaCampo($categoria);
		$copias = $this->limpiaCampo($copias);
		

		if($idLibroAnterior == "" || $libro_id == "" || $nombre == "" || $descripcion=="" || $categoria == "" || $copias == ""){
			echo "hola editando";
			return FALSE;
		}

		$data = array(
			'libro_id' => $libro_id,
			'nombre_lib' => $nombre,
			'descr_lib' => $descripcion,
			'copias_lib' => $copias,
			'categoria_lib' => $categoria,
			);
		$this->db->update('libro', $data, array('libro_id' => $idLibroAnterior));

		// $this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}

	public function borrarLibro($libro_id=""){
		$libro_id = $this->limpiaCampo($libro_id);

		if($libro_id == "")
			return FALSE;

		$instruccion = "DELETE FROM libro WHERE libro_id='$libro_id'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


}

/* End of file LibroModelo.php */
/* Location: ./application/models/LibroModelo.php */