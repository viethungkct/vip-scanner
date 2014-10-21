<?php

class VIPRestrictedCommandsCheck extends BaseCheck
{
	function check( $files ) {
		$result = true;

		$checks = array(
			// WordPress Classes
			"WP_User_Query" => array( 'level' => "Note", "note" => "Use of WP_User_Query" ),

			"update_post_caches" => array( "level" => "Note", "note" => "Post cache alteration" ),

			"update_option" => array( "level" => "Note", "note" => "Updating option" ),
			"get_option" 	=> array( "level" => "Note", "note" => "Getting option" ),
			"add_option" 	=> array( "level" => "Note", "note" => "Adding Option" ),
			"delete_option" => array( "level" => "Note", "note" => "Deleting Option" ),

			"wp_remote_get" => array( "level" => "Warning", "note" => "Uncached Remote operation, please use one of these functions: http://vip.wordpress.com/documentation/best-practices/fetching-remote-data/" ),
			"fetch_feed" 	=> array( "level" => "Warning", "note" => "Remote feed operation" ),

			"wp_schedule_event" 		=> array( "level" => "Warning", "note" => "WP Cron usage" ),
			"wp_schedule_single_event" 	=> array( "level" => "Warning", "note" => "WP Cron usage" ),
			"wp_clear_scheduled_hook" 	=> array( "level" => "Warning", "note" => "WP Cron usage" ),
			"wp_next_scheduled" 		=> array( "level" => "Warning", "note" => "WP Cron usage" ),
			"wp_unschedule_event" 		=> array( "level" => "Warning", "note" => "WP Cron usage" ),
			"wp_get_schedule" 			=> array( "level" => "Warning", "note" => "WP Cron usage" ),

			"add_feed" => array( "level" => "Warning", "note" => "Custom feed implementation" ),

			'query_posts' => array( 'level' => 'Blocker', 'note' => 'Rewriting the main loop. WP_Query or get_posts (with suppress_filters => false) might be better functions: http://developer.wordpress.com/2012/05/14/querying-posts-without-query_posts/' ),

			// Restricted multisite functions
			'switch_to_blog' 		=> array( 'level' => 'Blocker', 'note' => 'Switching blog context' ),
			'restore_current_blog' 	=> array( 'level' => 'Blocker', 'note' => 'Switching blog context' ),
			'ms_is_switched' 		=> array( 'level' => 'Blocker', 'note' => 'Querying blog context' ),
			'wp_get_sites' 			=> array( 'level' => 'Blocker', 'note' => 'Querying network sites' ),
			
			// Uncached functions
			'get_category_by_slug' 	=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or changed to wpcom_vip_get_category_by_slug()' ),
			'wp_get_post_categories' => array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or changed to get_the_terms() along with wp_list_pluck() see: http://vip.wordpress.com/documentation/uncached-functions/' ),
			'wp_get_post_tags' 		=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or changed to get_the_terms() along with wp_list_pluck() see: http://vip.wordpress.com/documentation/uncached-functions/' ),
			'get_cat_ID' 			=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or changed to wpcom_vip_get_term_by()' ),
			'get_term_by' 			=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or changed to wpcom_vip_get_term_by()' ),
			'get_page_by_title' 	=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or cached' ),
			'get_page_by_path' 		=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or changed to wpcom_vip_get_page_by_title()' ),
			'wp_get_object_terms' 	=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or cached' ),
			'wp_get_post_terms' 	=> array( 'level' => 'Warning', 'note' => 'Uncached function. Should be used on a limited basis or changed to get_the_terms() along with wp_list_pluck() see: http://vip.wordpress.com/documentation/uncached-functions/' ),
			'get_posts' 			=> array( 'level' => 'Warning', 'note' => 'Uncached function. Use WP_Query or ensure suppress_filters is false' ),
			'wp_get_recent_posts' 	=> array( 'level' => 'Warning', 'note' => 'Uncached function. Use WP_Query or ensure suppress_filters is false' ),

			// Object cache bypass
			"wpcom_uncached_get_post_meta" 		=> array( "level" => "Warning", "note" => "Bypassing object cache, please validate" ),
			"wpcom_uncached_get_post_by_meta" 	=> array( "level" => "Warning", "note" => "Bypassing object cache, please validate" ),

			// Functions that need to be used with care
			'wpcom_vip_load_custom_cdn' => array( 'level' => 'Blocker', 'note' => 'This should only be used if you have a CDN already set up.' ),

			// Role modifications
			"get_role" 		=> array( "level" => "Warning", "note" => "Role access; use helper functions http://lobby.vip.wordpress.com/best-practices/custom-user-roles/" ),
			"add_role" 		=> array( "level" => "Blocker", "note" => "Role modification; use helper functions http://lobby.vip.wordpress.com/best-practices/custom-user-roles/" ),
 			"remove_role" 	=> array( "level" => "Blocker", "note" => "Role modification; use helper functions http://lobby.vip.wordpress.com/best-practices/custom-user-roles/" ),
 			"add_cap" 		=> array( "level" => "Blocker", "note" => "Role modification; use helper functions http://lobby.vip.wordpress.com/best-practices/custom-user-roles/" ),
 			"remove_cap" 	=> array( "level" => "Blocker", "note" => "Role modification; use helper functions http://lobby.vip.wordpress.com/best-practices/custom-user-roles/" ),

 			// User meta
			"add_user_meta" 	=> array( "level" => "Blocker", "note" => "Using user meta, consider user_attributes http://vip.wordpress.com/documentation/user_meta-vs-user_attributes/" ),
			"delete_user_meta" 	=> array( "level" => "Blocker", "note" => "Using user meta, consider user_attributes http://vip.wordpress.com/documentation/user_meta-vs-user_attributes/" ),
			"get_user_meta" 	=> array( "level" => "Blocker", "note" => "Using user meta, consider user_attributes http://vip.wordpress.com/documentation/user_meta-vs-user_attributes/" ),
			"update_user_meta" 	=> array( "level" => "Blocker", "note" => "Using user meta, consider user_attributes http://vip.wordpress.com/documentation/user_meta-vs-user_attributes/" ),
			
			// debugging
			"error_log" 	=> array( "level" => "Blocker", "note" => "Filesystem operation" ),
			"var_dump" 		=> array( "level" => "Warning", "note" => "Unfiltered variable output" ),
			"print_r" 		=> array( "level" => "Warning", "note" => "Unfiltered variable output" ),
			"var_export" 	=> array( "level" => "Warning", "note" => "Unfiltered variable output" ),
			"wp_debug_backtrace_summary" => array( "level" => "Blocker", "note" => "Unfiltered filesystem information output" ),
			"debug_backtrace" => array( "level" => "Blocker", "note" => "Unfiltered filesystem information output" ),
			"debug_print_backtrace" => array( "level" => "Blocker", "note" => "Unfiltered filesystem information output" ),


			// other
			"date_default_timezone_set" => array( "level" => "Blocker", "note" => "Timezone manipulation" ),
			"error_reporting" 			=> array( "level" => "Blocker", "note" => "Settings alteration" ),
			"filter_input" 				=> array( "level" => "Warning", "note" => "Using filter_input(), use sanitize_* functions instead" ),
			'eval' 						=> array( 'level' => 'Blocker', "note" => "Meta programming" ),
			'create_function' 			=> array( 'level' => 'Blocker', "note" => "Using create_function, consider annonymous functions" ),
			'extract' 					=> array( 'level' => 'Blocker', "note" => "Explicitly define variables rather than using extract()" ),
			"ini_set" 					=> array( "level" => "Blocker", "note" => "Settings alteration" ),
			"switch_theme" 				=> array( "level" => "Blocker", "note" => "Switching theme programmatically is not allowed. Please make the update by hand after a deploy of your code" ),
			"wp_is_mobile" 				=> array( "level" => "Warning", "note" => "wp_is_mobile() is not batcache-friendly, please use <a href=\"http://vip.wordpress.com/documentation/mobile-theme/#targeting-mobile-visitors\">jetpack_is_mobile()</a>" ),

			// Restricted widgets
			"WP_Widget_Tag_Cloud" => array( "level" => "Warning", "note" => "Using WP_Widget_Tag_Cloud, use WPCOM_Tag_Cloud_Widget instead" ),

			// filesystem functions
			//"basename" => array( "level" => "Note", "note" => "Returns filename component of path" ),
			"chgrp" => array( "level" => "Blocker", "note" => "Changes file group" ),
			"chmod" => array( "level" => "Blocker", "note" => "Changes file mode" ),
			"chown" => array( "level" => "Blocker", "note" => "Changes file owner" ),

			"clearstatcache" 	=> array( "level" => "Blocker", "note" => "Clears file status cache" ),
			"set_file_buffer" 	=> array( "level" => "Warning", "note" => "Alias of stream_set_write_buffer" ),
			
			"copy" 			=> array( "level" => "Blocker", "note" => "Copies file" ),
			"curl_init" 	=> array( "level" => "Blocker", "note" => "cURL used. Should use WP_HTTP class or cached functions instead" ),
			"curl_setopt" 	=> array( "level" => "Blocker", "note" => "cURL used. Should use WP_HTTP class or cached functions instead" ),
 			"curl_exec" 	=> array( "level" => "Blocker", "note" => "cURL used. Should use WP_HTTP class or cached functions instead" ),
 			"curl_close" 	=> array( "level" => "Blocker", "note" => "cURL used. Should use WP_HTTP class or cached functions instead" ),
			"delete" 		=> array( "level" => "Blocker", "note" => "See unlink or unset" ),
			//"dirname" => array( "level" => "Warning", "note" => "Returns directory name component of path" ),
			"disk_free_space" 	=> array( "level" => "Blocker", "note" => "Returns available space in directory" ),
			"disk_total_space" 	=> array( "level" => "Blocker", "note" => "Returns the total size of a directory" ),
			"diskfreespace" 	=> array( "level" => "Blocker", "note" => "Alias of disk_free_space" ),
			
			"fclose" 	=> array( "level" => "Warning", "note" => "Closes an open file pointer" ),
			"feof" 		=> array( "level" => "Warning", "note" => "Tests for end-of-file on a file pointer" ),
			"fflush" 	=> array( "level" => "Blocker", "note" => "Flushes the output to a file" ),
			"fgetc" 	=> array( "level" => "Warning", "note" => "Gets character from file pointer" ),
			"fgetcsv" 	=> array( "level" => "Warning", "note" => "Gets line from file pointer and parse for CSV fields" ),
			"fgets" 	=> array( "level" => "Warning", "note" => "Gets line from file pointer" ),
			"fgetss" 	=> array( "level" => "Warning", "note" => "Gets line from file pointer and strip HTML tags" ),
			
			//"file_exists" => array( "level" => "Warning", "note" => "Checks whether a file or directory exists" ),
			"file_get_contents" => array( "level" => "Blocker", "note" => "Use wpcom_vip_file_get_contents() instead" ),
			"file_put_contents" => array( "level" => "Blocker", "note" => "Write a string to a file" ),
			
			"file" 		=> array( "level" => "Warning", "note" => "Reads entire file into an array" ),
			"fileatime" => array( "level" => "Blocker", "note" => "Gets last access time of file" ),
			"filectime" => array( "level" => "Blocker", "note" => "Gets inode change time of file" ),
			"filegroup" => array( "level" => "Blocker", "note" => "Gets file group" ),
			"fileinode" => array( "level" => "Blocker", "note" => "Gets file inode" ),
			"filemtime" => array( "level" => "Warning", "note" => "Gets file modification time" ),
			"fileowner" => array( "level" => "Blocker", "note" => "Gets file owner" ),
			"fileperms" => array( "level" => "Blocker", "note" => "Gets file permissions" ),
			"filesize" 	=> array( "level" => "Blocker", "note" => "Gets file size; should not be called on the front-end." ),
			"filetype" 	=> array( "level" => "Blocker", "note" => "Gets file type; should not be called on the front-end." ),
			"flock" 	=> array( "level" => "Blocker", "note" => "Portable advisory file locking" ),
			"fnmatch" 	=> array( "level" => "Blocker", "note" => "Match filename against a pattern" ),
			"fopen" 	=> array( "level" => "Blocker", "note" => "Opens file or URL" ),
			"fpassthru" => array( "level" => "Warning", "note" => "Output all remaining data on a file pointer" ),
			"fputcsv" 	=> array( "level" => "Blocker", "note" => "Format line as CSV and write to file pointer" ),
			"fputs" 	=> array( "level" => "Blocker", "note" => "Alias of fwrite" ),
			"fread" 	=> array( "level" => "Warning", "note" => "Binary-safe file read" ),
			"fscanf" 	=> array( "level" => "Warning", "note" => "Parses input from a file according to a format" ),
			"fseek" 	=> array( "level" => "Warning", "note" => "Seeks on a file pointer" ),
			"fstat" 	=> array( "level" => "Warning", "note" => "Gets information about a file using an open file pointer" ),
			"ftell" 	=> array( "level" => "Warning", "note" => "Returns the current position of the file read/write pointer" ),
			"ftruncate" => array( "level" => "Blocker", "note" => "Truncates a file to a given length" ),
			"fwrite" 	=> array( "level" => "Blocker", "note" => "Binary-safe file write" ),
			"glob" 		=> array( "level" => "Blocker", "note" => "Find pathnames matching a pattern" ),
			"is_dir" 	=> array( "level" => "Note", "note" => "Tells whether the filename is a directory" ),
			"is_file" 	=> array( "level" => "Note", "note" => "Tells whether the filename is a regular file" ),
			"is_link" 	=> array( "level" => "Note", "note" => "Tells whether the filename is a symbolic link" ),
			
			//"is_readable" => array( "level" => "Warning", "note" => "Tells whether the filename is readable" ),
			"is_executable" 		=> array( "level" => "Blocker", "note" => "Tells whether the filename is executable" ),
			"is_uploaded_file" 		=> array( "level" => "Warning", "note" => "Tells whether the file was uploaded via HTTP POST" ),
			"move_uploaded_file" 	=> array( "level" => "Blocker", "note" => "Moves an uploaded file to a new location" ),
			"is_writable" 			=> array( "level" => "Warning", "note" => "Tells whether the filename is writable" ),
			"is_writeable" 			=> array( "level" => "Warning", "note" => "Alias of is_writable" ),
			
			"parse_ini_file" 	=> array( "level" => "Warning", "note" => "Parse a configuration file" ),
			"parse_ini_string" 	=> array( "level" => "Warning", "note" => "Parse a configuration string" ),

			"lchgrp" 	=> array( "level" => "Blocker", "note" => "Changes group ownership of symlink" ),
			"lchown" 	=> array( "level" => "Blocker", "note" => "Changes user ownership of symlink" ),
			"link" 		=> array( "level" => "Blocker", "note" => "Create a hard link" ),
			"linkinfo" 	=> array( "level" => "Warning", "note" => "Gets information about a link" ),
			"lstat" 	=> array( "level" => "Warning", "note" => "Gives information about a file or symbolic link" ),
			"mkdir" 	=> array( "level" => "Blocker", "note" => "Makes directory" ),
			"pathinfo" 	=> array( "level" => "Warning", "note" => "Returns information about a file path" ),
			"pclose" 	=> array( "level" => "Blocker", "note" => "Closes process file pointer" ),
			"popen" 	=> array( "level" => "Blocker", "note" => "Opens process file pointer" ),
			"readfile" 	=> array( "level" => "Warning", "note" => "Outputs a file" ),
			"readlink" 	=> array( "level" => "Warning", "note" => "Returns the target of a symbolic link" ),
			"realpath" 	=> array( "level" => "Warning", "note" => "Returns canonicalized absolute pathname" ),
			"rename" 	=> array( "level" => "Blocker", "note" => "Renames a file or directory" ),
			"rewind" 	=> array( "level" => "Warning", "note" => "Rewind the position of a file pointer" ),
			"rmdir" 	=> array( "level" => "Blocker", "note" => "Removes directory" ),

			"stat" 		=> array( "level" => "Warning", "note" => "Gives information about a file" ),
			"symlink" 	=> array( "level" => "Blocker", "note" => "Creates a symbolic link" ),
			"tempnam" 	=> array( "level" => "Warning", "note" => "Create file with unique file name" ),
			"tmpfile" 	=> array( "level" => "Blocker", "note" => "Creates a temporary file" ),
			"touch" 	=> array( "level" => "Blocker", "note" => "Sets access and modification time of file" ),
			"umask" 	=> array( "level" => "Blocker", "note" => "Changes the current umask" ),
			"unlink" 	=> array( "level" => "Blocker", "note" => "Deletes a file" ),

			// process control functions
			"pcntl_alarm" 			=> array( "level" => "Blocker", "note" => "Set an alarm clock for delivery of a signal" ),
			"pcntl_exec" 			=> array( "level" => "Blocker", "note" => "Executes specified program in current process space" ),
			"pcntl_fork" 			=> array( "level" => "Blocker", "note" => "Forks the currently running process" ),
			"pcntl_getpriority" 	=> array( "level" => "Blocker", "note" => "Get the priority of any process" ),
			"pcntl_setpriority" 	=> array( "level" => "Blocker", "note" => "Change the priority of any process" ),
			"pcntl_signal_dispatch" => array( "level" => "Blocker", "note" => "Calls signal handlers for pending signals" ),
			"pcntl_signal" 			=> array( "level" => "Blocker", "note" => "Installs a signal handler" ),
			"pcntl_sigprocmask" 	=> array( "level" => "Blocker", "note" => "Sets and retrieves blocked signals" ),
			"pcntl_sigtimedwait" 	=> array( "level" => "Blocker", "note" => "Waits for signals, with a timeout" ),
			"pcntl_sigwaitinfo" 	=> array( "level" => "Blocker", "note" => "Waits for signals" ),
			"pcntl_wait" 			=> array( "level" => "Blocker", "note" => "Waits on or returns the status of a forked child" ),
			"pcntl_waitpid" 		=> array( "level" => "Blocker", "note" => "Waits on or returns the status of a forked child" ),
			"pcntl_wexitstatus" 	=> array( "level" => "Blocker", "note" => "Returns the return code of a terminated child" ),
			"pcntl_wifexited" 		=> array( "level" => "Blocker", "note" => "Checks if status code represents a normal exit" ),
			"pcntl_wifsignaled" 	=> array( "level" => "Blocker", "note" => "Checks whether the status code represents a termination due to a signal" ),
			"pcntl_wifstopped" 		=> array( "level" => "Blocker", "note" => "Checks whether the child process is currently stopped" ),
			"pcntl_wstopsig" 		=> array( "level" => "Blocker", "note" => "Returns the signal which caused the child to stop" ),
			"pcntl_wtermsig" 		=> array( "level" => "Blocker", "note" => "Returns the signal which caused the child to terminate" ),

			// session functions http://php.net/manual/en/ref.session.php
			"session_cache_expire" 		=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_cache_limiter" 	=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_commit" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_decode" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_destroy" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_encode" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_get_cookie_params" => array( "level" => "Blocker", "note" => "Using session function" ),
			"session_id" 				=> array( "level" => "Blocker", "note" => "Using session function" ),
			"ssession_is_registered" 	=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_module_name" 		=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_name" 				=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_regenerate_id" 	=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_register_shutdown" => array( "level" => "Blocker", "note" => "Using session function" ),
			"session_register" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_save_path" 		=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_set_cookie_params" => array( "level" => "Blocker", "note" => "Using session function" ),
			"session_set_save_handler" 	=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_start" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_status" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_unregister" 		=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_unset" 			=> array( "level" => "Blocker", "note" => "Using session function" ),
			"session_write_close" 		=> array( "level" => "Blocker", "note" => "Using session function" ),

			// direct mysql usage
			"mysql_affected_rows" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_client_encoding" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_close" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_connect" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_create_db" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_data_seek" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_db_name" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_db_query" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_drop_db" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_errno" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_error" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_escape_string" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_fetch_array" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_fetch_assoc" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_fetch_field" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_fetch_lengths" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_fetch_object" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_fetch_row" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_field_flags" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_field_len" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_field_name" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_field_seek" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_field_table" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_field_type" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_free_result" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_get_client_info" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_get_host_info" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_get_proto_info" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_get_server_info" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_info" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_insert_id" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_list_dbs" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_list_fields" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_list_processes" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_list_tables" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_num_fields" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_num_rows" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_pconnect" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_ping" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_query" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_real_escape_string"	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_result" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_select_db" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_set_charset" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_stat" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_tablename" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_thread_id" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysql_unbuffered_query" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),

			// mysqli http://www.php.net/manual/en/mysqli.summary.php
			"mysqli" 							=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_affected_rows" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_client_info" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_client_version" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_connect_errno" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_connect_error" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_errno" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_error" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_field_count" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_host_info" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_proto_info" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_server_info" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_server_version" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_info" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_insert_id" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_sqlstate" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_warning_count" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_autocommit" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_change_user" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_character_set_name" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_client_encoding" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_close" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_commit" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_connect" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_debug" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_dump_debug_info" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_charset" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_connection_stats"		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_client_stats" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_cache_stats" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_warnings" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_init" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_kill" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_more_results" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_multi_query" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_next_result" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_options" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_set_opt()" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_ping" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_prepare" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_query" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_real_connect" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_real_escape_string" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_escape_string" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_real_query" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_refresh" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_rollback" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_select_db" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_set_charset" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_set_local_infile_default"	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_set_local_infile_handler" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_ssl_set" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stat" 						=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_init" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_store_result" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_thread_id" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_thread_safe" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_use_result" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),

			// mysqli statements
			"mysqli_stmt_affected_rows" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_errno" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_error" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_field_count" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_insert_id" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_num_rows" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_param_count" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_param_count" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_sqlstate" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_attr_get" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_attr_set" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_bind_param" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_bind_param" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_bind_result" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_bind_result" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_close" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_data_seek" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_execute" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_execute" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_fetch" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch" 					=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_free_result" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_get_result" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_get_warnings" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_more_results" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_next_result" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_prepare" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_reset" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_result_metadata" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_get_metadata" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_send_long_data" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_stmt_store_result" 		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_field_tell" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_num_fields" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_lengths" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_num_rows" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_data_seek" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_all" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_array" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_assoc" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_field_direct"		=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_field" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_fields" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_object" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_fetch_row" 				=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_field_seek" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_free_result" 			=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_embedded_server_end" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),
			"mysqli_embedded_server_start" 	=> array( "level" => "Blocker", "note" => "Direct MySQL usage, use WP APIs instead" ),

			// XML Entity Loader mods
			'libxml_set_external_entity_loader' => array( 'level' => 'Blocker', 'note' => 'Modifying the XML entity loader is disabled for security reasons.' ),
		);

		foreach ( $this->filter_files( $files, 'php' ) as $file_path => $file_content ) {
			foreach ( $checks as $check => $check_info ) {
				$this->increment_check_count();

				if ( strpos( $file_content, $check ) !== false ) {
					$pattern = "/\s+($check)+\s?\(+/msiU";
					
					if ( preg_match( $pattern, $file_content, $matches ) ) {
						$filename = $this->get_filename( $file_path );

						$lines = $this->grep_content( rtrim( $matches[0], '(' ), $file_content );

						$this->add_error(
							$check,
							$check_info['note'],
							$check_info['level'],
							$filename,
							$lines
						);

						$result = false;
					}
				}
			}
		}

		return $result;
	}
}
