<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modulo extends PidePassword {

	private $modulo;

	function __construct(){
        parent::__construct();
        $this->modulo = "Modulo";
    }

	public function index($paginaInicio=0,$numeroRegistros=10)
	{	

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"consultas") === FALSE){
			$error = "No tiene permisos para consultar";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}

		$modulo = $this->obtenEntrada("modulo");
		$habilitado = $this->obtenEntrada("habilitado");

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
								'modulo' => $modulo,
								'habilitado' => $habilitado,
								'paginaInicio' => $paginaInicio,
								'numeroRegistros' => $numeroRegistros
								);

		$this->form_validation->set_data($datosValidar);

		$this->form_validation->set_rules('modulo','módulo','trim|alpha_dash|max_length[45]');
		$this->form_validation->set_rules('habilitado','habilitado','trim|integer|max_length[1]');
		$this->form_validation->set_rules('paginaInicio','página de inicio','trim|integer');
		$this->form_validation->set_rules('numeroRegistros','registros por pagina','trim|integer|less_than_equal_to[1000]');

		if($this->form_validation->run() === FALSE){
			$datos = array();
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Módulos";
			$datos['modulos'] = array();
			$datos['modulo'] = $modulo;
			$datos['habilitado'] = $habilitado;

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;
		
			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('modulos/listaModulos',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}
		 

		$this->load->model('ModuloModelo');
		$total = $this->ModuloModelo->reporteModulos($modulo,$habilitado,$paginaInicio,$numeroRegistros,TRUE);
		$modulos = $this->ModuloModelo->reporteModulos($modulo,$habilitado,$paginaInicio,$numeroRegistros);
		$consulta = "?modulo=$modulo&habilitado=$habilitado";
		$paginacion = paginacion(base_url()."index.php/modulo/index",$paginaInicio,$numeroRegistros,$consulta,$total);


		$datos = array();
		$datos['paginaActual'] = "usuarios";
		$datos['titulo'] = "Módulos";
		$datos['modulos'] = $modulos;
		$datos['modulo'] = $modulo;
		$datos['habilitado'] = $habilitado;
		$datos['paginacion'] = $paginacion;

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;
	
		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('modulos/listaModulos',$datos);
		$this->load->view('componentes/piePagina');
	}


	

	public function agregarModulo(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"altas") === FALSE){
			$error = "No tiene permisos para agregar";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}

		// Se presionó el botón que muestra la forma
		if(empty($_POST)){
			$this->muestraFormaModulo();
			return;
		}

		// Se presionó el botón de la forma y los datos vienen en el arreglo
		// POST
		$modulo = $this->obtenEntrada("modulo");
		$habilitado = $this->obtenEntrada("habilitado");

		$this->form_validation->set_rules('modulo','módulo','trim|required|alpha_dash|max_length[45]');
		$this->form_validation->set_rules('habilitado','habilitado','trim|required|integer|max_length[1]');
		
		if($this->form_validation->run() === FALSE){
			$this->muestraFormaModulo();
			return;
		}

		$this->load->model('ModuloModelo');
		if($this->ModuloModelo->obtenDatosModulo($modulo) !== null){
			$this->muestraFormaModulo("modulos","Agregar módulo","El módulo $modulo ya existe");
			return;
		}

		if($this->ModuloModelo->insertaModulo($modulo, $habilitado, $user)){
			redirect(base_url()."index.php/modulo/");
			return;
		}	

		$this->muestraFormaModulo("modulos","Agregar módulo","No se pudo agregar el módulo");
	}


	public function muestraFormaModulo($paginaActual="usuarios",$titulo="Agregar módulo",$error=""){
		$datos = array();
		$datos['paginaActual'] = $paginaActual;
		$datos['titulo'] = $titulo;
		$datos['error'] = $error;

		$this->load->model('PermisosModelo');
		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('modulos/formaModulo',$datos);
		$this->load->view('componentes/piePagina');	
	}


	public function borrarModulo(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"bajas") === FALSE){
			$error = "No tiene permisos para borrar";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}

		$modulo = $this->obtenEntrada("modulo");
		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
								'modulo' => $modulo,
								);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('modulo','módulo','trim|required|alpha_dash|max_length[45]');


		if($this->form_validation->run() === FALSE){
			$error = "Error al recibir los datos del módulo $modulo";
			$this->mostrarPaginaError("modulos",$error,$error);
			return;
		}

		$this->load->model('ModuloModelo');
		if($this->ModuloModelo->borrarModulo($modulo)){
			redirect(base_url()."index.php/modulo/");
			return;
		}

		$error = "Error al borrar el módulo $modulo";
		$this->mostrarPaginaError("modulos",$error,$error);
	}



	public function editarModulo(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"cambios") === FALSE){
			$error = "No tiene permisos para editar";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}

		// Si está vacío el POST tiene que cargar la forma con los datos del módulo
		if(empty($_POST)){
			$modulo = $this->obtenEntrada("modulo");
			// Como vienen los datos por GET se ponen las siguientes dos instrucciones
			$datosValidar = array(
									'modulo' => $modulo,
									);

			$this->form_validation->set_data($datosValidar);
			$this->form_validation->set_rules('modulo','módulo','trim|required|alpha_dash|max_length[45]');

			if($this->form_validation->run() === FALSE){
				$error = "Error al editar el modulo $modulo";
				$this->mostrarPaginaError("modulos",$error,$error);
				return;
			}

			$this->load->model('ModuloModelo');
			$objetoModulo = $this->ModuloModelo->obtenDatosModulo($modulo);

			if($objetoModulo === null || $objetoModulo === FALSE){
				$error = "Error al editar el modulo $modulo, no existe";
				$this->mostrarPaginaError("modulos",$error,$error);
				return;
			}

			//die(print_r($objetoModulo));
			$datos = array();
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Editar módulo";
			$datos['moduloAnterior'] = $modulo;
			$datos['modulo'] = $objetoModulo->modulo;
			$datos['habilitado'] = $objetoModulo->habilitado;
			$datos['url'] = base_url()."index.php/modulo/editarModulo";

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;

			//die(print_r($datos));
			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('modulos/formaModulo',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}
		
		$moduloAnterior = $this->obtenEntrada("moduloAnterior");
		$modulo = $this->obtenEntrada("modulo");
		$habilitado = $this->obtenEntrada("habilitado");
		
		$this->form_validation->set_rules('moduloAnterior','módulo anterior','trim|required|alpha_dash|max_length[45]');
		$this->form_validation->set_rules('modulo','módulo','trim|required|alpha_dash|max_length[45]');
		$this->form_validation->set_rules('habilitado','habilitado','trim|required|integer|max_length[1]');
		
		if($this->form_validation->run() === FALSE){
			$datos = array();
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Editar módulo";
			$datos['url'] = base_url()."index.php/modulo/editarModulo";

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;

			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('modulos/formaModulo',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}

		$this->load->model('ModuloModelo');
		if($this->ModuloModelo->editarModulo($moduloAnterior, $habilitado, $modulo, $user)){
			redirect(base_url()."index.php/modulo/");
			return;
		}

		$error = "Error al editar el módulo $moduloAnterior";
		$this->mostrarPaginaError("modulos",$error,$error);
	}
	
}









