<?php 

	function my_database_admin_role_based_access(){
		echo '<div>';
			if(isset($_POST['my_database_admin_role_based_access_view'])){
				if(current_user_can('manage_options')  && check_admin_referer( 'my_database_admin_role_based_access', 'my_database_admin_role_based_access_nonce')) {
					
					global $wp_roles;
					foreach($wp_roles->roles as $key=>$value ) {
						$role_key = 'mda_role_'.$key;
						if(isset($_POST[$role_key]))
							update_option($role_key, true);
						else
							update_option($role_key, false);
					}
					
					echo "<div style='max-width:60%;color:black;margin:4px;background: #cdfbaa;padding: 5px 20px;border: 1px solid #b8d6a1;'>Role based access settings has been updated.</b></div>";
				} else {
					echo "<div style='max-width:60%;color:black;margin:4px;background:#ffc3c3;padding: 5px 20px;border: 1px solid #ff8c8c;'>You don't have permission to perform this operation.</b></div>";
				}
			}
			
		my_database_admin_role_based_access_view();
		echo '</div>';
		
	}
	
	
	function my_database_admin_role_based_access_view() {
		echo '<div style="margin:4px;padding:10px 40px;max-width:750px">
		<h3><br>Role Based Access</h3><h4>Allow access to other roles than Administrator</h4><br>
		<form  method="POST" action="">';
			wp_nonce_field('my_database_admin_role_based_access', 'my_database_admin_role_based_access_nonce');
		echo '<input type="hidden" name="my_database_admin_role_based_access_view" value="1"/> ';
		
		global $wp_roles;
		foreach($wp_roles->roles as $key=>$value ) {
			if($key!='administrator') {
				echo '<input type="checkbox" ';
				if(get_option("mda_role_".$key)) echo 'checked'; 
				echo ' name="mda_role_'.$key.'"> '.ucwords($key).'<br><br>';
			}
		}
		echo '<br><input type="submit" class="button button-primary button-large" value="Save Settings"></form></div>';
	}
	
	
?>