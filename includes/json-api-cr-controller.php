<?php
/*
Controller name: Citizen Reporter
Controller description:Additional functionaility to the JSON_API controller for the Citizen Reporter app integration
*/
class JSON_API_CR_Controller {


    public function confirm_payment(){

        $post_id = $_POST['post_id'];
        $confirm = $_POST['confirm'];
        $payment_post_id = $_POST['remote_id'];

        confirm_payment($post_id, $payment_post_id, $confirm);

    }
    public function submit_feedback(){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $feedback = $_POST['feedback'];
        $os_version = $_POST['os_version'];
        $model = $_POST['model'];

        if($username != ""){
            $username = "admin";
        }

        $feedback_post_id = wp_insert_post(
            array(
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_author' => $username,
                'post_title' => $feedback,
                'post_status' => 'draft',
                'post_type' => 'feedback'
            )
        );

        update_post_meta($feedback_post_id, 'email', $email);
        update_post_meta($feedback_post_id, 'os_version', $os_version);
        update_post_meta($feedback_post_id, 'model', $model);

        $message = $email."<br/>".$feedback."<br/>".$os_version.$model;

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
        $headers .= 'Cc: nick@codeforafrica.org' . "\r\n";

        mail("support@codeforafrica.org","New Feedback on CitizenReporter",$message,$headers);

        return array("result"=>"OK", "message"=>"Feedback submitted!");

    }
    public function edit_user_device(){
        $username = $_POST['username'];
        $user = get_user_by( "login", $username );
        $user_id = $user->ID;
        $device_id = $_POST['regId'];

        update_user_meta($user_id, 'gcm_id', $device_id);

        return array("result"=>"OK", "message"=>"Device registered successfully!");

    }

    public function editprofile(){

        $username = $_POST['username'];
        $user = get_user_by( "login", $username );
        $user_id = $user->ID;

        if(isset($_POST['email'])){

            $email_address = $_POST['email'];
        }
        if(isset($_POST['password']))
        {
            $password = $_POST['password'];
        }

        if(isset($_POST['email']))
        {
            wp_update_user( array ('ID' => $user_id, 'user_email' => $email_address) ) ;
        }
        if(isset($_POST['password'])){
            wp_update_user( array ('ID' => $user_id, 'user_pass' => $password) ) ;
        }

        update_user_meta($user_id, 'address', $_POST['address']);
        update_user_meta($user_id, 'location', $_POST['location']);
        update_user_meta($user_id, 'phone_number', $_POST['phone_number']);
        update_user_meta($user_id, 'first_name', $_POST['first_name']);
        update_user_meta($user_id, 'last_name', $_POST['last_name']);

        return array("result"=>"OK", "message"=>"Profile updated successfully!");
    }

    public function register(){
        global $json_api;

        $username = $_POST['username'];
        $password = $_POST['password'];
        $email_address = $_POST['email'];

        //$phone_number = $_POST['phone_number'];
        //$location = $_POST['location'];
        //$firstname = $_POST['firstname'];
        //$lastname = $_POST['lastname'];
        $key = $_POST['key'];
        if(!isset($username)||(!$password)){
            return array("result"=>"NOK", "message"=>"missing required fields!");
        }

        if( null == username_exists( $username ) ) {

            // Generate the password and create the user
            //$password = wp_generate_password( 12, false );
            $user_id = wp_create_user( $username, $password, $email_address );
            if(isset($_POST['operatorName'])){
                wp_update_user( array ('ID' => $user_id, 'operatorName' => $_POST['operatorName']) ) ;
            }
            if(isset($_POST['deviceId'])){
                wp_update_user( array ('ID' => $user_id, 'deviceId' => $_POST['deviceId']) ) ;
            }
            if(isset($_POST['serialNumber'])){
                wp_update_user( array ('ID' => $user_id, 'serialNumber' => $_POST['serialNumber']) ) ;
            }
            // Set the nickname
            wp_update_user(
                array(
                    'ID'          =>    $user_id,
                    'nickname'    =>    $email_address,
                )
            );

            // Set the role
            $user = new WP_User( $user_id );
            $user->set_role( 'author' );
            // Add meta
            //update_user_meta($user_id, 'location', $_POST['location']);
            //update_user_meta($user_id, 'phone_number', $_POST['phone_number']);
            //update_user_meta($user_id, 'first_name', $_POST['first_name']);
            //update_user_meta($user_id, 'last_name', $_POST['last_name']);
            return array("result"=>"OK", "message"=>"Registration successfull!", "user_id"=>$user_id);
        }else{
            return array("result"=>"NOK", "message"=>"User already exists!");

        }
    }
    public function user(){
        global $json_api;
        $username = $_POST['username'];
        //	$token = $_POST['token'];
        //	$key = $_POST['key'];
//	   if($key!=get_site_option('api_key')){
//	      return array("result"=>"NOK", "message"=>"Incorrect API key!");}
//	   else{
        $user = get_user_by( "login", $username );
        $user_id = $user->ID;
        $p = array();
        $p['username'] = get_userdata($user_id)->user_login;
        $p['password'] = get_user_meta($user_id, 'password', TRUE);
        $p['email'] = get_userdata($user_id)->user_email;;
        $p['first_name'] = get_user_meta($user_id, 'first_name', TRUE);
        $p['last_name'] = get_user_meta($user_id, 'last_name', TRUE);
        $p['phone_number'] = get_user_meta($user_id, 'phone_number', TRUE);
        $p['location'] = get_user_meta($user_id, 'location', TRUE);
        $p['address'] = get_user_meta($user_id, 'address', TRUE);

        return array("result"=>"OK", "user"=>$p);
//	  }
    }

    public function send_message(){

        $recipient = 1;
        if(isset($_POST['recipient']))
            $recipient = $_POST['recipient'];

        if(isset($_POST['author'])){
            $author = $_POST['author'];
        }else{
            //if sent username instead
            if(isset($_POST['username'])){

                $author_data = get_user_by('login', $_POST['username']);
                $author = $author_data->ID;

            }else{
                return array("result"=>"NOK", "message"=>"Missing parameters");
            }
        }

        send_message($author, $_POST['message_string'], $recipient);

        return array("result"=>"OK", "message"=>"Message sent!");
    }

}