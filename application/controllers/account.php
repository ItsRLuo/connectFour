<?php

class Account extends CI_Controller {
	 
	function __construct() {
    		// Call the Controller constructor
	    	parent::__construct();
			$this->load->library('securimage');
			$this->load->helper('html');
			$this->load->helper('url');
			
			// Create database for the game if no database exists
			$checktable = mysql_query("SHOW TABLES LIKE 'user'");
			if ($table_exists = mysql_num_rows($checktable) == 0)
			{
				$this->db->query("create table User(id int not null AUTO_INCREMENT, login CHAR(20), 
						  first char(20), last char(20), 
			              password char(200), salt int, email char(200), user_status_id int,
			              invite_id int, match_id int, primary key(id));");	
			}
			$checktable = mysql_query("SHOW TABLES LIKE 'invite'");
			if ($table_exists = mysql_num_rows($checktable) == 0)
			{
				$this->db->query("create table invite(id int not null AUTO_INCREMENT
								  , user1_id int, user2_id int, 
					              invite_status_id int, primary key(id), 
					              foreign key(user1_id) references user(id),
					              foreign key(user2_id) references user(id));");	
			}
			$checktable = mysql_query("SHOW TABLES LIKE 'match'");
			if ($table_exists = mysql_num_rows($checktable) == 0)
			{
				$this->db->query("create table `match`(id int not null AUTO_INCREMENT
					              , user1_id int, user2_id int, 
					              match_status_id int, primary key(id), 
					              foreign key(user1_id) references user(id),
					              foreign key(user2_id) references user(id));");	
			}
			session_start();
    }
        

	public function _remap($method, $params = array()) {
		// enforce access control to protected functions

		$protected = array('updatePasswordForm','updatePassword','index','logout');

		if (in_array($method,$protected) && !isset($_SESSION['user']))
			redirect('account/loginForm', 'refresh'); //Then we redirect to the index page again
		 
		return call_user_func_array(array($this, $method), $params);
	}


	function loginForm() {
		$this->load->view('account/loginForm');
	}

	function login() {
		$servername = "localhost";
		$username = "root";
		$password = "123";
		$dbname = "root";
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		$sql = "CREATE TABLE MyGuessts (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
				firstname VARCHAR(30) NOT NULL,
				lastname VARCHAR(30) NOT NULL,
				email VARCHAR(50),
				reg_date TIMESTAMP
				)";

		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('account/loginForm');
		}
		else
		{
			$login = $this->input->post('username');
			$clearPassword = $this->input->post('password');

			$this->load->model('user_model');

			$user = $this->user_model->get($login);

			if (isset($user) && $user->comparePassword($clearPassword)) {
				$_SESSION['user'] = $user;
				$data['user']=$user;

				$this->user_model->updateStatus($user->id, User::AVAILABLE);

				redirect('arcade/index', 'refresh'); //redirect to the main application page
			}
			else {
				$data['errorMsg']='Incorrect username or password!';
				$this->load->view('account/loginForm',$data);
			}
		}
	}

	function logout() {
		$user = $_SESSION['user'];
		$this->load->model('user_model');
		$this->user_model->updateStatus($user->id, User::OFFLINE);
		session_destroy();
		redirect('account/index', 'refresh'); //Then we redirect to the index page again
	}

	function newForm() {
		$this->load->view('account/newForm');
	}

	function createNew() {
		include_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
		$securimage = new Securimage();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'required|is_unique[user.login]');
	    $this->form_validation->set_rules('password', 'Password', 'required');
	    $this->form_validation->set_rules('first', 'First', "required");
	    $this->form_validation->set_rules('last', 'last', "required");
	    $this->form_validation->set_rules('email', 'Email', "required|is_unique[user.email]");
	    	
		$data['captchaErrorMessage'] = "";
	  
	  	#$this->db->query('insert into ')
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('account/newForm');
		}
		else if ($securimage->check($_POST['captcha_code']) == false) {
			
			$data['captchaErrorMessage'] = "<br />The security code entered was incorrect.<br />";
			$this->load->view('account/newForm', $data);
		}
		
		else
		{
			$user = new User();
			$user->login = $this->input->post('username');
			$user->first = $this->input->post('first');
			$user->last = $this->input->post('last');
			$clearPassword = $this->input->post('password');
			$user->encryptPassword($clearPassword);
			$user->email = $this->input->post('email');
			$this->load->model('user_model');

			$error = $this->user_model->insert($user);
	   
			$this->load->view('account/loginForm');
		}
	}


	function updatePasswordForm() {
		$this->load->view('account/updatePasswordForm');
	}

	function updatePassword() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('oldPassword', 'Old Password', 'required');
		$this->form_validation->set_rules('newPassword', 'New Password', 'required');
		 
		 
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('account/updatePasswordForm');
		}
		else
		{
			$user = $_SESSION['user'];
	   
			$oldPassword = $this->input->post('oldPassword');
			$newPassword = $this->input->post('newPassword');

			if ($user->comparePassword($oldPassword)) {
				$user->encryptPassword($newPassword);
				$this->load->model('user_model');
				$this->user_model->updatePassword($user);
				redirect('arcade/index', 'refresh'); //Then we redirect to the index page again
			}
			else {
				$data['errorMsg']="Incorrect password!";
				$this->load->view('account/updatePasswordForm',$data);
			}
		}
	}

	function recoverPasswordForm() {
		$this->load->view('account/recoverPasswordForm');
	}

	function recoverPassword() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'email', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('account/recoverPasswordForm');
		}
		else
		{
			$email = $this->input->post('email');
			$this->load->model('user_model');
			$user = $this->user_model->getFromEmail($email);

			if (isset($user)) {
				$newPassword = $user->initPassword();
				$this->user_model->updatePassword($user);

				$this->load->library('email');
				 
				$config['protocol']    = 'smtp';
				$config['smtp_host']    = 'ssl://smtp.gmail.com';
				$config['smtp_port']    = '465';
				$config['smtp_timeout'] = '7';
				//not used for now
				$config['smtp_user']    = '***';
				$config['smtp_pass']    = '***';
				$config['charset']    = 'utf-8';
				$config['newline']    = "\r\n";
				$config['mailtype'] = 'text'; // or html
				$config['validation'] = TRUE; // bool whether to validate email or not

				$this->email->initialize($config);

				$this->email->from('richardchen922@gmail.com', 'Login App');
				$this->email->to($user->email);

				$this->email->subject('Password recovery');
				$this->email->message("Your new password is $newPassword");

				$result = $this->email->send();

				//$data['errorMsg'] = $this->email->print_debugger();

				//$this->load->view('emailPage',$data);
				$this->load->view('account/emailPage');

			}
			else {
				$data['errorMsg']="No record exists for this email!";
				$this->load->view('account/recoverPasswordForm',$data);
			}
		}
	}
}
?>
