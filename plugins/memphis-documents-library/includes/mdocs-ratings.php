<?php
function mdocs_ratings() {
	if(isset($_POST['type']) && $_POST['type'] == 'rating') {
		$mdocs = get_option('mdocs-list');
		$found = false;
		$sa =  mdocs_get_table_atts();
		foreach($mdocs as $index => $the_mdoc) {
			if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
				if($sa['show-rating']['show']) {
					$the_rating = mdocs_get_rating($the_mdoc);
					if($the_rating['your_rating'] == 0) $text = __("Rate Me!");
					else $text = __("Your Rating");
					echo '<div class="mdocs-rating-container">';
					echo '<h1>'.$the_mdoc['name'].'</h1>';
					echo '<div class="mdocs-ratings-stars" data-my-rating="'.$the_rating['your_rating'].'">';
					echo '<p>'.$text,'</p>';
					for($i=1;$i<=5;$i++) {
						if($the_rating['average'] >= $i) echo '<i class="fas fa-star fa-5x mdocs-gold  mdocs-my-rating" id="'.$i.'" aria-hidden="true"></i>';
						elseif(ceil($the_rating['average']) == $i ) echo '<i class="fas fa-star-half-alt fa-5x mdocs-gold mdocs-my-rating" id="'.$i.'" aria-hidden="true"></i>';
						else echo '<i class="far fa-star fa-5x mdocs-my-rating" id="'.$i.'" aria-hidden="true"></i>';
					}
					echo '</div>';
					echo '</div>';
				} else _e('Ratings functionality is off.','memphis-documents-library');
				$found = true;
				break;
			}
		}
	}
}
function mdocs_get_rating($the_mdoc) {
	global $current_user;
	$avg = 0;
	$the_rating = array('total'=>0,'your_rating'=>0);
	if(is_array($the_mdoc['ratings']) && count($the_mdoc['ratings']) > 0 ) {
		foreach($the_mdoc['ratings'] as $index => $average) {
			if($index === 0) {
				unset($the_mdoc['ratings'][$index]);
			} else {
				$avg += $average;
				$the_rating['total']++;
				if($current_user->user_email === $index) $the_rating['your_rating'] = floatval($average);
			}
		}
		$the_rating['average'] =  floatval(number_format($avg/$the_rating['total'],1));
		return $the_rating;
	} else {
		$the_rating['total'] = 0;
		$the_rating['average'] = '-';
		return $the_rating;
	}
	
}
function mdocs_set_rating($the_id) {
	global $current_user;
	$avg = 0;
	if(isset($_GET['mdocs-rating'])) $the_rating = mdocs_sanitize_string($_GET['mdocs-rating']);
	elseif(isset($_POST['mdocs-rating'])) $the_rating = intval($_POST['mdocs-rating']);
	$mdocs = get_option('mdocs-list');
	foreach($mdocs as $index => $doc) if($doc['id'] == $the_id) $doc_index = $index;
	$mdocs[$doc_index]['ratings'][$current_user->user_email] = $the_rating;
	foreach($mdocs[$doc_index]['ratings'] as $index => $rating) $avg += $rating;
	$mdocs[$doc_index]['rating'] = floatval(number_format($avg/count($mdocs[$doc_index]['ratings']),1));
	mdocs_save_list($mdocs);
	$_POST['type'] = 'rating';
	
	//$test = mdocs_get_file_by($_REQUEST['mdocs_file_id'],'id');
	//var_dump($test);
	//var_dump($_REQUEST);
	//mdocs_the_list();
	//mdocs_ratings();
	
}
?>