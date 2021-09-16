<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends PidePassword {

	private $modulo;

	function __construct(){
        parent::__construct();
        $this->modulo = "Usuario";
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

		$username = $this->obtenEntrada("username");
		$email = $this->obtenEntrada("email");

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
								'username' => $username,
								'email' => $email,
								'paginaInicio' => $paginaInicio,
								'numeroRegistros' => $numeroRegistros
								);

		$this->form_validation->set_data($datosValidar);


		$this->form_validation->set_rules('username','username','trim|callback_validaUsername|max_length[30]');
		$this->form_validation->set_rules('email','email','trim|max_length[255]');
		$this->form_validation->set_rules('paginaInicio','página de inicio','trim|integer');
		$this->form_validation->set_rules('numeroRegistros','registros por pagina','trim|integer|less_than_equal_to[1000]');
		

		if($this->form_validation->run() === FALSE){
			$datos = array();
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Usuarios del sitio";
			$datos['usuarios'] = array();
			$datos['username'] = $username;
			$datos['email'] = $email;

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;
		
			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('usuarios/listaUsuarios',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}


		$this->load->model('UsuarioModelo');
		$total = $this->UsuarioModelo->reporteUsuarios($username, $email,$paginaInicio,$numeroRegistros,TRUE);
		$usuarios = $this->UsuarioModelo->reporteUsuarios($username, $email,$paginaInicio,$numeroRegistros);
		$consulta = "?username=$username&email=$email";
		$paginacion = paginacion(base_url()."index.php/usuario/index",$paginaInicio,$numeroRegistros,$consulta,$total);


		$datos = array();
		$datos['paginaActual'] = "usuarios";
		$datos['titulo'] = "Usuarios del sitio";
		$datos['usuarios'] = $usuarios;
		$datos['username'] = $username;
		$datos['email'] = $email;
		$datos['paginacion'] = $paginacion;

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;
	
		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('usuarios/listaUsuarios',$datos);
		$this->load->view('componentes/piePagina');
	}

	public function agregarUsuario(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"altas") === FALSE){
			$error = "No tiene permisos para dar de alta usuarios";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}
		
		// Se presionó el botón que muestra la forma
		if(empty($_POST)){
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Agregar usuario";

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;

			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('usuarios/formaUsuario',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}

		// Se presionó el botón de la forma y los datos vienen en el arreglo
		// POST
		
		$username = $this->obtenEntrada("username");
		$email = $this->obtenEntrada("email");
		$password = $this->obtenEntrada("password");
		$password2 = $this->obtenEntrada("password2");
		
		$this->form_validation->set_rules('username','nombre de usuario','trim|required|callback_validaUsername|min_length[3]|max_length[30]');
		$this->form_validation->set_rules('email','email','trim|required|valid_email');
		$this->form_validation->set_rules('password','contraseña','trim|required|min_length[8]');
		$this->form_validation->set_rules('password2','repetir contraseña','trim|required|min_length[8]|matches[password]');
		
		if($this->form_validation->run() === FALSE){
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Agregar usuario";

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;

			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('usuarios/formaUsuario',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}

		$this->load->model('UsuarioModelo');
		if($this->UsuarioModelo->obtenDatosUsuario($username) !== null){
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "No se pudo agregar al usuario, el usuario ya existe";

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;

			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('usuarios/formaUsuario',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}


		if($this->UsuarioModelo->insertaUsuario($username, $email,$password, $user)){
			redirect(base_url()."index.php/usuario/");
			return;
		}
		
		$datos['paginaActual'] = "usuarios";
		$datos['titulo'] = "No se pudo agregar al usuario";

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('usuarios/formaUsuario',$datos);
		$this->load->view('componentes/piePagina');
		
	}


	public function borrarUsuario(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"bajas") === FALSE){
			$error = "No tiene permisos para dar de baja usuarios";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}


		$username = $this->obtenEntrada("username");
		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
								'username' => $username,
								);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('username','username','trim|callback_validaUsername|min_length[3]|max_length[30]');


		if($this->form_validation->run() === FALSE){
			$error = "Error al recibir los datos del usuario $username";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}

		$this->load->model('UsuarioModelo');
		if($this->UsuarioModelo->borrarUsuario($username)){
			redirect(base_url()."index.php/usuario/");
			return;
		}

		$error = "Error al borrar el usuario $username";
		$this->mostrarPaginaError("usuarios",$error,$error);
	}



	public function editarUsuario(){

		$user = $this->session->userdata('username');

    	$this->load->model('PermisosModelo');
    	if($this->PermisosModelo->tienePermiso($user,$this->modulo,"cambios") === FALSE){
			$error = "No tiene permisos para cambiar usuarios";
			$this->mostrarPaginaError("usuarios",$error,$error);
			return;
		}

		if(empty($_POST)){
			$username = $this->obtenEntrada("username");
			// Como vienen los datos por GET se ponen las siguientes dos instrucciones
			$datosValidar = array(
									'username' => $username,
									);

			$this->form_validation->set_data($datosValidar);
			$this->form_validation->set_rules('username','username','trim|callback_validaUsername|min_length[3]|max_length[30]');


			if($this->form_validation->run() === FALSE){
				$error = "Error al borrar el usuario $username";
				$this->mostrarPaginaError("usuarios",$error,$error);
				return;
			}

			$this->load->model('UsuarioModelo');
			$usuario = $this->UsuarioModelo->obtenDatosUsuario($username);

			//die(print_r($usuario));
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Editar usuario";
			$datos['usernameAnterior'] = $username;
			$datos['username'] = $usuario->username;
			$datos['email'] = $usuario->email;
			$datos['url'] = base_url()."index.php/usuario/editarUsuario";

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;

			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('usuarios/formaUsuario',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}
		
		$usernameAnterior = $this->obtenEntrada("usernameAnterior");
		$username = $this->obtenEntrada("username");
		$email = $this->obtenEntrada("email");
		$password = $this->obtenEntrada("password");
		$password2 = $this->obtenEntrada("password2");
		
		$this->form_validation->set_rules('usernameAnterior','nombre de usuario','trim|required|callback_validaUsername|min_length[3]|max_length[30]');
		$this->form_validation->set_rules('username','nombre de usuario','trim|required|callback_validaUsername|min_length[3]|max_length[30]');
		$this->form_validation->set_rules('email','email','trim|valid_email');
		$this->form_validation->set_rules('password','contraseña','trim|min_length[8]');
		$this->form_validation->set_rules('password2','repetir contraseña','trim|min_length[8]|matches[password]');
		
		if($this->form_validation->run() === FALSE){
			$datos['paginaActual'] = "usuarios";
			$datos['titulo'] = "Editar usuario";
			$datos['url'] = base_url()."index.php/usuario/editarUsuario";

			$usuarioLogeado = $this->session->userdata('username');
			$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
			$datos['lista_permisos'] = $lista_permisos;

			$this->load->view('componentes/encabezado',$datos);
			$this->load->view('componentes/menuAdministracion',$datos);
			$this->load->view('usuarios/formaUsuario',$datos);
			$this->load->view('componentes/piePagina');
			return;
		}

		$this->load->model('UsuarioModelo');
		if($this->UsuarioModelo->editarUsuario($usernameAnterior, $email, $password, $username, $user)){
			redirect(base_url()."index.php/usuario/");
			return;
		}

		$error = "Error al editar el usuario $username";
		$this->mostrarPaginaError("usuarios",$error,$error);
	}


}









