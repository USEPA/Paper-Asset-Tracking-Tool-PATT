<?php 


	function my_database_admin_admin(){
		
		$mydatabase = "";
		$selectqueryresults = false;
		$current_user_id = get_current_user_id();
		if(get_user_meta($current_user_id, 'my_database_admin_active_db', true))
			$mydatabase = get_user_meta($current_user_id, 'my_database_admin_active_db', true);
		
		echo '<div>';
		
			$current_user_can_manage_options = false;
			if ( current_user_can( 'manage_options' )) {
				$current_user_can_manage_options = true;
			} else {
				$user = wp_get_current_user();
				foreach($user->roles as $role) {
					if(get_option("mda_role_".$role)) {
						$current_user_can_manage_options = true;
						break;
					}
				}
				
			}
		
			if($current_user_can_manage_options) {
				
				if(isset($_POST['my_database_admin_selectdb']) && isset($_POST['databasename'])){
					if( check_admin_referer( 'my_database_admin_dashboard_selectdb', 'my_database_admin_dashboard_selectdb_nonce') ) {
						$mydatabase = sanitize_text_field($_POST['databasename']);
						update_user_meta($current_user_id, 'my_database_admin_active_db', $mydatabase);
						echo "<div style='max-width:60%;color:black;margin:4px;background: #cdfbaa;padding: 5px 20px;border: 1px solid #b8d6a1;'>Database <b>".$mydatabase."</b> is active now.</div>";
					}
				} else if(isset($_POST['my_database_admin_resetdb'])){
					if( check_admin_referer( 'my_database_admin_dashboard_resetdb', 'my_database_admin_dashboard_resetdb_nonce') ) {
						$mydatabase = "";
						delete_user_meta($current_user_id, 'my_database_admin_active_db');
						echo "<div style='max-width:60%;color:black;margin:4px;background: #cdfbaa;padding: 5px 20px;border: 1px solid #b8d6a1;'>No database is active now. Select database you want to use.</div>";
					}
				} else if(isset($_POST['my_database_admin_run_query']) && isset($_POST['query'])){
					if( check_admin_referer( 'my_database_admin_dashboard_runquery', 'my_database_admin_dashboard_runquery_nonce') ) {
						$sql = sanitize_text_field($_POST['query']);
						$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						$sql = stripcslashes($sql);
						$result = $conn->query($sql);
						if(gettype($result) == 'boolean') {
							echo "<div style='max-width:60%;color:black;margin:4px;background: #cdfbaa;padding: 5px 20px;border: 1px solid #b8d6a1;'>Success.</div>";
						} else if($result == false) {
							echo "<div style='max-width:60%;color:black;margin:4px;background:#ffc3c3;padding: 5px 20px;border: 1px solid #ff8c8c;'><b>Error occured:</b> ". mysqli_error($conn)."</b></div>";
						} else {
							$selectqueryresults = $result;
						}
					}
				}
				
			}
			
		my_database_admin_dashboard($mydatabase, $selectqueryresults);
		
		echo '</div>';
		
	}
	
	
	function my_database_admin_dashboard($mydatabase, $selectqueryresults){
		
		echo '<div style="margin:4px;padding:10px 40px;max-width:950px">';
		echo '<a href="https://wordpress.org/support/plugin/my-database-admin" target="_blank"><button style="float:right;background: #58c551;color: #fff;padding: 5px;font-size: 14px;">Request new feature or give feedback</button></a>';
		echo '<img src="'.plugin_dir_url( __FILE__ ).'../../images/logo.png"><hr>';
		
		if(empty($mydatabase)) {
			
			echo "<h3>Select database</h3>";
			echo "<form  method='POST' id='selectdbform'>";
			wp_nonce_field('my_database_admin_dashboard_selectdb', 'my_database_admin_dashboard_selectdb_nonce');
			echo "<input type='hidden' name='my_database_admin_selectdb' value='1'/>
				<input type='hidden' name='databasename' id='databasename' value=''>
			</form>";
			echo '<script>function selectdb(databasename) { 
				document.getElementById("databasename").value = databasename;
				document.getElementById("selectdbform").submit(); 
			}</script>';
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if ($conn->connect_error)
				die("Connection failed: " . $conn->connect_error);
			
			$result = $conn->query("SHOW DATABASES");
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					if(isset($row['Database']))
					echo "<p><a href='#' onclick='selectdb(\"".$row['Database']."\")'>".$row['Database']. "</a></p>";
				}
			}

		} else {
			
			echo '<script>function selecttable(tablename) { 
				document.getElementById("runquery").value += " " + tablename;
			}
			</script>';
			
			echo "<form  method='POST' id='resetdbform'>";
			wp_nonce_field('my_database_admin_dashboard_resetdb', 'my_database_admin_dashboard_resetdb_nonce');
			echo "<input type='hidden' name='my_database_admin_resetdb' value='1'/>
			</form>";
			echo '<script>function resetdb() { 
				document.getElementById("resetdbform").submit(); 
			}</script>';
			
			$query = 'SELECT * FROM ';
			if(isset($_POST['query'])) {
				$query = sanitize_text_field($_POST['query']);
				$query = stripcslashes($query);
			}
			
			echo "<p style='font-size:20px'><a href='#' onclick=resetdb()>".DB_HOST."</a> > <span>".$mydatabase."</span></p><p><a href='#' onclick=resetdb()>Select Other Database</a></p><br>";
			
			if($selectqueryresults) {
				echo "<hr><h2>Results:</h2>";
				my_database_admin_show_result($selectqueryresults);
			}
			
			echo '<hr><table class="my_database_admin_querytable"><tr><td valign="top" style="width:35%"><h4>Tables:</h4>';
			
			$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if (!$conn->connect_error) {
				$result = $conn->query("SHOW TABLES FROM ".$mydatabase);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						foreach($row as $key => $value) {
							echo "<p><a href='#' onclick='selecttable(\"".$value."\")'>".$value. "</a></p>";
							break;
						}
					}
				}
			}
			

			echo '</td><td valign="top" style="width:65%">';
			echo '<form method="POST" action="">';
			wp_nonce_field('my_database_admin_dashboard_runquery', 'my_database_admin_dashboard_runquery_nonce');
			echo '<input type="hidden" name="my_database_admin_run_query" value="1"/>
			<textarea rows="8" cols="120" required="true"  name="query" id="runquery" placeholder="SELECT * from wp_options" value="" style="min-width:250px">'.$query.'</textarea><br><br>
			<input type="submit" class="button button-primary button-large" value="Execute">
			</form>';
			echo '</td></td></table>';
		}
		

		
		echo '</div>';
		
	}
	
	function my_database_admin_show_result($result){
		
		$displayheading = true;
		if ($result->num_rows > 0) {
				echo "<table class=my-database-admin-table>";
				while($row = $result->fetch_assoc()) {
					if(is_array($row)) {
						echo "<tr>";
						if($displayheading)
						foreach($row as $key => $value) {
							echo "<th>".$key."</th>";
						}
						$displayheading = false;
						echo "</tr>";
						
						echo "<tr>";
						foreach($row as $key => $value) {
							echo "<td>".$value."</td>";
						}
						echo "</tr>";
					}
				}
				echo "</table>";
			} else
				echo "No records found.";
	}

?>