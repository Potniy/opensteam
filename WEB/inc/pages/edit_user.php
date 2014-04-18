<?php

  if ( OSS_SuperAdmin() AND isset($_GET["id"]) AND (is_numeric($_GET["id"]) OR strstr( $_GET["id"], "STEAM_") ) ) {
    
	$pid = trim($_GET["id"]);

	if(!is_numeric($pid) AND strstr( $pid, "STEAM_") ) {
	
		  $sth = $db->prepare( "SELECT * FROM ".OSSDB_PLAYERS." WHERE steam=:steam LIMIT 1" );
		  $sth->bindValue(':steam', $pid, PDO::PARAM_STR); 
		  $result = $sth->execute();
	
		  $row = $sth->fetch(PDO::FETCH_ASSOC);
	      $pid = $row["id"];
	}
	 
    //Remove ban
	if(isset($_GET["rban"]) AND is_numeric($_GET["rban"])) {
	
	$banid = $_GET["rban"];
	$sth = $db->prepare( "DELETE FROM ".OSSDB_BANS." WHERE id=:id LIMIT 1" );
	$sth->bindValue(':id', $banid, PDO::PARAM_INT); 
	$result = $sth->execute();
	header("location: ".OSS_HOME."?option=edit_user&id=".$pid );
	die();
	}
	
	//Change user
	if ( isset($_POST["change_user"]) ) {
	
	   $rank = trim($_POST["rank"]);
	   $sth = $db->prepare( "UPDATE ".OSSDB_PLAYERS." SET rank=:rank WHERE id=:id LIMIT 1" );
	   $sth->bindValue(':rank', $rank, PDO::PARAM_STR); 
	   $sth->bindValue(':id',   $pid, PDO::PARAM_INT); 
	   $result = $sth->execute();
	   
	   //BAN USER
	   if(!empty($_POST["expire_date"])) {
	     
		  $expire = trim($_POST["expire_date"]);
		  $check = explode("-", $expire );
		  $checked = 1;
		 
		  if( isset($check[0]) AND strlen($check[0]) == 4 )  $checked++;    
		  if( isset($check[1]) AND strlen($check[1]) == 2 )  $checked++;
		  if( isset($check[2]) AND strstr($check[2], " ") AND strstr($check[2], ":") ) $checked++;
		  
		  if($checked>=4) {
		  
		  $sth = $db->prepare( "SELECT * FROM ".OSSDB_PLAYERS." WHERE id=:id LIMIT 1" );
		  $sth->bindValue(':id', $pid, PDO::PARAM_INT); 
		  $result = $sth->execute();
	
		  $row = $sth->fetch(PDO::FETCH_ASSOC);
		  
		  $datetime = date("Y-m-d H:i:s");
		  $admin = trim($_SESSION["name"]);
		  
		  $ins = $db->prepare( "INSERT INTO ".OSSDB_BANS."(steam, name, admin, bantime, expire) 
		  VALUES('".$row["steam"]."', '".$row["playerName"]."', '".$admin."', '".$datetime."', '".$expire."' ) ON DUPLICATE KEY UPDATE expire = '".$expire."', admin='".$admin."', name = '".$row["playerName"]."' " );
		  
		  $result = $ins->execute();
		  }
		  
	   } // END BAN USER
	   
	   header("location: ".OSS_HOME."?option=edit_user&id=".$pid );
	   die();
	}
  

    
	$sth = $db->prepare( "SELECT * FROM ".OSSDB_PLAYERS." WHERE id=:id LIMIT 1" );
	$sth->bindValue(':id', $pid, PDO::PARAM_INT); 
	$result = $sth->execute();
	
	$row = $sth->fetch(PDO::FETCH_ASSOC);

	$EditPlayer = array();
	$EditPlayer["id"]      = $row["id"];
	$EditPlayer["steamID"] = $row["steamID"];
	$EditPlayer["steam"] = $row["steam"];
	$EditPlayer["location"] = $row["location"];
	$EditPlayer["playerName"] = $row["playerName"];
	$EditPlayer["avatar_medium"] = $row["avatar_medium"];
	$EditPlayer["last_connection"] = date( OSS_DATE_FORMAT, strtotime($row["last_connection"]) );
	$EditPlayer["connections"] = $row["connections"];
	$EditPlayer["rank"] = $row["rank"];
	$EditPlayer["user_ip"] = $row["user_ip"];
	
	//Check ban
	$sth = $db->prepare( "SELECT * FROM ".OSSDB_BANS." WHERE steam=:steam AND (expire>=NOW() OR expire = '0000-00-00 00:00:00' ) LIMIT 1" );
	$sth->bindValue(':steam', $EditPlayer["steam"], PDO::PARAM_STR); 
	$result = $sth->execute();
	
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	
	if(!empty($row["name"])) {
	
	  $EditPlayer["banid"]   = $row["id"];
	  $EditPlayer["admin"]   = $row["admin"];
	  $EditPlayer["bantime"] = date( OSS_DATE_FORMAT, strtotime($row["bantime"]));
	  $EditPlayer["expire"]  = date( OSS_DATE_FORMAT, strtotime($row["expire"] ));
	  $EditPlayer["expire_date"]  = $row["expire"];
	}
  }
  

?>