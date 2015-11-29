<?php
/**
 * Plugin Name: WP debug
 * Plugin URI: https://github.com/blocknotes/wordpress_sql_query_exec
 * Description: WordPress debug plugin
 * Version: 1.0.1
 * Author: Mattia Roccoberton
 * Author URI: http://blocknot.es
 * License: GPL3
 */
define( 'WPD_VER', '1.0.1' );		// ! keep both versions number updated

class wp_debug_plugin
{
	function __construct()
	{
	// --- Actions --------------------------------------------------------- //
		add_action( 'admin_init', array( &$this, 'action_init' ) );
		add_action( 'admin_menu', array( &$this, 'action_menu' ) );
	}

	function action_init()
	{
		wp_register_style( 'wp-debug', plugins_url( 'admin-style.css', __FILE__ ) );
		wp_enqueue_style( 'wp-debug' );
		wp_enqueue_style( 'wpd_codemirror', plugin_dir_url( __FILE__ ) . 'libs/codemirror/codemirror.css' );
		wp_register_script( 'wpd_codemirror', plugin_dir_url( __FILE__ ) . 'libs/codemirror/codemirror.min.js', array() );
		wp_enqueue_script( 'wpd_codemirror' );
		wp_enqueue_script( 'wp-debug', plugins_url( 'admin-script.js', __FILE__ ), array() );
	}

	function action_menu()
	{
		add_management_page( 'Javascript Exec', 'DBG JS exec', 'manage_options', 'javascript-exec', array( &$this, 'page_js_exec' ) );
		add_management_page( 'PHP Exec', 'DBG PHP exec', 'manage_options', 'php-exec', array( &$this, 'page_php_exec' ) );
		add_management_page( 'SQL Query Exec', 'DBG SQL exec', 'manage_options', 'sql-query-exec', array( &$this, 'page_sql_exec' ) );
	}

	function page_js_exec()
	{
		if( !current_user_can( 'manage_options' ) ) wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		$wpd_js = isset( $_POST['wpd_js'] ) ? trim( stripslashes( $_POST['wpd_js'] ) ) : '';
?>
		<div class="wrap">
			<div id="wpd-credits"><a href="http://www.blocknot.es/home/me/" target="_blank"><img src="http://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate" /></a></div>
			<h2>Javascript Exec <small><?php echo ' &ndash; v', WPD_VER; ?></small></h2>
			<form method="post" id="form-wpd-js" name="form-wpd-js">
				<div>
					<label for="wpd-js">Javascript editor (press Shift+Enter to execute):</label><br/>
					<textarea id="wpd-js" name="wpd_js" autofocus="autofocus" cols="80" rows="10" onkeypress="Javascript:if(event.shiftKey&&event.keyCode===13){document.getElementById('form-wpd-js').submit();return false;}"><?php echo $wpd_js; ?></textarea>
				</div>
				<div style="margin-top: 10px">
					<input type="submit" class="button" value="Execute" style="font-weight: bold" />
				</div>
			</form>
		</div>
<?php
		if( !empty( $wpd_js ) ) echo "<script>\n", $wpd_js, "</script>\n";
	}

	function page_php_exec()
	{
		if( !current_user_can( 'manage_options' ) ) wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		$wpd_php = isset( $_POST['wpd_php'] ) ? trim( stripslashes( $_POST['wpd_php'] ) ) : '';
?>
		<div class="wrap">
			<div id="wpd-credits"><a href="http://www.blocknot.es/home/me/" target="_blank"><img src="http://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate" /></a></div>
			<h2>PHP Exec <small><?php echo ' &ndash; v', WPD_VER; ?></small></h2>
			<form method="post" id="form-wpd-php" name="form-wpd-php">
				<div>
					<label for="wpd-php">PHP editor (press Shift+Enter to execute):</label><br/>
					<textarea id="wpd-php" name="wpd_php" autofocus="autofocus" cols="80" rows="10" onkeypress="Javascript:if(event.shiftKey&&event.keyCode===13){document.getElementById('form-wpd-php').submit();return false;}"><?php echo $wpd_php; ?></textarea>
				</div>
				<div style="margin-top: 10px">
					<input type="submit" class="button" value="Execute" style="font-weight: bold" />
				</div>
			</form>
		</div>
<?php
		if( !empty( $wpd_php ) )
		{
			echo "<hr style=\"margin-top: 10px\"/>\n<pre id=\"wpd-php-output\">\n";
			if( eval( $wpd_php ) === FALSE ) echo '<i>Invalid command</i>';
			echo "</pre>\n";
		}
	}

	function page_sql_exec()
	{
		global $wpdb;
		if( !current_user_can( 'manage_options' ) ) wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		$wpd_cut = !isset( $_POST['wpd_cut'] ) ? TRUE : !empty( $_POST['wpd_cut'] );
		$wpd_cnt = isset( $_POST['wpd_last_cnt'] ) ? ( intval( $_POST['wpd_last_cnt'] ) > 1 ? 1 : 2 ) : 1;
		$wpd_q  = isset( $_POST['wpd_query'] ) ? $_POST['wpd_query'] : '';
		$wpd_q1 = isset( $_POST['wpd_last_query1'] ) ? $_POST['wpd_last_query1'] : '';
		$wpd_q2 = isset( $_POST['wpd_last_query2'] ) ? $_POST['wpd_last_query2'] : '';
?>
		<div class="wrap">
			<div id="wpd-credits"><a href="http://www.blocknot.es/home/me/" target="_blank"><img src="http://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate" /></a></div>
			<h2>SQL Query Exec <small><?php echo ' &ndash; v', WPD_VER; ?></small></h2>
			<div id="wpd-warning"><b>Warning:</b> manipulating the database can be dangerous, be careful. Do a backup before going on.<br />The author of this plugin is not responsible for the consequences of use of this software, no matter how awful, even if they arise from flaws in it.</div>
			<form method="post" id="form-wpd-query" name="form-wpd-query">
				<input type="hidden" name="wpd_show_tables" id="wpd-show-tables" value="0" />
				<input type="hidden" name="wpd_last_cnt" value="<?php echo $wpd_cnt; ?>" />
				<input type="hidden" name="wpd_last_query1" value="<?php echo htmlentities( stripslashes( ( $wpd_cnt == 1 ) ? $wpd_q : $wpd_q1 ) ); ?>" />
				<input type="hidden" name="wpd_last_query2" value="<?php echo htmlentities( stripslashes( ( $wpd_cnt == 2 ) ? $wpd_q : $wpd_q2 ) ); ?>" />
				<div>
					<label for="wpd-query">SQL editor (press Shift+Enter to execute):</label>
					<textarea id="wpd-query" name="wpd_query" autofocus="autofocus" cols="80" rows="3" onkeypress="Javascript:if(event.shiftKey&&event.keyCode===13){document.getElementById('form-wpd-query').submit();return false;}"><?php echo stripslashes( $wpd_q ); ?></textarea>
				</div>
				<div style="margin-top: 5px">
					<label for="wpd_prev_query">Previous query:</label>
					<input type="text" id="wpd-prev-query" readonly="readonly" value="<?php echo htmlentities( stripslashes( ( $wpd_cnt == 2 ) ? $wpd_q1 : $wpd_q2 ) ); ?>" />
				</div>
				<div style="margin-top: 10px">
					<label class="selectit"><input type="checkbox" id="wpd_cut" name="wpd_cut" <?php echo $wpd_cut ? 'checked="checked"' : ''; ?> />Cut long values (over 40 chars)</label> &nbsp; 
					<input type="button" class="button" value="Copy previous query" onclick="Javascript:document.getElementById('wpd-query').value=document.getElementById('wpd-prev-query').value;document.getElementById('wpd-query').focus();" /> &nbsp; 
					<input type="button" class="button" value="SHOW TABLES" onclick="Javascript:document.getElementById('wpd-show-tables').value=1;document.getElementById('form-wpd-query').submit();" /> &nbsp; 
					<input type="submit" class="button" value="Execute" style="font-weight: bold" />
				</div>
			</form>
<?php
		if( isset( $_POST['wpd_show_tables'] ) && $_POST['wpd_show_tables'] == '1' )
		{
			$result = $wpdb->query( 'SHOW TABLES' );
			if( $result !== FALSE )
			{
				echo "<hr />\n";
				$cnt = 0;
				$results = $wpdb->last_result;
				echo '<div style="text-align: center"><b>', count( $results ), "</b> tables</div>\n";
				echo '<div id="wpd-results-wrapper"><table id="wpd-results">', "\n";
				foreach( $results as $result )
				{
					$vars = get_object_vars( $result );
					//var_dump( current( $vars ) );
					$cnt++;
					echo '<tr class="', ( $cnt % 2 == 0 ) ? 'even' : 'odd', '">';
					//echo "<td class=\"c1\">$cnt</td>";
					echo '<td>&nbsp;<input type="button" class="button button-small" value="SELECT * FROM" onclick="Javascript:document.getElementById(\'wpd-query\').value=\'SELECT * FROM ', current( $vars ), '\';document.getElementById(\'wpd-query\').focus();" /> ', current( $vars ), '</td>';
					echo "</tr>\n";
				}
				echo "</table></div>\n";
				$wpdb->flush();
			}
			else
			{
				echo '<p id="wpd-message">Query error</p>';
				$wpdb->show_errors();
				$wpdb->print_error();
				$wpdb->hide_errors();
			}
		}
		else if( isset( $_POST['wpd_query'] ) && !empty( $_POST['wpd_query'] ) )
		{
			$result = $wpdb->query( stripslashes( $_POST['wpd_query'] ) );
			if( $result !== FALSE )
			{
				echo "<hr />\n";
				$cnt = 0;
				$results = $wpdb->last_result;
				if( !empty( $results ) )
				{
					echo '<div style="text-align: center"><b>', count( $results ), "</b> results</div>\n";
					echo '<div id="wpd-results-wrapper"><table id="wpd-results">', "\n";
					foreach( $results as $result )
					{
						$vars = get_object_vars( $result );
						if( $cnt == 0 )
						{
							echo '<tr><th class="c1">#</th>';
							foreach( $vars as $key => $value ) echo '<th>', $key, '</th>';
							echo "</tr>\n";
						}
						$cnt++;
						echo '<tr class="', ( $cnt % 2 == 0 ) ? 'even' : 'odd', '">';
						echo "<td class=\"c1\">$cnt</td>";
						foreach( $vars as $key => $value )
						{
							if( $wpd_cut )
							{
								if( strlen( $value ) < 40 ) $value_ = htmlentities( $value );
								else $value_ = htmlentities( substr( $value, 0, 40 ) ) . ' &hellip;';
							}
							else $value_ = htmlentities( $value );
							echo '<td>', $value_, '</td>';
						}
						echo "</tr>\n";
					}
					echo "</table></div>\n";
				}
				else echo '<div style="text-align: center"><b>No results', "</b></div>\n";
				$wpdb->flush();
			}
			else
			{
				echo '<p id="wpd-message">Query error</p>';
				$wpdb->show_errors();
				$wpdb->print_error();
				$wpdb->hide_errors();
			}
		}
		echo "</div>\n";
	}
}

if( is_admin() ) new wp_debug_plugin();
