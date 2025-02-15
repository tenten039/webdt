<?php
	$filepath = realpath(dirname(__FILE__));
	include_once ($filepath.'/../lib/database.php');
	include_once ($filepath.'/../helpers/format.php');
?> 
<?php 
	class useradmin
	{
		private $db;
		private $fm;
		public function __construct()
		{
			$this->db = new Database();
			$this->fm = new Format();
		}	
		public function signupadmin($adminUser,$adminName,$adminPwd,$adminPwd_2){
            $adminUser = $this->fm->validation($adminUser);
            $adminName = $this->fm->validation($adminName);
            $adminPwd = $this->fm->validation($adminPwd);
            $adminPwd_2 = $this->fm->validation($adminPwd_2);

            // $checkAdminUser = $this->fm->invalidFormat($adminUser);
            // $checkAdminName = $this->fm->invalidFormat($adminName);

            // if(!$checkAdminUser || !$checkAdminName ){
            //     header("location: ../view/pages/signup/signup.php?error=invalidFormat");
            //     exit();
            // }

            mysqli_real_escape_string($this->db->link,$adminUser);
            mysqli_real_escape_string($this->db->link,$adminName);
            mysqli_real_escape_string($this->db->link,$adminPwd);
            mysqli_real_escape_string($this->db->link,$adminPwd_2);

            if(empty($adminUser) || empty($adminPwd) || empty($adminPwd_2)){
                header("Location: ../view/pages/signup/signup.php?error=emptyinput");
            }
            else if($adminPwd != $adminPwd_2){
                header("Location: ../view/pages/signup/signup.php?error=pwddontmatch");
            }
            else {
                $query = "INSERT INTO admin (adminId, adminUser, adminName, adminPwd) VALUES (NULL,'$adminUser', '$adminName', '$adminPwd');";
                
                $result = $this->db->insert($query);
                if($result != false ){
                    
                    header("location: ../view/pages/signup/signup.php?error=none");
                }
                else{
                    header("location: ../view/pages/signup/signup.php?error=fail");
                }
            }
            
        }       
		public function login_customer($date)
		{
			$email =  $date['email'];
			$password = md5($date['password']);
			if($email == '' || $password == ''){
				$alert = "<span class='error'>Email And Password must be not empty</span>";
				return $alert;
			}else{
				$check_login = "SELECT * FROM customer WHERE email='$email' AND password='$password' ";
				$result_check = $this->db->select($check_login);
				if ($result_check != false) {
					$value = $result_check->fetch_assoc();
					Session::set('customer_login', true);
					Session::set('customer_id', $value['id']);
					Session::set('customer_name', $value['name']);
					header('Location:order.php');
				}else {
					$alert = "<span class='error'>Email or Password doesn't match</span>";
					return $alert;
				}
			}
		}
		public function show_customers($id)
		{
			$query = "SELECT * FROM customer WHERE id='$id' ";
			$result = $this->db->select($query);
			return $result;
		}
		public function show_user()
		{
			$query = "SELECT * FROM admin";
			$result = $this->db->select($query);
			return $result;
		}
		public function update_customers($data, $id)
		{
			$name = mysqli_real_escape_string($this->db->link, $data['name']);
			$zipcode = mysqli_real_escape_string($this->db->link, $data['zipcode']);
			$email = mysqli_real_escape_string($this->db->link, $data['email']);
			$address = mysqli_real_escape_string($this->db->link, $data['address']);
			$phone = mysqli_real_escape_string($this->db->link, $data['phone']);
			
			if($name=="" || $zipcode=="" || $email=="" || $address=="" || $phone ==""){
				$alert = "<span class='error'>Fields must be not empty</span>";
				return $alert;
			}else{
				$query = "UPDATE customer SET name='$name',zipcode='$zipcode',email='$email',address='$address',phone='$phone' WHERE id ='$id'";
				$result = $this->db->insert($query);
				if($result){
						$alert = "<span class='success'>Khách hàng Updated thành công</span>";
						return $alert;
				}else{
						$alert = "<span class='error'>Khách hàng Updated Not thành công</span>";
						return $alert;
				}
				
			}
		}
		public function search_admin()
		{
			$term = mysqli_real_escape_string($this->db->link,$_REQUEST['searchFnc']); 
			$query = 
			"SELECT * from admin
			 WHERE adminName like '%".$term."%' or adminUser like '%".$term."%'
			 order by adminID asc";

			// $query = "SELECT * FROM product order by productId desc ";
			$result = $this->db->select($query);
			return $result;
		}

	}
 ?>