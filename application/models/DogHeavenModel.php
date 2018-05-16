<?php
class DogHeavenModel extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	/*  pet_lovers registeration function */

	function pet_lovers_register($postData){

		$response = [];
		$this->db->where('email',$postData['email']);
		$query1 = $this->db->get('pet_lovers');
		$row =  $query1->row_array();
		if($row){
			$response['status'] = "false";
			$response['message'] ="this email already exixts please  try another email";
			echo json_encode($response);
		}
		else{
			$query = $this->db->insert('pet_lovers',$postData);
			$insert_id =  $insert_id = $this->db->insert_id();
			$query2 = $this->db->get_where('pet_lovers',array('id'=>$insert_id));
			$res = $query2->row_array();
			if($res) {
				$email = $postData['email'];
				$msg = "http://mastercreationz.com/dog_heaven/DogHeaven/DogHeavenApi/confirmation_page?email=".$email;
				$body = $this->load->view('confirmation.html',$msg,TRUE);
				$message = $body."( click here to verify email  ".$msg.")";
				$send_email = $this->sendEmail($postData['email'],$message);
				if($send_email){
					$response['success'] = 'true';
					$response['message'] = 'data inserted suuccessfully';
				}
				//$response['user_details']  = ['id'=>$res['id'],'email'=>$res['email']];
			}
			else {
				$response['success'] = 'false';
				$response['message'] = 'data not inserted';
			}
			echo json_encode($response);
		}

	}

	/*  pet_owners registeration function */

	function pet_owners_register($postData){
		$data1  = [
			'owner_name' => $postData['owner_name'],
			'owner_email' => $postData['owner_email'],
			'owner_dob' => $postData['owner_dob'],
			'gender' => $postData['gender'],
			'location' => $postData['location'],
			'user_name' =>$postData['user_name'],
			'password'=>md5($postData['password']),
			'owner_profile_picture' =>	$postData['owner_profile_picture'],
			'unique_id' => uniqid()
		];
		$data2 = [
			'name' => $postData['name'],
			'age' => $postData['age'],
			'city' => $postData['state'],
			'pet_history' => $postData['pet_history'],
			'profile_picture' => $postData['profile_picture'],
			'user_id' => ''
		];
		// echo "<pre>";
		// print_r($data2);
		$response = [];
		$this->db->where('owner_email',$data1['owner_email']);
		$query1 = $this->db->get('pet_owners');
		$row =  $query1->row_array();
		if($row){
			$response['success'] = "false";
			$response['message'] ="this email already exixts please  try another email";
			echo json_encode($response);
		}
		else{
			$query = $this->db->insert('pet_owners',$data1);
			if($query){
				$insert_id =  $insert_id = $this->db->insert_id();
				$query2 = $this->db->get_where('pet_owners',array('id'=>$insert_id));
				$res = $query2->row_array();
				// echo "<pre>";
				// print_r($res);
				if($res){
					$email = $postData['owner_email'];
					$msg = "http://mastercreationz.com/dog_heaven/DogHeaven/DogHeavenApi/confirmation_page?email=".$email;
					$body = $this->load->view('confirmation.html',$msg,TRUE);
					$message = $body."(click here to verify email".$msg.")";
					$send_email = $this->sendEmail($data1['owner_email'],$message);
					if($send_email == 1){
						$data2['user_id'] = $res['unique_id'];
						$query3 = $this->db->insert('pet_profile',$data2);
					}
				}
			}
			if($res) {
				$response['success'] = 'true';
				$response['message'] = 'data inserted suuccessfully';
				$response['user_details']  = ['id'=>$res['id'],'owner_email'=>$res['owner_email']];
			}
			else {
				$response['success'] = 'false';
				$response['message'] = 'data not inserted';
			}
			echo json_encode($response);
		}
	}
	/*  pet_business registeration function */

	function pet_business_register($postData){
		$response = [];
		$this->db->where('email',$postData['email']);
		$query1 = $this->db->get('pet_business');
		$row =  $query1->row_array();
		if($row){
			$response['success'] = "false";
			$response['message'] ="this email already exixts please  try another email";
			echo json_encode($response);
		}
		else{
			$query = $this->db->insert('pet_business',$postData);
			$insert_id =  $insert_id = $this->db->insert_id();
			$query2 = $this->db->get_where('pet_business',array('id'=>$insert_id));
			$res = $query2->row_array();
			if($res) {
				$email = $postData['email'];
				$msg = "http://mastercreationz.com/dog_heaven/DogHeaven/DogHeavenApi/confirmation_page?email=".$email;
				$body = $this->load->view('confirmation.html',$msg,TRUE);
				$message = $body."(click here to verify email".$msg.")";
				$send_email = $this->sendEmail($postData['email'],$message);
				if($send_email){
					$response['success'] = 'true';
					$response['message'] = 'data inserted suuccessfully';
					$response['user_details']  = ['id'=>$res['id'],'email'=>$res['email']];
				}
			}
			else {
				$response['success'] = 'false';
				$response['message'] = 'data not inserted';
			}
			echo json_encode($response);
		}
	}

	/* login function for all the users  */

	function user_login($postData) {
		$email = $postData['email'];
		$password = md5($postData['password']);
		$where = "email='$email' OR user_name='$email' AND password='$password' " ;
		$this->db->where($where);
		$query1 = $this->db->get('pet_lovers');
		// echo $this->db->last_query();
		$row1 = $query1->row_array();
		$where = "owner_email='$email' OR user_name='$email' AND password='$password' " ;
		$this->db->where($where);
		$query2 = $this->db->get('pet_owners');
		$row2 = $query2->row_array();
		$where = "email='$email' AND password='$password' " ;
		$this->db->where($where);
		$query3 = $this->db->get('pet_business');
		$row3 = $query3->row_array();
		if($row1 ||$row2 ||$row3){
			if($row1){
				return $row1;
			}
			if($row2){
				return $row2;
			}
			if($row3){
				return $row3;
			}
		}
	}
	/* function to add_posts*/
	function add_new_posts($postData) {;
		$query = $this->db->insert('posts',$postData);
		// echo $this->db->last_query();
		if($query){
			return 1;
		}
		else{
			return 0;
		}
	}
	/* functiom to get user posts */
	function get_user_posts($postData){
		$this->db->select('id,title,post_image,user_id,user_type,created_at');
		$user_posts = $this->db->get_where('posts',['user_id'=>$postData['user_id']]);
		$get_user_posts = $user_posts->result_array();
		$sql = "SELECT `k`.`post_id`, `k`.`user_id`, `k`.`like_status`, `k`.`post_user_id` FROM `post_likes` `k` INNER JOIN `posts` `p` ON `k`.`post_user_id`=`p`.`user_id` WHERE `k`.`user_id` = '5ab4e7c000b81' and k.post_id=p.id";
		$post_likes = $this->db->query($sql);
		$user_likes = $post_likes->result_array();
		// echo "like posts array";
		// echo "<pre>";
		// print_R($user_likes);
		// echo "user post array";
		// echo "<pre>";
		// print_R($get_user_posts);
		$posts_array = [];
		$user_posts = $this->db->get('posts');
		$get_res = $user_posts->result_array();
		for($x=0;$x<count($get_user_posts);$x++){
			$get_user_posts[$x]['like_status'] = 0;
		}
			for($i=0;$i<count($user_likes);$i++){
				for($j=0;$j<count($get_user_posts);$j++){
					if($user_likes[$i]['post_id'] == $get_user_posts[$j]['id'] ){
						$get_user_posts[$j]['like_status'] = $user_likes[$i]['like_status'];
					}
				}
			}
		$this->db->select('p.id,p.title,p.post_image,p.user_id,p.user_type,f.follower_id,f.following_id,p.created_at');
		$this->db->from('posts p');
		$this->db->join('followers f', 'f.following_id=p.user_id', 'left');
		$this->db->where('f.follower_id',$postData['user_id']);
		$friends_posts = $this->db->get();
		$get_friends_posts = $friends_posts->result_array();
		for($y=0;$y<count($get_friends_posts);$y++){
			$get_friends_posts[$y]['like_status'] = 0;
		}
			for($q=0;$q<count($user_likes);$q++){
				for($p=0;$p<count($get_friends_posts);$p++){
					if($get_friends_posts[$p]['id'] == $user_likes[$q]['post_id']){
						$get_friends_posts[$p]['like_status'] = $user_likes[$q]['like_status'];
						break;
					}
				}
			}
		$count = count($get_res);
		$post_array =array_merge($get_friends_posts,$get_user_posts);
		// echo "<pre>";
		// print_R($post_array);
		$final_post_array = [];
		$post_array =array_merge($get_friends_posts,$get_user_posts);

		for($k=0;$k<count($post_array);$k++){

			if($post_array[$k]['user_type'] == "lover"){
				// print_R($post_array[$k]['user_id']);
				$this->db->select('first_name,last_name,email,profile_image');
				$lovers = $this->db->get_where('pet_lovers',['unique_id'=>$post_array[$k]['user_id']]);
				$get_res = $lovers->row_array();
				// echo "<pre>";
				// print_r($final_post_array);die;
			}
			elseif($post_array[$k]['user_type'] == "owner"){
				$this->db->select('owner_name,owner_email,owner_profile_picture');
				$owners= $this->db->get_where('pet_owners',['unique_id'=>$post_array[$k]['user_id']]);
				$get_res = $owners->row_array();
				// echo "<pre>";
				// print_r($get_res);
			}
			else {
				$this->db->select('company,email,profile_image');
				$business = $this->db->get_where('pet_business',['unique_id'=>$post_array[$k]['user_id']]);
				$get_res = $business->row_array();
				// echo "<pre>";
				// print_r($get_res);
			}
			$final_post_array[]  = array_merge($post_array[$k],$get_res);
		}
		// echo "<pre>";
		// print_r($final_post_array);
		if(!empty($final_post_array)) {
			return $final_post_array;
		}
		else{
			return 0;
		}
	}


	/* fucntion to add new albums */
	function add_new_album($postData){
		$query = $this->db->insert('albums',$postData);
		if($query){
			return 1;
		}
		else{
			return 0;
		}
	}
	/** fucntion to get pet owners profile in formatiom*/
	function get_profile($postData){
		$this->db->select('*')
		->from('pet_profile')
		->join('pet_owners', 'pet_owners.unique_id = pet_profile.user_id')->where('pet_profile.user_id',$postData['user_id']);
		$query1= $this->db->get();
		$row1 = $query1->row_array();
		$query2 = $this->db->get_where('pet_lovers',['unique_id'=>$postData['user_id']]);
		$row2 = $query2->row_array();
		$query3 = $this->db->get_where('pet_business',['unique_id'=>$postData['user_id']]);
		$row3= $query3->row_array();
		$query = $this->db->get('posts');
		$row = $query->result_array();
		$user_posts = $this->db->get_where('posts',['user_id'=>$postData['user_id']]);
		$get_posts = $user_posts->result_array();
		$this->db->select('k.post_id,k.user_id,k.like_status');
		$this->db->from('post_likes k');
		$this->db->join('posts p', 'p.id=k.post_id', 'left');
		$this->db->where('k.user_id',$postData['user_id']);
		$post_likes = $this->db->get();
		$user_likes = $post_likes->result_array();
		$this->db->last_query();
		for($x=0;$x<count($get_posts);$x++){
			for($y=0;$y<count($user_likes);$y++){
				if($get_posts[$x]['id'] == $user_likes[$y]['post_id']){
					$get_posts[$x]['like_status'] = $user_likes[$y]['like_status'];
				}
				else{
					$get_posts[$x]['like_status'] = 0;
				}
			}
		}
		// echo "<pre>";
		// print_R($get_posts);
		$user_images = $this->db->get_where('gallery_images',['user_id'=>$postData['user_id']]);
		$user_image_data = $user_images->result_array();
		$user_docs= $this->db->get_where('pet_documents',['user_id'=>$postData['user_id']]);
		$user_doc_data = $user_docs->result_array();
		$user_pets = $this->db->get_where('pet_for_sale',['user_id'=>$postData['user_id']]);
		$pet_result = $user_pets->result_array();
		// echo "<pre>";
		// print_r($pet_result);
		$subscription = $this->db->get_where('inner_purchase',['user_id'=>$postData['user_id']]);
		$sub_result = $subscription->row_array();
		$get_followers = $this->db->get_where('followers',['following_id'=>$postData['user_id']]);
		$get_all_followers = $get_followers->result_array();
		if(!empty($get_all_followers)){
			$count_followers = count($get_all_followers);
		}
		else{
			$count_followers = 0;
		}
		$user_following = $this->db->get_where('followers',['follower_id'=>$postData['user_id']]);
		$get_all_following = $user_following->result_array();
		if(!empty($get_all_following)){
			$count_following = count($get_all_following);
		}
		else{
			$count_following = 0;
		}
		$user_details = [];
		if( $row1||$row2||$row3 ){
			if($row1){
				$user_details['user'] = $row1;
				$user_details['user_images'] = $user_image_data;
				$user_details['user_files'] = $user_doc_data;
				$user_details['user_pets_for_sale'] = $pet_result;
				$user_details['user_posts'] = $get_posts;
				$user_details['subscription_details'] = $sub_result;
				$user_details['followers'] = $count_followers;
				$user_details['following'] = $count_following;
					return $user_details;
			}
			if($row2){
				$user_details['user'] = $row2;
				$user_details['user_images'] = $user_image_data;
				$user_details['user_files'] = $user_doc_data;
				$user_details['user_pets_for_sale'] = $pet_result;
				$user_details['user_posts'] = $get_posts;
				$user_details['subscription_details'] = $sub_result;
				$user_details['followers'] = $count_followers;
				$user_details['following'] = $count_following;
				return $user_details;
			}
			if($row3){
				$user_details['user'] = $row3;
				$user_details['user_images'] = $user_image_data;
				$user_details['user_files'] = $user_doc_data;
				$user_details['user_pets_for_sale'] = $pet_result;
				$user_details['user_posts'] = $get_posts;
				$user_details['subscription_details'] = $sub_result;
				$user_details['followers'] = $count_followers;
				$user_details['following'] = $count_following;
					return $user_details;
			}
		}
		else {
			return 0;
		}
	}


	/* function to send email to user*/
	public function sendEmail($email,$message){
		$config = Array(
			'protocol' => 'sendmail',
			'mail' => '/usr/sbin/sendmail',
			'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_port' => 465,
			'smtp_user' => 'xxx',
			'smtp_pass' => 'xxx',
			'mailtype'  => 'html',
			'charset' => "utf-8"
		);

		$this->load->library('email',$config);
		$this->email->from('arora17poonam@gmail.com', 'poonam');
		$this->email->to($email);
		$this->email->subject('Dog Heaven');
		$this->email->message($message);
		if($this->email->send()){
			return 1;
		}
		else{
			return 0;
		}
	}

	public function confirmation_page($email){
		$data=array('confirmation_status'=>1);
		$this->db->set('confirmation_status',1,false);
		$this->db->where('email',$email);
		$query1 = $this->db->update('pet_lovers',$data);
		$this->db->set('confirmation_status',1);
		$this->db->where('owner_email',$email);
		$query2 = $this->db->update('pet_owners',$data);
		$this->db->set('confirmation_status',1);
		$this->db->where('email',$email);
		$query3 = $this->db->update('pet_business',$data);
		if($query1||$query2||$query3){
			echo $email ." you are successfully verfied";
		}
	}


	function password_update($email,$password){
		// echo $email;
		$check_pet_lovers_email = $this->db->get_where('pet_lovers',['email'=>$email]);
		$row1 = $check_pet_lovers_email->row_array();
		$check_pet_owners_email = $this->db->get_where('pet_owners',['owner_email'=>$email]);
		$row2 = $check_pet_owners_email->row_array();
		$check_pet_business_email = $this->db->get_where('pet_business',['email'=>$email]);
		$row3 = $check_pet_business_email->row_array();
		// echo "<pre>";
		// print_R($row3);
		if($row1||$row2||$row3){
			if($row1){
				$user_email = $row1['email'];
				$data = ['password'=>$password];
				$this->db->where('email',$user_email);
				$update = $this->db->update('pet_lovers',$data);
				if($update){
					return 1;
				}
				else {
					return 0;
				}
			}
			if($row2){
				$user_email = $row2['owner_email'];
				$data = ['password'=>$password];
				$this->db->where('owner_email',$user_email);
				$update = $this->db->update('pet_owners',$data);
				if($update){
					return 1;
				}
				else {
					return 0;
				}
			}
			if($row3){
				$user_email = $row3['email'];
				$data = ['password'=>$password];
				$this->db->where('email',$user_email);
				$update = $this->db->update('pet_business',$data);
				if($update){
					return 1;
				}
				else {
					return 0;
				}
			}
		}
	}
	// public function search_user($postData){
	// 	$where = "email='$postData' OR user_name='$postData'" ;
	// 	$this->db->where($where);
	// 	$serach_user = $this->db->get('pet_lovers');
	// 	$user = $search_user->row_array();
	// 	echo "<pre>";
	// 	print_r($user);
	//
	// }
/* like user_posts*/
/* like user_posts*/
	public function like_posts($postData){
		$data = [
			'user_id' => $postData['user_id'],
			'post_id' => $postData['post_id'],
			'like_status' => 1,
			'post_user_id'=>$postData['post_user_id']
		];
		$check_existing = $this->db->get_where('post_likes',['user_id'=>$postData['user_id'],'post_id'=>$postData['post_id']]);
		$get_existing_result = $check_existing->row_array();
		// if($postData['user_type'] == 'lover') {
		// 	$table = "pet_lovers";
		// } elseif($postData['user_type'] == 'owner'){
		// 	$table = "pet_owners";
		// } else {
		// 	$table = "pet_business";
		// }
		// $get_post_user_email = $this->db->get_where($table,['unique_id'=>$postData['post_user_id']]);
		// $result = $get_post_user_email->row_array();
		// if($postData['user_type'] == 'lover') {
		// 	$email = $result['email'];
		// 	$name = $result['first_name'];
		// } elseif($postData['user_type'] == 'owner'){
		// 		$email = $result['owner_email'];
		// 		$name = $result['owner_name'];
		// } else {
		// 		$email = $result['email'];
		// 		$name = $result['company'];
		// }
		// $message = $name." has liked your post";
		// $send_email = $this->sendEmail($email,$message);
		if(empty($get_existing_result)) {
			$query = $this->db->insert('post_likes',$data);
			$insert_id = $this->db->insert_id();
			$get_last_result =  $this->db->get_where('post_likes',['id'=>$insert_id]);
			$result = $get_last_result->row_array();
			if($query) {
				return $result;
			}
		}
		else {
			if($get_existing_result['like_status'] == 1){
				$data1 = ['like_status'=> 0];
			}
			else {
				$data1 = ['like_status'=> 1];
			}
			$this->db->set('like_status',false);
			$this->db->where(['user_id'=>$postData['user_id'],'post_id'=>$postData['post_id']]);
			$update = $this->db->update('post_likes',$data1);
			if($update){
				$get_updated_row = $this->db->get_where('post_likes',['user_id'=>$postData['user_id'],'post_id'=>$postData['post_id']]);
				return $updated_result = $get_updated_row->row_array();
			}
		}
	}

/* added images to pet gallkery*/
	function pet_gallery_images($data){
		$query = $this->db->insert("gallery_images",$data);
		if($query) {
			return 1;
		}
		else {
			return 0;
		}
	}

	function upload_documents($data){
		$query = $this->db->insert('pet_documents',$data);
		if($query){
			return 1;
		}
		else{
			return 0;
		}
	}
	// function fetch_details($postData){
	// 	$this->db->select('*')
	// 	->from('gallery_images')
	// 	->join('pet_documents', 'pet_documents.user_id = gallery_images.user_id')->where('gallery_images.user_id',$postData['user_id']);
	// 	$query= $this->db->get();
	// 	$row  = $query->result_array();
	// 	// echo $this->db->last_query();
	// 	// echo "<pre>";
	// 	// print_r($row);
	// 	if(!empty($row)){
	// 		return $row;
	// 	}
	// 	else {
	// 		return 0;
	// 	}
	// }
	function delete_image($postData){
		$query = $this->db->get('gallery_images',['id',$postData['image_id'],'user_id'=>$postData['user_id']]);
		$row = $query->row_array();
 // 	 echo "<pre>";
 //  print_R($row);
		if($row){
			$this->db->where('id', $postData['image_id']);
			$del = $this->db->delete('gallery_images');
			if($del){
				return 1;
			}
			else{
				return 0;
			}
	 }
	}

	function sale_pet($data){
		$query = $this->db->insert('pet_for_sale',$data);
		if($query){
			return 1;
		}
		else{
			return 0;
		}
	}

	function edit_profile($postData){
		$query = $this->db->get_where('gallery_images',['id'=>$postData['image_id'],'user_id'=>$postData['user_id']]);
		$row = $query->row_array();
		$data = ['image_name'=>$postData['image_name']];
		$this->db->where('id',$postData['image_id']);
		$update = $this->db->update('gallery_images',$data);
		if($update){
			return 1;
		}
		else{
			return 0;
		}
	}
	function get_pet_sale_list($postData){

		if($postData['search'] == null && $postData['latitude'] ==null && $postData['longitude'] == null){
			$this->db->where('user_id !=', $postData['user_id']);
			$this->db->where('purchase_status',0);
			$query = $this->db->get('pet_for_sale');
			$row = $query->result_array();
			// echo $this->db->last_query();
		}
		elseif($postData['search']!=null && $postData['latitude'] ==null && $postData['longitude'] == null){
			$this->db->like('name',$postData['search']);
			$this->db->or_like('breed',$postData['search']);
			$this->db->where('user_id !=', $postData['user_id']);
			$query1 = $this->db->get('pet_for_sale');
			$row1 = $query1->result_array();
		}
		else{
			$this->db->like('name',$postData['search']);
			$this->db->where('user_id !=', $postData['user_id']);
			$search = $this->db->get('pet_for_sale');
			$result = $search->result_array();
			if(!empty($result)){
				$latitude = $postData['latitude'];
				$longitude = $postData['longitude'];
				$location = 	$this->db->query('SELECT id,name,breed,pet_image,longitude,latitude,  ( 3959   * acos( cos( radians('.$latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( '.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( latitude ) ) ) ) AS distance FROM pet_for_sale HAVING distance < 10 ORDER BY distance LIMIT 0 , 10');
				$result_locations = $location->result_array();
				$get_result = [];
				for($i=0;$i<count($result_locations);$i++){
					$get_result[]=['name'=>$result_locations[$i]['name'],'breed'=>$result_locations[$i]['breed'],'longitude'=>$result_locations[$i]['longitude'],'latitude'=>$result_locations[$i]['latitude'],'pet_image'=>$result_locations[$i]['pet_image']];
				}
				return $get_result;
			}
		}
		if(!empty($row)){
			return $row;
		}
		elseif(!empty($row1)){
			return $row1;
		}
		else{
			return 0;
		}
	}

	public function search_user($postData){
		$this->db->like('email',$postData['search_data']);
		$this->db->or_like('user_name',$postData['search_data']);
		$this->db->or_like('email',$postData['search_data']);
		$pet_lover =$this->db->get('pet_lovers');
		$match1 = $pet_lover->result_array();
		// echo "<pre>";
		// print_r($match1);
		// echo $this->db->last_query();
		$this->db->like('owner_email',$postData['search_data']);
		$this->db->or_like('owner_name',$postData['search_data']);
		$pet_owner =$this->db->get('pet_owners');
		$match2 = $pet_owner->result_array();
		$this->db->like('company',$postData['search_data']);
		$pet_business =$this->db->get('pet_business');
		$match3 = $pet_business->result_array();
		$query = $this->db->get_where('followers',['follower_id'=>$postData['user_id']]);
		$result = $query->result_array();
		for($i=0;$i<count($match1);$i++){
			if($match1[$i]['unique_id'] == $result[$i]['following_id'] ){
				$match1[$i]['follow_status'] = "following";
			}
			else{
				$match1[$i]['follow_status'] = "follow";
			}
		}
		for($i=0;$i<count($match2);$i++){
			if($match2[$i]['unique_id'] == $result[$i]['following_id'] ){
					$match2[$i]['follow_status'] = "following";
			}
			else
			{
					$match2[$i]['follow_status'] = "follow";
			}
		}
		for($i=0;$i<count($match3);$i++){
			if($match3[$i]['unique_id'] == $result[$i]['following_id'] ){
					$match3[$i]['follow_status'] = "following";;
			}
			else{
					$match3[$i]['follow_status'] = "follow";
			}
		}
		if($match1 || $match2||$match3||$match4){
			if($match1){
				return $match1;
			}
			if($match2){
				return $match2;
			}
			if($match3){
				return $match3;
			}
			if($match4){
				return $match4;
			}
		}
	}
	function follower($postData){
		$check_exist =  $this->db->get_where('followers',['follower_id'=>$postData['follower_id'],'following_id'=>$postData['following_id']]);
		$result = $check_exist->row_array();
		$this->db->select('*');
		$this->db->from('followers');
		$this->db->join('pet_business', 'followers.following_id = pet_business.unique_id');
		$query1 = $this->db->get();
		$res1 = $query1->row_array();
		$this->db->select('*');
		$this->db->from('followers');
		$this->db->join('pet_owners', 'followers.following_id = pet_owners.unique_id');
		$query2 = $this->db->get();
		$res2 = $query2->row_array();
		$this->db->select('*');
		$this->db->from('followers');
		$this->db->join('pet_lovers', 'followers.following_id = pet_lovers.unique_id');
		$query3 = $this->db->get();
		$res3 = $query3->row_array();
		if(empty($result)){
			$postData['follow_status'] = 1;
			$query = $this->db->insert('followers',$postData);
				if($query){
					if($res1) {
						$vcs = $res1['vcs'];
						$vcs_update = $vcs+1;
						$table = 'pet_business';
				}
				elseif($res2) {
					$vcs = $res2['vcs'];
					$vcs_update = $vcs+1;
					$table = 'pet_owners';
				}
				else {
					$vcs = $res3['vcs'];
					$vcs_update = $vcs+1;
					$table = 'pet_lovers';
				}
				$data = ['vcs'=> $vcs_update];
				$this->db->set('vcs',false);
				$this->db->where('unique_id',$postData['following_id']);
				$update = $this->db->update($table,$data);
			}
		}
		else{
			if($result['follow_status'] == 1){
				$follow_status = ['follow_status'=>0];
			} else{
				$follow_status = ['follow_status'=>1];
			}
			$this->db->set('follow_status',false);
			$this->db->where('follower_id',$postData['follower_id']);
			$update = $this->db->update('followers',$follow_status);
			$updated_record =  $this->db->get_where('followers',['follower_id'=>$postData['follower_id'],'following_id'=>$postData['following_id']]);
			$get_result = $updated_record->row_array();
			$updated_status = $get_result['follow_status'];
			if($update){
				if($res1) {
					$vcs = $res1['vcs'];
					if($updated_status == 0){
						$vcs_update = $vcs-1;
					}
					else{
						$vcs_update = $vcs+1;
					}
					$table = 'pet_business';
			}
			elseif($res2) {
				$vcs = $res2['vcs'];
				if($updated_status == 0){
					$vcs_update = $vcs-1;
				}
				else{
					$vcs_update = $vcs+1;
				}
				$table = 'pet_owners';
			}
			else {
				$vcs = $res3['vcs'];
				if($updated_status == 0){
					$vcs_update = $vcs-1;
				}
				else{
					$vcs_update = $vcs+1;
				}
				$table = 'pet_lovers';
			}
			$data = ['vcs'=> $vcs_update];
			$this->db->set('vcs',false);
			$this->db->where('unique_id',$postData['following_id']);
			$update = $this->db->update($table,$data);
			}
		}
		if($update){
			return 1;
		}
		else{
			return 0;
		}
	}
	function update_user($postData){
		$search = $postData['user_type'];
		if($search == "lover"){
			$search = $postData['user_type'];
			define('UPLOAD_DIR1','pet_lovers_profiles/');
			$img1 = $postData['profile_image'];
			$img1 = str_replace('data:image/png;base64,', '',$img1);
			$img1 = str_replace(' ', '+',$img1);
			$img_code1 = base64_decode($img1);
			$file1 =UPLOAD_DIR1. uniqid() . '.png';
			$success1 = file_put_contents($file1, $img_code1);
			$postData['profile_image'] = $file1;
			$data1=array('first_name'=>$postData['first_name'],'last_name'=>$postData['last_name'],'gender'=>$postData['gender'],'email'=>$postData['email'],'location'=>$postData['location'],'link'=>$postData['link'],'profile_image'=>$file1,'dob'=>$postData['dob'],'bio'=>$postData['bio']);
			$this->db->set('first_name','last_name','email','bio','dob','gender','location','profile_image',false);
			$this->db->where('unique_id',$postData['user_id']);
			$this->db->where('user_type','lover');
			$update_lover = $this->db->update('pet_lovers',$data1);
			if($update_lover){
				return 1;
			}
			else{
				return 0;
			}
		}
		if($search == "owner"){
			define('UPLOAD_DIR2','pet_owners_profiles/');
			$img2 = $postData['owner_profile_picture'];
			$img2 = str_replace('data:image/png;base64,', '',$img2);
			$img2 = str_replace(' ', '+',$img2);
			$img_code2 = base64_decode($img2);
			$file2 =UPLOAD_DIR2. uniqid() . '.png';
			$success2 = file_put_contents($file2, $img_code2);
			$postData['owner_profile_picture'] = $file2;
			$data2=array('owner_name'=>$postData['owner_name'],'owner_dob'=>$postData['owner_dob'],'owner_email'=>$postData['owner_email'],'gender'=>$postData['gender'],'location'=>$postData['location'],'owner_profile_picture'=>$file2,'bio'=>$postData['bio']);
			$this->db->set('owner_name','owner_email','owner_dob','gender','location','owner_profile_picture',false);
			$this->db->where('unique_id',$postData['user_id']);
			$this->db->where('user_type','owner');
			$update_owner = $this->db->update('pet_owners',$data2);
			if($update_owner){
				return 1;
			}
			else{
				return 0;
			}
		}
		if($search == "business"){
			define('UPLOAD_DIR3','pet_business_profiles/');
			$img3 = $postData['profile_image'];
			$img3 = str_replace('data:image/png;base64,', '',$img3);
			$img3 = str_replace(' ', '+',$img3);
			$img_code3 = base64_decode($img3);
			$file3 =UPLOAD_DIR3. uniqid() . '.png';
			echo $file3;
			$success3 = file_put_contents($file3, $img_code3);
			$postData['profile_image'] = $file3;
			$data3=array('company'=>$postData['company'],'bio'=>$postData['bio'],'email'=>$postData['email'],'profile_image'=>$file3,'contact_number'=>$postData['contact_number'],'link'=>$postData['link'],'location'=>$postData['location']);
			$this->db->set('company','email','profile_image','bio','contact_number','link','location',false);
			$this->db->where('unique_id',$postData['user_id']);
			$this->db->where('user_type','business');
			$update_business = $this->db->update('pet_business',$data3);
			if($update_business){
				return 1;
			}
			else{
				return 0;
			}
		}
	}


	function inner_purchase($postData){
		$data =['user_id'=> $postData['user_id'],'subscription'=>"true"];
		$query = $this->db->insert('inner_purchase',$data);
		if($query){
			$insert_id = $this->db->insert_id();
			$inserted_details = $this->db->get_where('inner_purchase',['id'=>$insert_id]);
			$row_data = $inserted_details->row_array();
			return $row_data;
		}
		else{
			return 0;
		}
	}

	public function purchase_pet($postData){
		$postData['status'] = 1;
		$insert_buyer = $this->db->insert('pet_buyer',$postData);
		if($insert_buyer){
			$pet_lover_vcs = $this->db->get_Where('pet_lovers',['unique_id'=>$postData['user_id']]);
			$result1 = $pet_lover_vcs->row_array();
			$pet_owner_vcs = $this->db->get_Where('pet_owners',['unique_id'=>$postData['user_id']]);
			$result2 = $pet_owner_vcs->row_array();
			$pet_business_vcs = $this->db->get_Where('pet_business',['unique_id'=>$postData['user_id']]);
			$result3 = $pet_business_vcs->row_array();
			if($result1) {
				$table = 'pet_lovers';
			}
			elseif($result2) {
				$table = "pet_owners";
			}
			else {
				$table = "pet_business";
			}
			$data1 = ['vcs'=>0];
			$this->db->set('vcs',false);
			$this->db->where('unique_id',$postData['user_id']);
			$update1 = $this->db->update($table,$data1);
			$data = ['purchase_status'=>1];
			$this->db->set('purchase_status',false);
			$this->db->where('product_id',$postData['product_id']);
			$update = $this->db->update('pet_for_sale',$data);
			if($update && $update1) {
				return 1;
			}
		}
			else{
				return 0;
			}
		}
		function user_purchase_list($postData){
			$this->db->select('s.breed,s.name,s.quantity,s.description,s.price,s.product_id,s.user_id,s.pet_image,s.condition,s.location');
			$this->db->from('pet_for_sale s');
			$this->db->where('b.user_id',$postData['user_id']);
			$this->db->join('pet_buyer b', 's.product_id = b.product_id'); // this joins the quote table to the topics table
			$query = $this->db->get();
			$data =[];
			$row = $query->result_array();
			// echo "<pre>";
			// print_r($row);
			 for($i=0;$i<count($row);$i++){
				 $data[$i]['user_list'] = ['user_id'=>$row[$i]['user_id'],'quantity'=>$row[$i]['quantity'],'price'=>$row[$i]['price'],'pet_image'=>$row[$i]['pet_image'],'breed'=>$row[$i]['breed'],'location'=>$row[$i]['location'],'condition'=>$row[$i]['condition'],'product_id'=>$row[$i]['product_id'],'name'=>$row[$i]['name'],'description'=>$row[$i]['description'],'price'=>$row[$i]['price']];
			 }
			 if($data)
			{
				// echo "<pre>";
				// print_r($data);
				return $data;
			}
			else{
				return 0;
			}
		}
		function user_sale_list($postData){
			$query  =  $this->db->get_where('pet_for_sale',['user_id'=>$postData['user_id']]);
			$row = $query->result_array();
			// echo $this->db->last_query();
			// echo "<pre>";
			// print_r($row);
			$data = [];
			if(!empty($row)){
				for($i=0;$i<count($row);$i++){
					 $data[$i] = ['user_id'=>$row[$i]['user_id'],'quantity'=>$row[$i]['quantity'],'price'=>$row[$i]['price'],'pet_image'=>$row[$i]['pet_image'],'breed'=>$row[$i]['breed'],'location'=>$row[$i]['location'],'condition'=>$row[$i]['condition'],'product_id'=>$row[$i]['product_id'],'name'=>$row[$i]['name'],'description'=>$row[$i]['description'],'price'=>$row[$i]['price']];
				}
		    return $data;
			}
			else{
				return 0;
			}
		}
		function get_followers($postData){
			if($postData['user_type'] == "business"){
				$table = 'pet_business';
			}
			elseif($postData['user_type'] == "owner"){
				$table = 'pet_owners';
			}
			else{
				$table = 'pet_lovers';
			}
			$query = $this->db->query('select * from  followers f  left join '.$table.'  b on f.following_id=b.unique_id where f.follower_id = "'.$postData['user_id'].'"');
			$rows = $query->result_array();
			$result = [];
			for($i=0;$i<count($rows);$i++){
				if($postData['user_type'] == "business"){
					$result[] = ['id'=>$rows[$i]['id'],'follower_id'=>$rows[$i]['follower_id'],'company'=>$rows[$i]['company'],'email,'=>$rows[$i]['email'],'id'=>$rows[$i]['id'],'contact_number'=>$rows[$i]['contact_number'],'link'=>$rows[$i]['link'],'location'=>$rows[$i]['location'],'profile_image'=>$rows[$i]['profile_image']];
				}
				elseif($postData['user_type'] == "owners"){
					$result[] =  ['id'=>$rows[$i]['id'],'follower_id'=>$rows[$i]['follower_id'],'owner_email'=>$rows[$i]['owner_email'],'owner_name'=>$rows[$i]['owner_name'],'owner_profile_picture'=>$rows[$i]['owner_profile_picture'],'location'=>$rows[$i]['location']];
				}
				else{
					$result =  ['id'=>$rows[$i]['id'],'follower_id'=>$rows[$i]['follower_id'],'first_name'=>$rows[$i]['first_name'],'last_name'=>$rows[$i]['last_name'],'email'=>$rows[$i]['email'],'location'=>$rows[$i]['location'],'profile_image'=>$rows[$i]['profile_image']];
				}
				return $result;
			}
		}
		function user_comments($postData){
			$data = ['user_id'=>$postData['user_id'],'post_id'=>$postData['post_id'],'comment'=>$postData['comment']];
			$query = $this->db->insert('comments',$data);
			if($postData['user_type'] == 'lover') {
				$table = "pet_lovers";
			} elseif($postData['user_type'] == 'owner'){
				$table = "pet_owners";
			} else {
				$table = "pet_business";
			}
			$get_post_user_email = $this->db->get_where($table,['unique_id'=>$postData['post_user_id']]);
			$result = $get_post_user_email->row_array();
			if ($postData['user_type'] == 'lover') {
				$email = $result['email'];
				$name = $result['first_name'];
			} elseif($postData['user_type'] == 'owner'){
					$email = $result['owner_email'];
					$name = $result['owner_name'];
			} else {
					$email = $result['email'];
					$name = $result['company'];
			}
			$message = $name." has commented on  your post";
			$send_email = $this->sendEmail($email,$message);
			if($query){
				$insert_id = $this->db->insert_id();
				$get_last_result = $this->db->get_where('comments',['id'=>$insert_id]);
				$result = $get_last_result->row_array();
				return $result;
			}
			else{
				return 0;
			}
		}
		function add_contacts($postData){
			$insert_contact = $this->db->insert('contacts',$postData);
			if($insert_contact){
				return 1;
			}else {
				return 0;
			}
		}

	function blocked_contacts($postData){
		$check_existing = $this->db->get_where('blocked_users',['blocked_id'=>$postData['blocked_id']]);
		$result = $check_existing->row_array();
		if(empty($result)) {
			$postData['block_status'] = 1;
  	  $insert_user = $this->db->insert('blocked_users',$postData);
			if($insert_user) {
				return 1;
			}
		}else {
			return 0;
		}
	}
}
?>
