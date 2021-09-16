<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Biblioteca extends MY_Controller {

	public function index()
	{
		
		$this->load->view('componentes/encabezadoFecha', $data);
		$this->load->view('biblioteca/listaBibliotecas', $data, FALSE);
		$this->load->view('componentes/piePagina', $data);

	}

}

/* End of file Biblioteca.php */
/* Location: ./application/controllers/Biblioteca.php */