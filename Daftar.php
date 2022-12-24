<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Daftar_model','Daftar');
		
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('Daftar');
	}


	public function Daftar_list()
	{
		$list = $this->Daftar->get_datatables(); //this=>> berarti nama controller
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $maha) {
			$no++;
			$row = array();
			$row[] = $maha->kode;
			$row[] = $maha->nama;
			$row[] = $maha->jk;
			$row[] = $maha->tgl;
			
			if($maha->ijasah)
				$row[] = '<a href="'.base_url('berkas-upload/'.$maha->ijasah).'" target="_blank"><img width="60" height="60" src="'.base_url('berkas-upload/pdf.png').'" class="img-responsive" /></a>';
			else
				$row[] = '(Berkas Tidak Tersedia)';


			if($maha->foto)
				$row[] = '<a href="'.base_url('upload/'.$maha->foto).'" target="_blank"><img width="100" height="100" src="'.base_url('upload/'.$maha->foto).'" class="img-responsive" /></a>';
			else
				$row[] = '(Foto Tidak Tersedia)';

			//add html for action
			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_Daftar('."'".$maha->kode."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				  <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_Daftar('."'".$maha->kode."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
		
			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Daftar->count_all(), //this=>> berarti nama controller
						"recordsFiltered" => $this->Daftar->count_filtered(), //this=>> berarti nama controller
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}





	public function Daftar_edit($kode)
	{
		$data = $this->mahasiswa->get_by_kode($kode);
		//$data->tgl = ($data->tgl == '0000-00-00') ? '' : $data->tgl; // if 0000-00-00 set tu empty for datepicker compatibility
		echo json_encode($data);
	}

	public function Daftar_add()
	{
		$this->_validate();
		$data = array(
				'nim' => $this->input->post('kode'),
				'nama' => $this->input->post('nama'),
				'jk' => $this->input->post('jk'),
				'tgl' => $this->input->post('tgl'),
				

			);
		if(!empty($_FILES['ijasah']['name']))
        {
            $upload_pdf = $this->upload_pdf();
            $data['ijasah'] = $upload_pdf;
        }

		if(!empty($_FILES['foto']['name']))
        {
            $upload = $this->upload_foto();
            $data['foto'] = $upload;
        }		
		$insert = $this->mahasiswa->save($data);
		echo json_encode(array("status" => TRUE));
	}

	public function mhs_update()
	{
		$this->_validate();
		$data = array(
				'kode' => $this->input->post('kode'),
				'nama' => $this->input->post('nama'),
				'jk' => $this->input->post('jk'),
				'tgl' => $this->input->post('tgl'),
			);

		if($this->input->post('remove_ijasah'))// jika hapus foto di centang
		{
			if(file_exists('berkas-upload/'.$this->input->post('remove_ijasah')) && $this->input->post('remove_ijasah'))
				unlink('berkas-upload/'.$this->input->post('remove_ijasah'));
			$data['ijasah'] = '';
		}

		if(!empty($_FILES['ijasah']['name']))
		{
			$upload = $this->upload_pdf();
			
			//delete file
			$maha = $this->mahasiswa->get_by_nim($this->input->post('kode'));
			if(file_exists('upload/'.$maha->ijasah) && $maha->ijasah)
				unlink('upload/'.$maha->ijasah);

			$data['ijasah'] = $upload;
		}

		if($this->input->post('remove_photo'))// jika hapus foto di centang
		{
			if(file_exists('upload/'.$this->input->post('remove_photo')) && $this->input->post('remove_photo'))
				unlink('upload/'.$this->input->post('remove_photo'));
			$data['foto'] = '';
		}

		if(!empty($_FILES['foto']['name']))
		{
			$upload = $this->upload_foto();
			
			//delete file
			$maha = $this->mahasiswa->get_by_nim($this->input->post('kode'));
			if(file_exists('upload/'.$maha->foto) && $maha->foto)
				unlink('upload/'.$maha->foto);

			$data['foto'] = $upload;
		}

		$this->mahasiswa->update(array('nim' => $this->input->post('kode')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function Daftar_delete($kode)
	{
		$this->mahasiswa->delete_by_kode($kode);
		echo json_encode(array("status" => TRUE));
	}


 private function upload_pdf()
    {
        $config['upload_path']          = 'berkas-upload/';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 5000; //set max size allowed in Kilobyte
        $config['file_name']            = round(microtime(true) * 1000); //just milisecond timestamp fot unique name
 		//$config['encrypt_name'] = TRUE;   

        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('ijasah')) //upload and validate
        {
            $data['inputerror'][] = 'ijasah';
            $data['error_string'][] = 'Kesalahan Upload pdf: '.$this->upload->display_errors('',''); //show ajax error
            $data['status'] = FALSE;
            echo json_encode($data);
            exit();
        }
        return $this->upload->data('file_name');

    }


    private function upload_foto()
    {
        $config1['upload_path']          = 'upload/';
        $config1['allowed_types']        = 'gif|jpg|png|jpeg';
        $config1['max_size']             = 2000; //set max size allowed in Kilobyte
        $config1['max_width']            = 1000; // set max width image allowed
        $config1['max_height']           = 1000; // set max height allowed
        $config1['file_name']            = round(microtime(true) * 1000); //just milisecond timestamp fot unique name
        //$config1['encrypt_name'] = TRUE;  
 
        $this->load->library('upload', $config1);
        $this->upload->initialize($config1);
 
        if(!$this->upload->do_upload('foto')) //upload and validate
        {
            $data['inputerror'][] = 'foto';
            $data['error_string'][] = 'Kesalahan Upload Gambar : '.$this->upload->display_errors('',''); //show ajax error
            $data['status'] = FALSE;
            echo json_encode($data);
            exit();
        }
        return $this->upload->data('file_name');

    }

    

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('nim') == '')
		{
			$data['inputerror'][] = 'nim';
			$data['error_string'][] = 'Nim harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('nama') == '')
		{
			$data['inputerror'][] = 'nama';
			$data['error_string'][] = 'Nama harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('jk') == '')
		{
			$data['inputerror'][] = 'jk';
			$data['error_string'][] = 'Jenis Kelamin harus diisi';
			$data['status'] = FALSE;
		}

		if($this->input->post('tgl') == '')
		{
			$data['inputerror'][] = 'tgl';
			$data['error_string'][] = 'Tanggal harus diisi';
			$data['status'] = FALSE;
		}

		/**
		if($this->input->post('alamat') == '')
		{
			$data['inputerror'][] = 'alamat';
			$data['error_string'][] = 'Alamat harus diisi';
			$data['status'] = FALSE;
		}
		*/

		if($data['status'] === FALSE)
		{
			echo json_encode($data);
			exit();
		}
	}

}
