<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class Welcome extends Controller {
    function Welcome(){
        parent::Controller();
        $this->load->library('securimage');
		$this->load->helper('url');
		$this->load->helper('html');
    }
    function index(){
        $this->load->view('test.html');
    }
    function check(){
        $inputCode = $this->input->post('imagecode');
        if($this->securimage->check($inputCode) == true){
			$data['result'] = '<h1>=v= PASS!</h1>';
            $this->load->view('test.html',$data);
        } else {
			$data['result'] = '<h1>/_\ FAILURE</h1>';
            $this->load->view('test.html',$data);		
        }
    }
} 