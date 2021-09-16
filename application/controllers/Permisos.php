<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos extends PidePassword {

	private $modulo;

    function __construct(){
        parent::__construct();
        $this->modulo = "Permisos";
    }

    public function index($paginaInicio=0,$numeroRegistros=10) {
      	$this->muestraPermisos($paginaInicio,$numeroRegistros);
    }
    
    
    /*
    	Los datos de paginaInicio y numeroRegistros los recibirá en la URL
    	index.php/permisos/muestraPermisos/110/10
    	En este caso paginaInicio es 110 y numeroRegistros es 10
    */
    public function muestraPermisos($paginaInicio=0,$numeroRegistros=10){
    
	    $user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"consultas") === FALSE){
			$error = "No tiene permisos para consultar";
			$this->mostrarPaginaError("permisos",$error,$error);
			return;
		}
		    
    				
		$nombreUsuario = $this->obtenEntrada('nombreUsuario');
		
		// Para validar cuando llegan datos con GET	
		$datosValidar = array(
								'paginaInicio' => $paginaInicio, 
								'numeroRegistros' => $numeroRegistros, 
								'nombreUsuario' => $nombreUsuario
							);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('nombreUsuario','nombre de usuario','trim|callback_validaUsername|max_length[30]');
		$this->form_validation->set_rules('paginaInicio','página de inicio','trim|integer');
		$this->form_validation->set_rules('numeroRegistros','registros por pagina','trim|integer|less_than_equal_to[1000]');
	
		
		if($this->form_validation->run() === TRUE)
			$error = FALSE;
		else
			$error = "Verifique los datos introducidos";

		$this->muestraListaUsuarios($paginaInicio,$numeroRegistros,$nombreUsuario,$error);
	}

	
	
	private function muestraListaUsuarios($paginaInicio=0,$numeroRegistros=10,$nombreUsuario="",$error=FALSE){
		$registrosTotales = 0;
		$registros = array();
		
		if($error === FALSE){
			$this->load->model('PermisosModelo'); 
			$registrosTotales = $this->PermisosModelo->reportePermisos($nombreUsuario,$paginaInicio,$numeroRegistros,TRUE);
			$registros = $this->PermisosModelo->reportePermisos($nombreUsuario,$paginaInicio,$numeroRegistros);
		}
		
		
		$consulta = "?nombreUsuario=$nombreUsuario";
		$paginacion = paginacion(base_url()."index.php/permisos/index",$paginaInicio,$numeroRegistros,$consulta,$registrosTotales);

		$datos = array();
		$datos['paginaActual'] = "usuarios";
		$datos['titulo'] = "Permisos de los usuarios";
		$datos['registros'] = $registros;
		$datos['nombreUsuario'] = $nombreUsuario;
		$datos['paginacion'] = $paginacion;
		$datos['mensaje'] = $error;
		
		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('permisos/reportePermisos',$datos);
		$this->load->view('componentes/piePagina');
    }


    
	

    /** Método para cambiar el estatus del permiso via ajax
		/index.php/permisos/cambiarPermiso/0/username/modulo/accion
		accion = {altas, bajas, cambios, consultas}
    **/
	public function cambiarPermiso($estatus="", $username="", $modulo= "", $accion = ""){
		
		$objeto = new stdClass();
	   	$objeto->resultado = "false";
	   	$objeto->titulo = "";
	   	$objeto->mensaje = "";

	   
	    $user = $this->session->userdata('username');
    	$this->load->model('PermisosModelo');

    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"cambios") === FALSE){
    		$objeto->titulo = "Error en usuario";
	   		$objeto->mensaje = "El usuario no tiene permisos para realizar los cambios";
	   		exit(json_encode($objeto));
    	}

    	// Para validar cuando llegan datos con GET	
		$datosValidar = array(
								'estatus' => $estatus, 
								'username' => $username,
								'modulo' => $modulo,
								'accion' => $accion,
							);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('estatus','estatus','trim|required|callback_validaStringBoolean');
		$this->form_validation->set_rules('username','nombre de usuario','trim|required|callback_validaUsername|max_length[30]');
		$this->form_validation->set_rules('modulo','módulo','trim|required|alpha_numeric');
		$this->form_validation->set_rules('accion','accion','trim|required|alpha_numeric');
	
		
		if($this->form_validation->run() !== TRUE){
			$objeto->titulo = "Error en los datos de entrada";
	   		$objeto->mensaje = validation_errors();
	   		exit(json_encode($objeto));
		}
		

		$this->load->model('PermisosModelo');
   
		if($estatus == 'true' || $estatus == 'TRUE')
			$resultado = $this->PermisosModelo->cambiarEstatusPermiso($username,$modulo,$accion,1,$user);
		else
			$resultado = $this->PermisosModelo->cambiarEstatusPermiso($username,$modulo,$accion,0,$user);
		
		
		if($resultado === TRUE){
			$objeto->resultado = "true";
			$objeto->titulo = "Se actualizó el permiso";
			$objeto->mensaje = "Se actualiz&oacute; correctamente el permiso";
			exit(json_encode($objeto));
		}

		$objeto->titulo = "Error al actualizar el permiso";
		$objeto->mensaje = "No se pudo actualizar el permiso";
		exit(json_encode($objeto));

	}
		
}
