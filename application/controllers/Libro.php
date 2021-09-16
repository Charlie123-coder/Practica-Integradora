<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Libro extends PidePassword {
	private $modulo;
	public function __construct()
	{
		parent::__construct();
		//Do your magic here
		$this->modulo = "Libro";
	}

	public function index($paginaInicio=0,$numeroRegistros=10){
		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"consultas") === FALSE){
			$error = "No tiene permisos para consultar";
			$this->mostrarPaginaError("libro",$error,$error);
			return;
		}

		// Se reciben los datos para la búsqueda
		$nombre = $this->obtenEntrada("nombre");
		$categoria = $this->obtenEntrada("categoria");
		$copias = $this->obtenEntrada("copias");
		$descripcion = $this->obtenEntrada("descripcion");

		//TODO: Faltan los demas campos

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
			'nombre' => $nombre,
			'categoria' => $categoria,
			'paginaInicio' => $paginaInicio,
			'numeroRegistros' => $numeroRegistros
		);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('nombre','nombre','trim|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('categoria','categoria','trim|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('paginaInicio','página de inicio','trim|integer');
		$this->form_validation->set_rules('numeroRegistros','registros por pagina','trim|integer|less_than_equal_to[1000]');

		if($this->form_validation->run() === FALSE){
			$this->listaLibros($nombre,$categoria,"");
			return;
		}

		$this->load->model('LibroModelo');
		$total = $this->LibroModelo->reporteLibros($nombre,$categoria,$paginaInicio,$numeroRegistros,TRUE);//falta editar los atributps
		$libros = $this->LibroModelo->reporteLibros($nombre,$categoria,$paginaInicio,$numeroRegistros);
		$consulta = "?nombre=$nombre&categoria=$categoria";
		$paginacion = paginacion(base_url()."index.php/libro/index",$paginaInicio,$numeroRegistros,$consulta,$total);

		$this->listaLibros($nombre,$categoria,$paginacion,$libros);
	}

	private function listaLibros($nombre="",$categoria="",$paginacion="",$libros=array()){
		$datos = array();
		$datos['paginaActual'] = "libro";
		$datos['titulo'] = "Libros";
		// La lista de libros
		$datos['libros'] = $libros;
		$datos['paginacion'] = $paginacion;

		// Se envían los datos que se recibieron para la búsqueda
		$datos['nombre'] = $nombre;
		$datos['categoria'] = $categoria;
		
		

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('libro/listaLibros',$datos);
		$this->load->view('componentes/piePagina');
	}


	public function agregarLibro(){

		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"altas") === FALSE){
			$error = "No tiene permisos para agregar";
			$this->mostrarPaginaError("libro",$error,$error);
			return;
		}

		// Se presionó el botón verde que está donde se muestra la lista de lugares, por lo que viene 
		// por medio de GET
		if(empty($_POST)){
			$this->muestraFormaLibro();
			return;
		}
		
		// Se presionó el botón de la forma y los datos vienen en el arreglo
		// POST
		$nombre = $this->obtenEntrada("nombre");
		$descripcion = $this->obtenEntrada("descripcion");
		$categoria = $this->obtenEntrada("categoria");
		$copias = $this->obtenEntrada("copias");
		

		$this->form_validation->set_rules('nombre','nombre','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('descripcion','descripcion','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('categoria','categoria','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('copias','copias','trim|required');

		if($this->form_validation->run() === FALSE){
			//$error = "Revise los datos ".validation_errors();
			$error = "";
			$this->muestraFormaLibro("libro","Agregar libro",$error);
			return;
		}

		$this->load->model('LibroModelo');
		if($this->LibroModelo->insertaLibro($nombre,$descripcion,$categoria,$copias)){
			redirect(base_url()."index.php/libro");
			return;
		}	

		$this->muestraFormaLibro("libro","Agregar libro","No se pudo agregar el libro");
	}

	public function muestraFormaLibro($paginaActual="libro",$titulo="Agregar libro",$error="",$idLibroAnterior="",$id="",
		$nombre="",$descripcion="",$categoria="",$copias="",$url=""){

		$datos = array();
		$datos['paginaActual'] = $paginaActual;
		$datos['titulo'] = $titulo;
		$datos['url'] = $url;
		$datos['error'] = $error;

		$datos['idLibroAnterior'] = $idLibroAnterior;
		$datos['libro_id'] = $id;
		$datos['nombre_lib'] = $nombre;
		$datos['descr_lib'] = $descripcion;
		$datos['categoria_lib'] = $categoria;
		$datos['copias_lib'] = $copias;

		$this->load->model('PermisosModelo');
		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;

		$this->load->view('componentes/encabezado',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('libro/formaLibro',$datos);
		$this->load->view('componentes/piePagina');	
	}

	public function editarLibro(){
		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"cambios") === FALSE){
			$error = "No tiene permisos para editar";
			$this->mostrarPaginaError("libro",$error,$error);
			return;
		}

		// Si está vacío el POST tiene que cargar la forma con los datos del módulo
		if(empty($_POST)){
			$libro_id = $this->obtenEntrada("id");
			// $fecha = $this->obtenEntrada("fecha");

			// Como vienen los datos por GET se ponen las siguientes dos instrucciones
			$datosValidar = array(
				'libro_id' => $libro_id,
				// 'fecha' => $fecha,
			);

			$this->form_validation->set_data($datosValidar);
			$this->form_validation->set_rules('libro_id','id del libro','trim|required|integer|greater_than[0]');
			// $this->form_validation->set_rules('fecha','Fecha','required');

			if($this->form_validation->run() === FALSE){
				$error = "Error al editar el libro con id: $libro_id";
				$this->mostrarPaginaError("libro",$error,$error);
				return;
			}

			$this->load->model('LibroModelo');
			$objetoLibro = $this->LibroModelo->obtenDatosLibro($libro_id);

			if($objetoLibro === null || $objetoLibro === FALSE){
				$error = "Error al editar el libro con el id: $libro_id, no existe";
				$this->mostrarPaginaError("libro",$error,$error);
				return;
			}

			$this->muestraFormaLibro("libro","Editar libro","",$libro_id, $libro_id, $objetoLibro->nombre_lib, $objetoLibro->descr_lib,$objetoLibro->categoria_lib,$objetoLibro->copias_lib,base_url()."index.php/libro/editarLibro");
			return;
		}
		
		$idLibroAnterior = $this->obtenEntrada("idLibroAnterior"); //escondido
		$libro_id = $this->obtenEntrada("id"); //El que aparece en formulario
		$nombre = $this->obtenEntrada("nombre"); //escondido
		$descripcion = $this->obtenEntrada("descripcion"); //en formulario
		$categoria = $this->obtenEntrada("categoria"); //en formulario
		$copias = $this->obtenEntrada("copias"); //en formulario
		
		

		$this->form_validation->set_rules('idLibroAnterior','id del libro anterior','trim|required|integer|greater_than[0]');
		$this->form_validation->set_rules('libro_id', 'libro_id', 'trim|integer|greater_than[0]');
		$this->form_validation->set_rules('nombre','nombre','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('descripcion','descripcion','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('categoria','categoria','trim|required|callback_validaAlfanumericoAcentosEspacio|max_length[255]');
		$this->form_validation->set_rules('copias','copias','trim|required');

		
		
		if($this->form_validation->run() === FALSE){

			$this->muestraFormaTemperatura("libro","Editar libro","",
				$idLibroAnterior,$libro_id,$nombre,$descripcion,$categoria,$copias,
				base_url()."index.php/libro/editarLibro");
			return;
		}

		$this->load->model('LibroModelo');
		$objetoLibro = $this->LibroModelo->obtenDatosLibro($libro_id);


		// if($objetoLibro !== null && $objetoLibro !== FALSE){
		// 	$error = "Error al editar el lugar con id: $lugar_id, ya existe";

		// 	$this->muestraFormaTemperatura("temperatura","Editar temperatura","",
		// 		$fechaAnterior,$idLugarAnterior,$lugar_id, $fecha,$valor,
		// 		base_url()."index.php/temperatura/editarTemperatura");
		// 	print($fechaAnterior);
		// 	return;
		// }
		


		if($this->LibroModelo->editarLibro($idLibroAnterior, $libro_id,$nombre,$descripcion, $categoria, $copias)){
			redirect(base_url()."index.php/libro/");
			return;
		}

		$error = "Error al editar el lugar con id: $libro_id";
		$this->mostrarPaginaError("libro",$error,$error);
	}

	public function borrarLibro(){

		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"bajas") === FALSE){
			$error = "No tiene permisos para borrar";
			$this->mostrarPaginaError("libro",$error,$error);
			return;
		}

		$libro_id = $this->obtenEntrada("id");
		

		// Como vienen los datos por GET se ponen las siguientes dos instrucciones
		$datosValidar = array(
			'libro_id' => $libro_id,
		);

		$this->form_validation->set_data($datosValidar);
		$this->form_validation->set_rules('libro_id','id del lugar','trim|required|integer|greater_than[0]');

		if($this->form_validation->run() === FALSE){
			$error = "Error al recibir los datos del libro con id: $libro_id";
			$this->mostrarPaginaError("libro",$error,$error);
			return;
		}

		$this->load->model('LibroModelo');
		if($this->LibroModelo->borrarLibro($libro_id)){
			redirect(base_url()."index.php/libro/");
			return;
		}

		$error = "Error al recibir los datos del libro con id: $libro_id";
		$this->mostrarPaginaError("libro",$error,$error);
	}

	public function verGrafica($paginaInicio=0,$numeroRegistros=10)
	{
		$user = $this->session->userdata('username');

		$this->load->model('PermisosModelo');
		if($this->PermisosModelo->tienePermiso($user,$this->modulo,"consultas") === FALSE){
			$error = "No tiene permisos para consultar";
			$this->mostrarPaginaError("libro",$error,$error);
			return;
		}

		$this->load->model('LibroModelo');
		$libros = $this->LibroModelo->reporteLibrosGraficas();
		
		// print_r($libros);
		$this->mostrarGraficaCategorias("Gráficas","",$libros);
	}

	private function mostrarGraficaCategorias($titulo="Gráficas",$error="",$categorias=array()){
		$datos = array();
		$datos['paginaActual'] = "libro";
		$datos['titulo'] = $titulo;
		$datos['error'] = $error;
		// La lista de lugares
		$datos['categorias'] = $categorias;
		$datos['paginacion'] = 0;

		$usuarioLogeado = $this->session->userdata('username');
		$lista_permisos = $this->PermisosModelo->reportePermisos($usuarioLogeado,0, 1000);
		$datos['lista_permisos'] = $lista_permisos;
		
		$this->load->view('componentes/encabezadoGrafica',$datos);
		$this->load->view('componentes/menuAdministracion',$datos);
		$this->load->view('libro/mostrarGraficasCategorias',$datos);
		$this->load->view('componentes/piePagina');
	}
	

}

/* End of file libro.php */
/* Location: ./application/controllers/libro.php */