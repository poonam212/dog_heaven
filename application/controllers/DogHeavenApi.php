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
		// $this->load_view('password_change.html');die;
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
		$postData['password'] = md5($postData['password']);
		$postData['user_type'] = "lover";
		$this->DogHeavenModel->pet_lovers_register($postData);
	}

	/* function name : pet_owners_register
	description: regiters new users */

	public function pet_owners_register(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$postData['user_type'] = "owner";
		define('UPLOAD_DIR_OWNER','pet_owners_profiles/');
		$img = $postData['owner_profile_picture'];
		$img = str_replace('data:image/png;base64,', '',$img);
		$img = str_replace(' ', '+',$img);
		$img_code = base64_decode($img);
		$file =UPLOAD_DIR_OWNER. uniqid() . '.png';
		$success = file_put_contents($file, $img_code);
		$postData['owner_profile_picture'] = $file;
		$postData['password'] = md5($postData['password']);
		define('UPLOAD_DIR_PET','pet_profiles/');
		$pet_img = $postData['profile_picture'];
		$pet_img = str_replace('data:image/png;base64,', '',$pet_img);
		$pet_img = str_replace(' ', '+',$pet_img);
		$pet_img_code = base64_decode($pet_img);
		$pet_file =UPLOAD_DIR_PET. uniqid() . '.png';
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
		$postData['password'] = md5($postData['password']);
		$postData['user_type'] = "business";
		define('UPLOAD_DIR_BUSINESS','pet_business_profiles/');
		$img = $postData['profile_image'];
		$img = str_replace('data:image/png;base64,', '',$img);
		$img = str_replace(' ', '+',$img);
		$img_code = base64_decode($img);
		$file = UPLOAD_DIR_BUSINESS. uniqid() . '.png';
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
				$response['message'] = "login successfully";
				if($result['user_type'] == "lover"){
					$image1 = $result['profile_image'];
					$uploadPath = 'pet_lovers_profiles/';
					$image = explode('/',$image1);
					$this->resize_image($image[1],$uploadPath);
					$thumb_image = explode('.',$image1);
					$thumb = $thumb_image[0].'_thumb'.'.'.$thumb_image[1];
					$response['user_info'] =['user_id'=> $result['unique_id'],'user-type'=>$result['user_type'],'user-image'=>$thumb];
				}elseif($result['user_type'] == "owner"){
					$image1 = $result['owner_profile_picture'];
					$uploadPath = 'pet_owners_profiles/';
					$image = explode('/',$image1);
					$this->resize_image($image[1],$uploadPath);
					$thumb_image = explode('.',$image1);
					$thumb = $thumb_image[0].'_thumb'.'.'.$thumb_image[1];
					$response['user_info'] =['user_idid'=> $result['unique_id'],'user-type'=>$result['user_type'],'user-image'=>$thumb];
				}
				else{
					$image1 = $result['profile_image'];
					$uploadPath = 'pet_business_profiles/';
					$image = explode('/',$image1);
					$this->resize_image($image[1],$uploadPath);
					$thumb_image = explode('.',$image1);
					$thumb = $thumb_image[0].'_thumb'.'.'.$thumb_image[1];
					$response['user_info'] =['user_id'=> $result['unique_id'],'user-type'=>$result['user_type'],'user-image'=>$thumb];
				}
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
		$data = array();
		$uploadData = [];
		$response = [];
		$files = $_FILES;
		// print_r($_FILES);die;
		// $response['image'][] = $_FILES;
		// echo json_encode($response);die;
		if( !empty($_FILES['file']['name'])){
		 $filesCount = count($_FILES['file']['name']);
		//  $response['count'] = $filesCount;
		 $uploadPath = 'posts_images/';
		 for($i = 0; $i < $filesCount; $i++){
			 $_FILES['file']['name'] = $files['file']['name'][$i];
			 $_FILES['file']['type'] = $files['file']['type'][$i];
			 $_FILES['file']['tmp_name'] = $files['file']['tmp_name'][$i];
			 $_FILES['file']['error'] = $files['file']['error'][$i];
			 $_FILES['file']['size'] = $files['file']['size'][$i];
			 $config['upload_path'] = $uploadPath;
			 $config['allowed_types'] = 'gif|jpg|png';
			 $this->load->library('upload', $config);
			 $this->upload->initialize($config);
			 $upload_data = $this->upload->data();
			 $name_array[] = $files['file'];
			 $fileName =  $files['file'];
			 $images = $fileName['name'];
			 $image_file[] = $uploadPath.$images[$i];
			 $tmp_name = $fileName['tmp_name'];
			//  print_r($fileName['name'][$i]);
			 $type = $fileName['type'];
			 $size = $fileName['size'];
			 move_uploaded_file($fileName['tmp_name'][$i],'posts_images/'.$fileName['name'][$i]);
			 $this->resize_image($fileName['name'][$i],$uploadPath);
			}
		 $file_name =  implode(',',$image_file);
		 $file_type = implode(',',$type);
		 $file_size = implode(',',$size);
		}
		$tag_friends = ['tag_friends'=>implode(',',$this->input->post('tag_friends'))];
		$tags = implode(',',$tag_friends);
		$data = [
			'title'=>$this->input->post('title'),
			'user_id'=>$this->input->post('user_id'),
			'post_image'=>$file_name,
		  'user_type'=>$this->input->post('user_type'),
			'longitude'=>$this->input->post('longitude'),
			'latitude'=>$this->input->post('latitude'),
			'tag_friends'=>$tags
		];
		$res =  $this->DogHeavenModel->add_new_posts($data);
	  // $response['data'] = $data;
		// echo json_encode($response);die;

		if($res == 1){
			$response['success'] = "true";
			$response['message'] = "post added  successfully";
		}
		else{
			$response['success'] = "false";
			$response['message'] = "post is not uploaded";
		}
		echo json_encode($response);
	}

	/* function name : get_posts
	description:function to get posts accordiing to user_id*/

	public function get_posts(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$res = $this->DogHeavenModel->get_user_posts($postData);
		// echo "<pre>";
		// print_R($res);
		if(!empty($res)){
			$response['success'] = "true";
			$response['message'] = "Data  fetched successfully";
			for($i=0;$i<count($res);$i++){
				if($res[$i]['user_type'] == "lover"){
					$image1 = $res[$i]['profile_image'];
					$uploadPath = 'pet_lovers_profiles/';
					$image = explode('/',$image1);
					$this->resize_image($image[1],$uploadPath);
					$thumb_image = explode('.',$image1);
				  $thumb = $thumb_image[0].'_thumb'.'.'.$thumb_image[1];
					$response['data'][$i] = ['post_id'=>$res[$i]['id'],'like_status'=>$res[$i]['like_status'],'title'=>$res[$i]['title'],'post_image'=>$res[$i]['post_image'],'user_id'=>$res[$i]['user_id'],'created_at'=>$res[$i]['created_at'],'name'=>$res[$i]['first_name'],'last_name'=>$res[$i]['last_name'],'email'=>$res[$i]['email'],'user-image'=>$thumb,'user_type'=>$res[$i]['user_type']];
				}elseif($res[$i]['user_type'] == "owner"){
					$image1 = $res[$i]['owner_profile_picture'];
					$uploadPath = 'pet_owners_profiles/';
					$image = explode('/',$image1);
					$this->resize_image($image[1],$uploadPath);
					$thumb_image = explode('.',$image1);
					$thumb = $thumb_image[0].'_thumb'.'.'.$thumb_image[1];
					$response['data'][$i]= ['post_id'=>$res[$i]['id'],'title'=>$res[$i]['title'],'like_status'=>$res[$i]['like_status'],'post_image'=>$res[$i]['post_image'],'user_id'=>$res[$i]['user_id'],'created_at'=>$res[$i]['created_at'],'name'=>$res[$i]['owner_name'],'email'=>$res[$i]['owner_email'],'user-image'=>$thumb,'user_type'=>$res[$i]['user_type']];
				}
			else{
					$image1 = $res[$i]['profile_image'];
					$uploadPath = 'pet_business_profiles/';
					$image = explode('/',$image1);
					$this->resize_image($image[1],$uploadPath);
					$thumb_image = explode('.',$image1);
					$thumb = $thumb_image[0].'_thumb'.'.'.$thumb_image[1];
					$response['data'][$i] = ['post_id'=>$res[$i]['id'],'title'=>$res[$i]['title'],'post_image'=>$res[$i]['post_image'],'user_id'=>$res[$i]['user_id'],'created_at'=>$res[$i]['created_at'],'name'=>$res[$i]['company'],'email'=>$res[$i]['email'],'user-image'=>$thumb,'user_type'=>$res[$i]['user_type'],'like_status'=>$res[$i]['like_status']];
				}
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
		// echo "<pre>";
		// print_r($result);
		if(!empty($result)){
			$response['success'] = "true";
			$response['message'] = "data fetched successfully";
			if($result['user']['user_type'] == "owner"){
				$image = explode('/',$result['user']['owner_profile_picture']);
				$uploadPath = 'pet_owners_profiles/';
				$this->resize_image($image[1],$uploadPath);
				$thumb = explode('.',$result['user']['owner_profile_picture']);
				$thumb_path = $thumb[0].'_thumb'.'.'.$thumb[1];
				// echo $thumb_path;
				$response['pet_profile']= ['pet_name'=>$result['user']['name'],'pet_age'=>$result['user']['age'],'pet_city'=>$result['user']['city'],'pet_state'=>$result['user']['state'],'pet_history'=>$result['user']['pet_history'],'pet_profile_pic'=>$result['user']['profile_picture']];
				$response['user'] = ['user_type'=>$result['user']['user_type'],'bio'=>$result['user']['bio'],'gender'=>$result['user']['gender'],'email'=>$result['user']['owner_email'],'dob'=>$result['user']['owner_dob'],'name'=>$result['user']['owner_name'],'gender'=>$result['user']['gender'],'location'=>$result['user']['location'],'user_name'=>$result['user']['user_name'],'profile_image'=>$result['user']['owner_profile_picture'],'subscription'=>$result['subscription_details']['subscription'],'thumb_path'=>'http://mastercreationz.com/dog_heaven/DogHeaven/'.$thumb_path,'followers'=>$result['followers'],'following'=>$result['following']];
				if(!empty($result['user_images'])){
					for($i=0;$i<count($result['user_images']);$i++){
						$response['user_images'][$i]=['image_name'=> $result['user_images'][$i]['image_name'],'id'=>$result['user_images'][$i]['id']];
					}
				}
				if(!empty($result['user_posts'])){
					for($i=0;$i<count($result['user_posts']);$i++){
						$response['user_posts'][$i]=['post_id'=> $result['user_posts'][$i]['id'],'like_status'=> $result['user_posts'][$i]['like_status'],'title'=> $result['user_posts'][$i]['title'],'post_image'=> $result['user_posts'][$i]['post_image'],'user_id'=> $result['user_posts'][$i]['user_id']];
					}
				}
				if(!empty($result['user_files'])){
					for($i=0;$i<count($result['user_files']);$i++){
						$response['user_files'][$i]=['file_name'=>$result['user_files'][$i]['file_name'],'id'=>$result['user_files'][$i]['id']];
					}
				}
				if(!empty($result['user_pets_for_sale'])){
					for($i=0;$i<count($result['user_pets_for_sale']);$i++){
						$response['user_pets'][$i]=['name'=>$result['user_pets_for_sale'][$i]['name'],'description'=>$result['user_pets_for_sale'][$i]['description'],'location'=>$result['user_pets_for_sale'][$i]['location'],'user_id'=>$result['user_pets_for_sale'][$i]['user_id'],'pet_image'=>$result['user_pets_for_sale'][$i]['pet_image'],'condition'=>$result['user_pets_for_sale'][$i]['condition'],'price'=>$result['user_pets_for_sale'][$i]['price']];
					}
				}
			}
			elseif($result['user']['user_type'] == "lover"){
				$image = explode('/',$result['user']['profile_image']);
				$uploadPath = 'pet_lovers_profiles/';
				$this->resize_image($image[1],$uploadPath);
				$thumb = explode('.',$result['user']['profile_image']);
				$thumb_path = $thumb[0].'_thumb'.'.'.$thumb[1];
				$response['user'] = ['user_type'=>$result['user']['user_type'],'bio'=>$result['user']['bio'],'gender'=>$result['user']['gender'],'last_name'=>$result['user']['last_name'],'first_name'=>$result['user']['first_name'],'email'=>$result['user']['email'],'user_name'=>$result['user']['user_name'],'dob'=>$result['user']['dob'],'user_name'=>$result['user']['user_name'],'location'=>$result['user']['location'],'link'=>$result['user']['link'],'profile_image'=>$result['user']['profile_image'],'subscription'=>$result['subscription_details']['subscription'],'thumb_path'=>'http://mastercreationz.com/dog_heaven/DogHeaven/'.$thumb_path,'followers'=>$result['followers'],'following'=>$result['following']];
				if(!empty($result['user_images'])){
					for($i=0;$i<count($result['user_images']);$i++){
						$response['user_images'][$i]=['image_name'=> $result['user_images'][$i]['image_name'],'id'=>$result['user_images'][$i]['id']];
					}
				}
				if(!empty($result['user_files'])){
					for($i=0;$i<count($result['user_files']);$i++){
						$response['user_files'][$i]=['file_name'=>$result['user_files'][$i]['file_name'],'id'=>$result['user_files'][$i]['id']];
					}
				}
				if(!empty($result['user_pets_for_sale'])){
					for($i=0;$i<count($result['user_pets_for_sale']);$i++){
						$response['user_pets'][$i]=['name'=>$result['user_pets_for_sale'][$i]['name'],'description'=>$result['user_pets_for_sale'][$i]['description'],'location'=>$result['user_pets_for_sale'][$i]['location'],'user_id'=>$result['user_pets_for_sale'][$i]['user_id'],'pet_image'=>$result['user_pets_for_sale'][$i]['pet_image'],'condition'=>$result['user_pets_for_sale'][$i]['condition'],'price'=>$result['user_pets_for_sale'][$i]['price']];
					}
				}
				if(!empty($result['user_posts'])){
					for($i=0;$i<count($result['user_posts']);$i++){
						$response['user_posts'][$i]=['post_id'=> $result['user_posts'][$i]['id'],'like_status'=> $result['user_posts'][$i]['like_status'],'title'=> $result['user_posts'][$i]['title'],'post_image'=> $result['user_posts'][$i]['post_image'],'user_id'=> $result['user_posts'][$i]['user_id']];
					}
				}

			}
			else{
				// echo $result['user']['profile_image'];
				$image3 = explode('/',$result['user']['profile_image']);
				// print_r($image);
				$uploadPath = 'pet_business_profiles/';
				$this->resize_image($image3[1],$uploadPath);
				$thumb = explode('.',$result['user']['profile_image']);
				$thumb_path = $thumb[0].'_thumb'.'.'.$thumb[1];
				// echo $thumb_path;
				$response['user'] = ['user_type'=>$result['user']['user_type'],'bio'=>$result['user']['bio'],'company'=>$result['user']['company'],'contact_number'=>$result['user']['contact_number'],'email'=>$result['user']['email'],'location'=>$result['user']['location'],'link'=>$result['user']['link'],'profile_image'=>$result['user']['profile_image'],'subscription'=>$result['subscription_details']['subscription'],'thumb_path'=>'http://mastercreationz.com/dog_heaven/DogHeaven/'.$thumb_path,'followers'=>$result['followers'],'following'=>$result['following']];
				if(!empty($result['user_images'])){
					for($i=0;$i<count($result['user_images']);$i++){
						$response['user_images'][$i]=['image_name'=> $result['user_images'][$i]['image_name'],'id'=>$result['user_images'][$i]['id']];
					}
				}
				if(!empty($result['user_files'])){
					for($i=0;$i<count($result['user_files']);$i++){
						$response['user_files'][$i]=['file_name'=>$result['user_files'][$i]['file_name'],'id'=>$result['user_files'][$i]['id']];
					}
				}
				if(!empty($result['user_pets_for_sale'])){
					for($i=0;$i<count($result['user_pets_for_sale']);$i++){
						$response['user_pets'][$i]=['name'=>$result['user_pets_for_sale'][$i]['name'],'description'=>$result['user_pets_for_sale'][$i]['description'],'location'=>$result['user_pets_for_sale'][$i]['location'],'user_id'=>$result['user_pets_for_sale'][$i]['user_id'],'pet_image'=>$result['user_pets_for_sale'][$i]['pet_image'],'condition'=>$result['user_pets_for_sale'][$i]['condition'],'price'=>$result['user_pets_for_sale'][$i]['price']];
					}
				}
				if(!empty($result['user_posts'])){
					for($i=0;$i<count($result['user_posts']);$i++){
						$response['user_posts'][$i]=['post_id'=> $result['user_posts'][$i]['id'],'like_status'=> $result['user_posts'][$i]['like_status'],'title'=> $result['user_posts'][$i]['title'],'post_image'=> $result['user_posts'][$i]['post_image'],'user_id'=> $result['user_posts'][$i]['user_id']];
					}
				}
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
		$msg ="http://mastercreationz.com/dog_heaven/DogHeaven/DogHeavenApi/update_password";
		$body = $this->load->view('confirmation.html',$msg,TRUE);
		$message = $body."(click here to verify email".$msg.")";
		$send_email = $this->DogHeavenModel->sendEmail($postData,$message);
		if($send_email == 1){
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
			$password = md5($this->input->post('password'));
			$email = $this->input->post('email');
			// echo $email;
			//echo $password;
			$res = $this->DogHeavenModel->password_update($email,$password);
			if($res == 1) {
				echo "password updated";
			}
			else{
				echo "not changed";
			}
		}
	}
	/* function name : like posts
	description:fucntion workes for post likes if user click  like for any post  */

		public function posts_like() {
			$rest_json    =    file_get_contents("php://input");
		  $postData    =    json_decode($rest_json, true);
			$response = [];
			$result = $this->DogHeavenModel->like_posts($postData);
			// echo "<pre>";
			// print_R($result);
			if($result['like_status'] == 1){
				$response['success'] = "true";
				$response['like_status'] = $result['like_status'];
				$response['message'] = "post liked successfully";
			}
			else{
				$response['success'] = "false";
				$response['like_status'] = $result['like_status'];
				$response['message'] = "post unlikes successfully";
			}
			echo json_encode($response);
		}

		/* function name : pet_gallery
		description: adding images to gallery*/

	public function pet_gallery(){
		$data = array();
		$uploadData = [];
		$response = [];
		$files = $_FILES;
		if( !empty($_FILES['file']['name'])){
		 $filesCount = count($_FILES['file']['name']);
		//  $response['count'] = $filesCount;
		//  echo json_encode($response);die;
		$uploadPath = 'pet_gallery/';
		for($i = 0; $i < $filesCount; $i++){
			$_FILES['file']['name'] = $files['file']['name'][$i];
			$_FILES['file']['type'] = $files['file']['type'][$i];
			$_FILES['file']['tmp_name'] = $files['file']['tmp_name'][$i];
			$_FILES['file']['error'] = $files['file']['error'][$i];
			$_FILES['file']['size'] = $files['file']['size'][$i];
			$config['upload_path'] = $uploadPath;
			$config['allowed_types'] = 'gif|jpg|png';
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			$upload_data = $this->upload->data();
			$name_array[] = $files['file'];
			$fileName =  $files['file'];
			$images= $fileName['name'];
			$image_file[] = $uploadPath.$images[$i];
			$type = $fileName['type'];
			$size = $fileName['size'];
			move_uploaded_file($fileName['tmp_name'][$i],'pet_gallery/'.$fileName['name'][$i]);
		 }
		 $file_name = implode(',',$image_file);
		 $file_type = implode(',',$type);
		 $file_size = implode(',',$size);
	 	}
		$data = [
			 'image_name'=>$file_name,
			 'image_size'=>$file_size,
			 'image_type'=>$file_type,
			 'user_id'=>$this->input->post('user_id')
			];
		$result = $this->DogHeavenModel->pet_gallery_images($data);
		// echo $result;
		if($result == 1){
			$response['success'] = "true";
			$response['message'] = "images added  to pet gallery";
			$response['file_data'] =$data['image_name'];
		}

		else {
			$response['success'] = "false";
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
				"file_size"=>$_FILES['file']['size'],
				"file_type"=>$extension,
				 "user_id"=>$this->input->post('user_id')
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

	 /* function name : del gallery images
 	description: fucntion works for delete gallery images if user want to remove imagess */

	public function del_gallery_image() {
		$rest_json    =   file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$res = $this->DogHeavenModel->delete_image($postData);
		if($res){
			$response['success'] = "true";
			$response['message'] = 'image deleted';
		}
		else{
			$response['success'] = "false";
			$response['message'] = 'image not deleted';
		}
		echo json_encode($response);
 	}
 /* function for adding pet for sale function name :pet for sale*/
	public function pet_for_sale(){
		$data = array();
		$uploadData = [];
		$response = [];
		$files = $_FILES;
		if( !empty($_FILES['file']['name'])){
		 $filesCount = count($_FILES['file']['name']);
		//  $response['count'] = $filesCount;
		//  echo json_encode($response);die;
		$uploadPath = 'pet_for_sale/';
		 for($i = 0; $i < $filesCount; $i++){
			 $_FILES['file']['name'] = $files['file']['name'][$i];
			 $_FILES['file']['type'] = $files['file']['type'][$i];
			 $_FILES['file']['tmp_name'] = $files['file']['tmp_name'][$i];
			 $_FILES['file']['error'] = $files['file']['error'][$i];
			 $_FILES['file']['size'] = $files['file']['size'][$i];
			 $config['upload_path'] = $uploadPath;
			 $config['allowed_types'] = 'gif|jpg|png';
			 $this->load->library('upload', $config);
			 $this->upload->initialize($config);
			 $upload_data = $this->upload->data();
			 $name_array[] = $files['file'];
			 $fileName =  $files['file'];
			 $images = $fileName['name'];
			 $image_file[] = $uploadPath.$images[$i];
			 $tmp_name = $fileName['tmp_name'];
			//  print_r($fileName['tmp_name'][0]);
			 $type = $fileName['type'];
			 $size = $fileName['size'];
			 move_uploaded_file($fileName['tmp_name'][$i],'pet_for_sale/'.$fileName['name'][$i]);
			 $this->resize_image($fileName['name'][$i],$uploadPath);
			}
		 $file_name =  implode(',',$image_file);
		 $file_type = implode(',',$type);
		 $file_size = implode(',',$size);
		}
		$data = [
			'description'=>$this->input->post('description'),
			'breed'=>$this->input->post('breed'),
			'location'=>$this->input->post('location'),
			'name'=>$this->input->post('name'),
			'condition'=>$this->input->post('condition'),
			'price'=>$this->input->post('price'),
			'user_id'=>$this->input->post('user_id'),
			'pet_image'=>$file_name,
			'quantity'=>$this->input->post('quantity'),
			'product_id'=>uniqid(),
			'longitude'=>$this->input->post('longitude'),
			'latitude'=>$this->input->post('latitude')
		];
		// echo "<pre>";
		// print_r($data);
		$res =  $this->DogHeavenModel->sale_pet($data);
		if($res == 1){
			$response['success'] = "true";
			$response['message'] = "pet added for sale successfully";
		}
		else{
			$response['success'] = "true";
			$response['message'] = "pet added for sale successfully";
		}
		echo json_encode($response);
	}

	/* function name :edit profile_image
	description:function for update gallery for edited image*/

 	public function edit_profile_image(){
		$rest_json    =   file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		define('UPLOAD_DIR','pet_gallery/');
		$img = $postData['image_name'];
		$img = str_replace('data:image/png;base64,', '',$img);
		$img = str_replace(' ', '+',$img);
		$img_code = base64_decode($img);
		$file =UPLOAD_DIR. uniqid() . '.png';
		$success = file_put_contents($file, $img_code);
		$postData['image_name'] = $file;
		$res=  $this->DogHeavenModel->edit_profile($postData);
		if($res == 1){
			 $response['success'] = "true";
			 $response['message'] = "image updated successfully";
		}
		else{
			$response['success'] = "false";
			$response['message']="image not updated";
		}
		echo json_encode($response);
 	}
	/* function name : get_product_list
	description:  get list of all petfor sale*/

 public function get_product_list(){
	 $response = [];
	 $rest_json    =   file_get_contents("php://input");
	 $postData    =    json_decode($rest_json, true);
	 $result = $this->DogHeavenModel->get_pet_sale_list($postData);
	//  echo "<pre>";
	//  print_r($result);

	if(!empty($result)){
	 $response['success'] = "true";
	 $response['message'] = "data fetched successfully";
		if($postData['longitude'] != null && $postData['latitude'] != null){
			$response['pet_detials'] = $result;
		}
		else{
			for($i=0;$i<count($result);$i++){
				$images_data = $result[$i]['pet_image'];
			  $img_data = explode(',',$images_data);
				for($j=0;$j<count($img_data);$j++){
				 	$images = $images_data;
					$data = explode('.',$img_data[$j]);
				}
				$thumb = $data[0].'_thumb'.'.'.$data[1];
				$response['pet_details'][$i] = ['user_id'=>$result[$i]['user_id'],'product_id'=>$result[$i]['product_id'],'name'=>$result[$i]['name'],'breed'=>$result[$i]['breed'],'quantity'=>$result[$i]['quantity'],'longitude'=>$result[$i]['longitude'],'latitude'=>$result[$i]['latitude'],'description'=>$result[$i]['description'],'location'=>$result[$i]['location'],'condition'=>$result[$i]['condition'],'price'=>$result[$i]['price'],'pet_image'=>$images,'thumb_image_path'=>'http://mastercreationz.com/dog_heaven/DogHeaven/'.$thumb];
				// $response['pet_details'][$i] = ['user_id'=>$result[$i]['user_id'],'product_id'=>$result[$i]['product_id'],'name'=>$result[$i]['name'],'description'=>$result[$i]['description'],'location'=>$result[$i]['location'],'condition'=>$result[$i]['condition'],'price'=>$result[$i]['price'],'pet_image'=>$images];
			}
		}
	}
	 	else{
		 $response['success'] = "false";
		 $response['message'] = "data not fetched";
		}
	 echo json_encode($response);
	 }
	 /* function name :timeline_search
 	description: search user business,comany,trends,breeds ,followers*/

	public function timeline_search() {
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$res = $this->DogHeavenModel->search_user($postData);
		// echo "<pre>";
		// print_r($res);
		if(!empty($res)){
			$response['success'] = "true";
		  $response['message'] = "search successfully found";
			for($i=0;$i<count($res);$i++) {
				if($res[$i]['user_type']){
					if($res[$i]['user_type'] == "lover"){
						$response['Details'][$i] = ['name'=>$res[$i]['first_name'],'email'=>$res[$i]['email'],'user_name'=>$res[$i]['user_name'],'profile_image'=>$res[$i]['profile_image'],'user_type'=>$res[$i]['user_type'],'user_id'=>$res[$i]['unique_id'],'follow_status'=>$res[$i]['follow_status']];
					}
					if($res[$i]['user_type'] == "owner"){
						$response['Details'][$i] = ['name'=>$res[$i]['owner_name'],'email'=>$res[$i]['owner_email'],'profile_image'=>$res[$i]['owner_profile_picture'],'user_type'=>$res[$i]['user_type'],'user_id'=>$res[$i]['unique_id']];
					}
					if($res[$i]['user_type'] == "business"){
						$response['Details'][$i] = ['company'=>$res[$i]['company'],'email'=>$res[$i]['email'],'profile_image'=>$res[$i]['profile_image'],'user_type'=>$res[$i]['user_type'],'user_id'=>$res[$i]['unique_id']];
					}
				}
			}
		}
		else{
			$response['success'] = "false";
			$response['message'] = "no record match please try another keywords";
		}
		echo json_encode($response);
	}

	/* function name : follow
	description: function works for to follow users */

	public function follow() {
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$res = $this->DogHeavenModel->follower($postData);
		if(!empty($res)) {
			$response['success'] = "true";
			$response['message'] = "follow status updated";
		}
		else{
			$response['success'] = "false";
			$response['message']="not updated";
		}
		echo json_encode($response);
	}

	/* function name : edit_user_profile
	description:  function to edit user profile*/

	public function edit_user_profile() {
	 $rest_json    =    file_get_contents("php://input");
	 $postData    =    json_decode($rest_json, true);
	 $res = $this->DogHeavenModel->update_user($postData);
	 if(!empty($res)) {
		 $response['success'] = "true";
		 $response['message'] = "user updated successfully";
	 }
	 else{
		 $response['success'] = "false";
		 $response['message'] = "not updated";
	 }
	 echo json_encode($response);
	}

	/* function name : save inner purchase detials
	description:inner purchase*/

 public function save_inner_purchase_details(){
 	$rest_json    =    file_get_contents("php://input");
 	$postData    =    json_decode($rest_json, true);
	$response = [];
	$result = $this->DogHeavenModel->inner_purchase($postData);
	if(!empty($result)){
		$response['success'] = "true";
		$response['message'] = "detailes saved successfully";
		$response['details'] = ['user_id'=>$result['user_id'],'subscription'=>$result['subscription']];
	}
 	echo json_encode($response);
 }

 /* function name : pet_purchase
 description: fucntio for  purchase pet if a user purchase any pet it mark an  entry in databse */

 	public function pet_purchase(){
	 $rest_json    =    file_get_contents("php://input");
	 $postData    =    json_decode($rest_json, true);
	 $response = [];
	 $result = $this->DogHeavenModel->purchase_pet($postData);
	 if($result == 1){
		 $response['success'] = "true";
		 $response['message'] = "purchase successfull";
	 }
	 else{
		 	$response['success'] = "false";
			$response['message'] = "purchase unsuccessfull";
	 }
	 echo json_encode($response);
 	}

	/* function name : user_purchase_list
	description: function to get user putrchase list */

	public function user_purchase_list(){
		$rest_json    =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$result = $this->DogHeavenModel->user_purchase_list($postData);
		if(!empty($result)){
			$response['success'] = "true";
			$response['message'] = "purchase list fetched successfully";
			$response['purchase_details'] = $result;
		}
		else{
			$response['success'] = "false";
			$response['message'] = "purchase list not fetched successfully";
		}
		echo json_encode($response);
	}
		/* function name : user_sale_list
	 description: get user sale list list of pet he has entered for sale */

	public function user_sale_list() {
		$rest_json   =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$result = $this->DogHeavenModel->user_sale_list($postData);
		if(!empty($result)){
			$response['success'] = "true";
			$response['message'] =  "Sale list fethed successfully";
			$response['user_sale_list']=$result;
		}
		else{
			$response['success'] = "false";
			$response['message'] =  "Sale list not fethed ";
		}
		echo json_encode($response);
	}

	/* function name : get_followers
	description: get followers of login user*/

	public function get_followers(){
		$rest_json   =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$result = $this->DogHeavenModel->get_followers($postData);
		if(!empty($result)){
			$response['success'] = "true";
			$response['message'] = "followers are fetched successfully";
			$response['follwers_details'] = $result;
		}
		else{
			$response['success'] = "false";
			$response['message'] = "followers are  not fetched ";
		}
		echo json_encode($response);
	}

	/* function name : user_comments
	description: fucntion for entering comments in database */

	public function user_comments(){
		$rest_json   =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$result = $this->DogHeavenModel->user_comments($postData);
		if(!empty($result)){
			$response['success'] = "true";
			$response['comment_details'] = ['comment'=>$result['comment'],'post_id'=>$result['post_id'],'user_id'=>$result['user_id']];
			$response['message']  = "you commented on this post successfully";
		}
		else{
			$response['success'] = "false";
			$response['message'] = " your comment is not posted. ";
		}
		echo json_encode($response);
	}
	/* function to resize_image*/
	public function resize_image($img_name,$uploadPath){
		$source_path = $uploadPath.$img_name;
		$source_path;
		$config_manip = array(
			 'image_library' => 'gd2',
			 'source_image' => $source_path,
			 'maintain_ratio' => TRUE,
			 'create_thumb' => TRUE,
			 'thumb_marker' => '_thumb',
			 'width' => 150,
			 'height' => 150
		);
		$this->load->library('image_lib', $config_manip);
		if (!$this->image_lib->resize()) {
				echo $this->image_lib->display_errors();
		}
		$this->image_lib->clear();
	}
	/* function name : add_contacts
	description:  function  for adding contacts in user contact list*/

	public function add_contacts(){
		$rest_json   =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$res = $this->DogHeavenModel->add_contacts($postData);
		if($res == 1) {
			$response['success'] = "true";
			$response['message'] = "contact added successfully";
		}else{
			$response['success'] = "false";
			$response['message'] = "contact can`t added";
		}
		echo json_encode($response);
	}

	/* function name : create_blacklist
	description:  function  for adding user in blacklist*/

	public function create_black_list(){
		$rest_json   =    file_get_contents("php://input");
		$postData    =    json_decode($rest_json, true);
		$response = [];
		$res = $this->DogHeavenModel->blocked_contacts($postData);
		if($res == 1) {
			$response['success'] = "true";
			$response['message'] = "user  added  to black list successfully";
		} else {
			$response['success'] = "false";
			$response['message'] = "user can`t added to blacklist";
		}
		echo json_encode($response);
	}
}
?>
