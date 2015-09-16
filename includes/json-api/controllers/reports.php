<?php
/*
Controller name: Reports
Controller description: Retrieving reports
*/

class JSON_API_Reports_Controller {
  public function create_report(){
		global $json_api;
  		$user_id = $_POST['user_id'];
		$title = $_POST['title'];
		$location = $_POST['lat'].", ".$_POST['lon'];
		$entities = $_POST['entities'];
		$categories = $_POST['categories'];
		$content = $_POST['description'];

		$post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$user_id,
				'post_name'		=>	$title,
				'post_title'		=>	$title,
				'post_content'		=>	$content,
				'post_status'		=>	'publish',
				'post_type'		=>	'report',
				'post_category' => array($categories),
				'tags_input' => array($entities)
			)
		);
		add_post_meta($post_id, 'report_location', $location, true);
		return array('report_id'=>$post_id);
  }
  public function insert_object(){
		global $json_api;
  		$user_id = $_POST['user_id'];
		$caption = $_POST['title'];
		
		$post_id = wp_insert_post(
			array(
				'comment_status'	=>	'closed',
				'ping_status'		=>	'closed',
				'post_author'		=>	$user_id,
				'post_name'		=>	$caption,
				'post_title'		=>	$caption,
				'post_status'		=>	'publish',
				'post_type'		=>	'story'
			)
		);
		
		add_post_meta($post_id, 'story_report_id', $report_id, true);
		
		//upload file
		$post_data = array(
      	'post_parent'  => $_POST['id'],
      	'post_title'   => $_POST['title'],
      	'post_content' => $_POST['content']
    	);

    	if (!empty($_FILES['attachment'])) {
    		$this->create_attachment($_FILES['attachment'], $post_id, $post_data);
		} else {
	      $json_api->error("Please attach a file to upload.");
	    }
		return array('object_id'=>$post_id);
  }
  
   public function create_attachment($file, $post_id, $post_data){
    
      include_once ABSPATH . '/wp-admin/includes/file.php';
      include_once ABSPATH . '/wp-admin/includes/media.php';
      include_once ABSPATH . '/wp-admin/includes/image.php';
      $attachment_id = media_handle_upload('attachment', $post_id, $post_data);
      unset($file);

      // set the media type as a meta
      update_post_meta($attachment_id, '_media_type', $_POST['media_type']);

      return array(
        'id' => $attachment_id
      );

  }
   
  public function get_reports() {
      global $json_api;
	
	  $posts = $json_api->introspector->get_posts(array(
	 // $posts = get_posts(array(
	    'post_type' => "report"
	  ));
		
	  $reports = array();
	  //Add stories
	  foreach($posts as $post){
	  	$post->stories = $this->get_stories($post->id);
		$reports[] = $post;
	  }
	  
	  return array(
	    'reports' => $reports
	  );
  }
  
  public function get_stories($report_id){
		global $json_api;
		//$stories = $json_api->introspector->get_posts(array(
		$stories = get_posts(array(
	    'post_type' => "story",
	    'meta_query' => array(
		 array(
			'key' => 'story_report_id',
			'value' => $report_id,
		 )
		)
	  ));
		return $stories;
  }
  
  public function get_sectors(){
  	global $json_api;
	$sectors = get_terms(array('report_sector'));
	return array('sectors'=>$sectors);
  }
  public function get_issues(){
  	global $json_api;
	$sectors = get_terms(array('report_issue'));
	return array('sectors'=>$sectors);
  }
  public function get_entities(){
  	global $json_api;
	$sectors = get_terms(array('report_entity'));
	return array('sectors'=>$sectors);
  }

}

?>
