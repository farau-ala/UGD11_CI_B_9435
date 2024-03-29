<?php
use Restserver \Libraries\REST_Controller ;
Class vehicles extends REST_Controller{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('vehiclesModel');
        $this->load->library('form_validation');
    }
    public function index_get(){
        return $this->returnData($this->db->get('vehicles')->result(), false);
    }
    public function index_post($id = null){
        $validation = $this->form_validation;
        $rule = $this->vehiclesModel->rules();
        if($id == null){
            array_push($rule,[
                
                    'field' => 'type',
                    'label' => 'type',
                    'rules' => 'required'
                ],
                [
                    'field' => 'created_at',
                    'label' => 'created_at',
                    'rules' => 'required'
                ],
                [
                    'field' => 'licensePlate',
                    'label' => 'licensePlate',
                    'rules' => 'required|is_unique[vehicles.licensePlate]'
                ]
            );
        }
        else{
            array_push($rule,
                [
                    'field' => 'licensePlate',
                    'label' => 'licensePlate',
                    'rules' => 'required|valid_licensePlate'
                ]
            );
        }
        $validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
        }
        $vehicles = new vehiclesData();
        $vehicles->merk = $this->post('merk');
        $vehicles->type = $this->post('type');
        $vehicles->licensePlate = $this->post('licensePlate');
        $vehicles->created_at = $this->post('created_at');
        if($id == null){
            $response = $this->vehiclesModel->store($vehicles);
        }else{
            $response = $this->vehiclesModel->update($vehicles,$id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }
    public function index_delete($id = null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->vehiclesModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }
    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}
Class vehiclesData{
    public $merk;
    public $type;
    public $licensePlate;
    public $created_at;
}