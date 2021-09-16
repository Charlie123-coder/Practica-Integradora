<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TemperaturaModelo extends MY_Model {

	function __construct(){
        parent::__construct();
    }

	public function reporteTemperaturas($lugar_id="",$fechaInicio="",$fechaFinal="",$temperaturaInicial="",
										$temperaturaFinal="",$paginaInicio=0,$numeroRegistros=10,$total=FALSE){
		$lugar_id = $this->limpiaCampo($lugar_id);
		$fechaInicio = $this->limpiaCampo($fechaInicio);
		$fechaFinal = $this->limpiaCampo($fechaFinal);
		$temperaturaInicial = $this->limpiaCampo($temperaturaInicial);
		$temperaturaFinal = $this->limpiaCampo($temperaturaFinal);
		$paginaInicio = $this->limpiaCampo($paginaInicio);
		$numeroRegistros = $this->limpiaCampo($numeroRegistros);

		if($total===FALSE)
			$instruccion = "SELECT lugar.nombre, temperatura.*  FROM lugar INNER JOIN temperatura ON lugar.id=temperatura.lugar_id";
		else
			$instruccion = "SELECT COUNT(*) AS total  FROM lugar INNER JOIN temperatura ON lugar.id=temperatura.lugar_id";

		$busqueda = "";

		if($lugar_id != "" || $fechaInicio != "" || $fechaFinal != "" && 
			$temperaturaInicial != "" && $temperaturaFinal != ""){
			$instruccion .= " WHERE";

			if($lugar_id != "")
				$busqueda = " lugar.id = '$lugar_id'";


			if($fechaInicio != ""){
				if($busqueda != "")
					$busqueda .= " AND";
				$busqueda .= " temperatura.fecha >= '$fechaInicio 00:00:00'"; 
			}

			if($fechaFinal != ""){
				if($busqueda != "")
					$busqueda .= " AND ";
				$busqueda .= "temperatura.fecha <= '$fechaFinal 23:59:59'";
			}

			if($temperaturaInicial != ""){
				if($busqueda != "")
					$busqueda .= " AND";
				$busqueda .= " temperatura.valor >= '$temperaturaInicial'"; 
			}

			if($temperaturaFinal != ""){
				if($busqueda != "")
					$busqueda .= " AND";
				$busqueda .= " temperatura.valor <= '$temperaturaFinal'"; 
			}
		}

		$instruccion .= $busqueda;
		if($total===FALSE){
			$instruccion .= " ORDER BY fecha DESC LIMIT $numeroRegistros OFFSET $paginaInicio";
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


	
	public function insertaTemperatura($lugar_id="",$fecha="",$valor=""){
		$lugar_id = $this->limpiaCampo($lugar_id);
		$fecha = $this->limpiaCampo($fecha);
		$valor = $this->limpiaCampo($valor);

		if($lugar_id == "" || $fecha == "" || $valor == "")
			return FALSE;

		$instruccion = "INSERT INTO temperatura (lugar_id,fecha,valor) 
						VALUES 
						('$lugar_id','$fecha','$valor')";

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


	public function borrarTemperatura($lugar_id="",$fecha=""){
		$lugar_id = $this->limpiaCampo($lugar_id);
		$fecha = $this->limpiaCampo($fecha);

		if($lugar_id == "" || $fecha == "")
			return FALSE;

		$instruccion = "DELETE FROM temperatura WHERE lugar_id='$lugar_id' AND fecha='$fecha'"; 

		$this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


	public function obtenDatosTemperatura($lugar_id = "", $fecha = ""){
		$lugar_id = $this->limpiaCampo($lugar_id);
		
		if($lugar_id == "" && $fecha == "")
			return FALSE;

		$instruccion = "SELECT * FROM temperatura WHERE lugar_id like '$lugar_id' AND fecha like '$fecha'";

		$query = $this->db->query($instruccion);

		if(isset($query) && $query->num_rows()>0)
			return $query->row();
		return null;
	}

	
	public function editarTemperatura($idLugarAnterior = "", $lugar_id = "", $fecha="",$fechaAnterior = "", $valor = ""){
		$idLugarAnterior = $this->limpiaCampo($idLugarAnterior);
		$lugar_id = $this->limpiaCampo($lugar_id);
		$fecha = $this->limpiaCampo($fecha);
		$fechaAnterior = $this->limpiaCampo($fechaAnterior);
		$valor = $this->limpiaCampo($valor);
		
		print( $fechaAnterior);

		if($idLugarAnterior == "" || $lugar_id == "" || $fecha == "" || $fechaAnterior=="" || $valor == ""){
			echo "hola";
			return FALSE;
		}

		// $instruccion = "UPDATE temperatura SET 
		// 				lugar_id='$lugar_id',
		// 				fecha = '$fecha',
		// 				valor = '$valor'
		// 				WHERE
		// 				lugar_id='$idLugarAnterior' AND fecha like '$fechaAnterior'"; 

		$data = array(
			'lugar_id' => $lugar_id,
			'fecha' => $fecha,
			'valor' => $valor
			);
		$this->db->update('temperatura', $data, array('lugar_id' => $idLugarAnterior, 'fecha' => $fechaAnterior));

		// $this->db->query($instruccion);

		return ($this->db->affected_rows() != 1) ? FALSE : TRUE;
	}


}












