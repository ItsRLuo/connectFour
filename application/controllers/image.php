<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
class Image extends Controller {
    function Image(){
        parent::Controller();
        $this->load->library('securimage');        
    }
    function securimage(){
        $this->securimage->show();
    }
} 