<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DogHeavenApi extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('DogHeavenModel');
		$this->load->helper('url');
		$this->load->helper('form');
		date_default_timezone_set('Asia/Kolkata');

	}
	/**
	* Index Page for this controller.
	*/
	public function index(){
		$this->load_view('password_change.html');die;
		//
	}

	/* function name : pet_lovers register
	description: regiters new users
	*/

	public function pet_lovers_register(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		define('UPLOAD_DIR','pet_lovers_profiles/');
		$img = $postData['profile_image'];
		$img = str_replace('data:image/png;base64,', '',$img);
		$img = str_replace(' ', '+',$img);
		$img_code = base64_decode($img);
		$file =UPLOAD_DIR. uniqid() . '.png';
		$success = file_put_contents($file, $img_code);
		$postData['profile_image'] = $file;
		$postData['unique_id'] = uniqid();
		$this->DogHeavenModel->pet_lovers_register($postData);
	}

	/* function name : pet_owners_register
	description: regiters new users */

	public function pet_owners_register(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		define('UPLOAD_DIR','pet_owners_profiles/');
		$img = $postData['owner_profile_picture'];
		$img = str_replace('data:image/png;base64,', '',$img);
		$img = str_replace(' ', '+',$img);
		$img_code = base64_decode($img);
		$file =UPLOAD_DIR. uniqid() . '.png';
		$success = file_put_contents($file, $img_code);
		$postData['owner_profile_picture'] = $file;

		define('UPLOAD_DIR1','pet_profiles/');
		$pet_img = $postData['profile_picture'];
		$pet_img = str_replace('data:image/png;base64,', '',$pet_img);
		$pet_img = str_replace(' ', '+',$pet_img);
		$pet_img_code = base64_decode($pet_img);
		$pet_file =UPLOAD_DIR1. uniqid() . '.png';
		$success1 = file_put_contents($pet_file, $pet_img_code);
		$postData['profile_picture'] = $pet_file;
		$this->DogHeavenModel->pet_owners_register($postData);
	}

	/* function name : business_register
	description: regiters new users */

	public function pet_business_register(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		define('UPLOAD_DIR','pet_business_profiles/');
		$img = $postData['profile_image'];
		$img = str_replace('data:image/png;base64,', '',$img);
		$img = str_replace(' ', '+',$img);
		$img_code = base64_decode($img);
		$file =UPLOAD_DIR. uniqid() . '.png';
		$success = file_put_contents($file, $img_code);
		$postData['profile_image'] = $file;
		$postData['password'] = md5($postData['password']);
		$postData['unique_id'] = uniqid();
		$this->DogHeavenModel->pet_business_register($postData);
	}

	/* function name : login
	description: login users with email */

	public function login(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$result = $this->DogHeavenModel->user_login($postData);
		if(!empty($result)) {
			if($result['confirmation_status'] == 0){
				$response['success'] = "false";
				$response['message'] = "please verify your email address first";
			}
			else {
				$response['success'] = "true";
				$response['user_id'] = $result['unique_id'];
				$response['message'] = "login successfully";
			}
		}
		else {
			$response['success'] = "false";
			$response['message'] = 'invalid credentials';
		}
		echo json_encode($response);
	}
	/* function name : add_posts
	description:  add new users posts */
	public function add_posts(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		define('UPLOAD_DIR','posts_images/');
		$img = $postData['post_image'];
		$img = str_replace('data:image/png;base64,', '',$img);
		$img = str_replace(' ', '+',$img);
		$img_code = base64_decode($img);
		$file =UPLOAD_DIR. uniqid() . '.png';
		$postData['post_image'] = $file;
		$success = file_put_contents($file, $img_code);
		$response =[];
		$res = $this->DogHeavenModel->add_new_posts($postData);
		if($res){
			$response['success'] = "true";
			$response['message'] = "data inserted successfully";
		}
		else{
			$response['success'] = "false";
			$response['message'] = "having issue in inserting data check details again";
		}
		echo json_encode($response);
	}
	/* function name : get_posts
	description:function to get posts accordiing to user_id*/
	public function get_posts(){
		// $rest_json    =    file_get_contents("php://input");
		// $postData    =    json_decode($rest_json, true);
		$response = [];
		$res = $this->DogHeavenModel->get_user_posts();
		if(!empty($res)){
			$response['success'] = "true";
			$response['message'] = "Data  fetched successfully";
			foreach($res as $result){
				$response['user_posts'][] = ['title'=>$result['title'],'description'=>$result['description'],'post_image'=>$result['post_image']];
			}
		}
		else{
			$response['success']  = "false";
			$response['message'] = "there are no posts for this user";
		}
		echo json_encode($response);
	}
	/* function name : add new album
	description: function for adding albums in database creating new albums*/
	public function add_new_album(){
		$response = [];
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$res = $this->DogHeavenModel->add_new_album($postData);
		if($res) {
			$response['success'] = 'true';
			$response['message'] = "album created successfully";
		}
		else {
			$response['success'] = "false";
			$response['message'] = "album not created";
		}
		echo json_encode($response);
	}
	/* function name : get_user_profile
	description: fucntion for getting pet owner profile*/
	public function get_user_profile(){
		$response = [];
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$result  = $this->DogHeavenModel->get_profile($postData);
		if(!empty($result)){
			$response['success'] = "true";
			$response['message'] = "data fetched successfully";
			if($result['user']['user_type']=="owner"){
				$response['pet_profile']= ['user_type'=>$result['user_type'],'pet_name'=>$result['name'],'pet_age'=>$result['age'],'pet_city'=>$result['city'],'pet_state'=>$result['state'],'pet_history'=>$result['pet_history'],'pet_profile_pic'=>$result['profile_picture']];
				$response['user'] = $result['user'];
				$response['user_files'] = $result['user_files'];
			}
			elseif($result['user']['user_type']=="lover"){
				$response['user'] = $result['user'];
				$response['user_files'] = $result['user_files'];
			}
			else{
				$response['user_details'] = ['user_type'=>$result['user_type'],'company'=>$result['company'],'contact_number'=>$result['contact_number'],'email'=>$result['email'],'location'=>$result['location'],'link'=>$result['link'],'profile_image'=>$result['profile_image']];
			}
		}
		else{
			$response['success'] = "false";
			$response['message'] = 'not fetched';
		}
		echo json_encode($response);
	}
	/* function name : send verfication email
	description: this functiom  send verifcation email to users who reagister for the site */
	public function sendVerificationEmail(){
		$email = $this->input->post('email');
		$this->DogHeavenModel->sendVerificatinEmail($email);
	}
	/* function name : confirmation_page
	description:  this function contains confirmation page after clicking confirmation link he reached to this functiom */
	public function confirmation_page(){
		$email = $this->input->get('email',True);
		$reciever_email = $this->input->get('email',True);
		$this->DogHeavenModel->confirmation_page($email);
	}
	/* function name : forgot password
	description: function workes for  forgot password send old password to user */
	public function forgot_password(){
		$response = [];
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$res = $this->DogHeavenModel->send_change_password_link($postData);
		if($res == 1){
			$response['success'] = "true";
			$response['message'] = "email sent successfully";
		}
		else{
			$response['success'] = "false";
			$response['message'] = "email can`t be sent please check your email";
		}
		echo json_encode($response);
	}
	/* function name : update password
	description: updates  old poassword to  new password */
	public function update_password() {
		$this->load->view('password_update_form');
		if(isset($_POST['update'])){
			$rest_json    =    file_get_contents("php://input");
			$postData    =    json_decode($rest_json, true);
			$password = md5($postData['password']);
			$email = $postData['email'];
			$res = $this->DogHeavenModel->password_update($email,$password);
			if($res == 1) {
				echo "password updated";
			}
			else{
				echo "not changed";
			}
		}
	}
	// public function search_user() {
	// 	$rest_json    =    file_get_contents("php://input");
	// 	$postData    =    json_decode($rest_json, true);
	// 	$this->DogHeavenModel->search_user($postData);
	// }
	/* function name : posts likes
	description: like user posts */

		public function posts_like() {
			$rest_json    =    file_get_contents("php://input");
		  $postData    =    json_decode($rest_json, true);
			$response = [];
			$result = $this->DogHeavenModel->like_posts($postData);
			if($result == 1){
				$response['success'] = "true";
				$response['message'] = "post liked successfully";
			}
			else{
				$response['success'] = "false";
				$resposne['message'] = "post not liked";
			}
			echo json_encode($response);
		}
		/* function name : pet_gallery
		description: adding images to gallery*/
	public function pet_gallery(){
		$response = [];
		$target_path = "pet_gallery/";
		// $response['success'] = "true";
		// $response['message'] = "tessitingh oi,mjad";
		// $response['file_data'] ="testeing ";
		// echo json_encode($response);die;
		$name = $_FILES['file']['name'];
		define('UPLOAD_DIR','pet_gallery/');
		$ext = explode('.',$name);
		$extension = $ext[1];

		$img = str_replace(' ', '+',$name);
		$file = UPLOAD_DIR . basename($img);
		// $success = file_put_contents($file,$img);
		  move_uploaded_file($_FILES['file']['tmp_name'],'pet_gallery/'.$name);
			$data = [
			 "image_name" => $file,
			 "image_type" => $extension,
			 "album_id"=>'1',
			 "user_id"=>'32'
			];
			$result = $this->DogHeavenModel->pet_gallery_images($data);
			if($result == 1){
				$response['success'] = "true";
				$response['message'] = "images added  to pet gallery";
				$response['file_data'] =$name;
			}
			else {
				$response['success'] = "true";
				$response['message'] = "images not added  to pet gallery";
			}
		 		echo json_encode($response);
		}
		/* function name : pet_documents
		description: upload new documnets for pet */
		public function pet_documents(){

			$response = [];
			$name = $_FILES['file']['name'];
			$ext = explode('.',$name);
			$extension = $ext[1];
		 	define('UPLOAD_DIR','pet_documents/');
		 	$file = UPLOAD_DIR . basename($name);
		 	// echo $file;
		 	move_uploaded_file($_FILES['file']['tmp_name'],'pet_documents/'.$name);
			$data = [
				'file_name'=>$file,
				'file_size'=>"234",
				"file_type"=>$extension,
				"user_id"=> '4'
			];
			$result = $this->DogHeavenModel->upload_documents($data);
			if($result == 1){
				$response['success'] = "true";
				$response['message'] = "document added successfully";
			}
			else{
				$response['success'] = "true";
				$response['message'] = "document added successfully";
			}
			echo json_encode($response);
	 }
	//  public function get_user_img_docs(){
	// 	 $rest_json    =   file_get_contents("php://input");
	// 	 $postData    =    json_decode($rest_json, true);
	// 	 $response = [];
	// 	 $res = $this->DogHeavenModel->fetch_details($postData);
	// 	 if(!empty($res)){
	// 		 $response['success'] = "true";
	// 		 $response['message'] = "data fetched successfully";
	// 		 foreach($res as $data ){
	// 			$response['details'][]= [
	// 				 'image_name' =>$data['image_name'],
	// 				 'image_size' =>$data['image_size'],
	// 				 'image_type' =>$data['image_type'],
	// 				 'album_id' =>$data['album_id'],
	// 				 'user_id' =>$data['user_id'],
	// 				 'file_name' =>$data['file_name'],
	// 				 'file_type' =>$data['file_type'],
	// 				 'file_size' =>$data['file_size']
	// 			];
	// 	 }
	//  }
	// 	 else{
	// 		 $response['sucess'] = "false";
	// 		 $response['message'] = "data not fethed";
	// 	 }
	// 	 echo json_encode($response);
 // 	}
}
?>
