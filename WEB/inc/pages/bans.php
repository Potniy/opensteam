<?php
    
	$PageTitle = 'Bans | OpenBans';
	
    $sql = "";
	
	if ( isset($_GET["search"]) AND strlen($_GET["search"])>=2 ) {
	
	  $search = strip_tags( trim($_GET["search"]) );
	  
	  $sql.=" AND b.name LIKE ('%".$search."%') ";
	}
	
    $sth = $db->prepare("SELECT COUNT(*) FROM ".OSSDB_BANS." as b WHERE b.id>=1 $sql LIMIT 1");
    $result = $sth->execute();
	
	 $r = $sth->fetch(PDO::FETCH_NUM);
	 $numrows = $r[0];
	 $result_per_page = $cfg["players_per_page"];
	 $draw_pagination = 0;
	 include('inc/pagination.php');
	 $draw_pagination = 1;
	 $SHOW_TOTALS = 1;
	 
	 $orderby  = " id DESC, expire ASC";
	 
	 if(isset($_GET["sort"])) {
	 
	   if($_GET["sort"] == "expire")    $orderby  = " expire DESC";
	   if($_GET["sort"] == "id")        $orderby  = " id ASC";
	   if($_GET["sort"] == "name")      $orderby  = " LOWER(name) ASC";
	   if($_GET["sort"] == "admin")     $orderby  = " LOWER(admin) ASC";
	 }
	 
	 $sth = $db->prepare("SELECT b.id, b.steam, b.name, b.admin, b.bantime, b.expire 
	 FROM ".OSSDB_BANS." as b 
	 WHERE b.id>=1 $sql 
	 ORDER BY $orderby 
	 LIMIT $offset, $rowsperpage");
	 $result = $sth->execute();
	 
	 $c=0;
	 
	 $BansData = array();
	 
	 while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
	 
	 $BansData[$c]["id"]       = ($row["id"]);
	 $BansData[$c]["steam"]    = ($row["steam"]);
	 $BansData[$c]["name"]     = ($row["name"]);
	 $BansData[$c]["admin"]    = ($row["admin"]);
	 $BansData[$c]["bantime"]  = date(OSS_DATE_FORMAT, strtotime($row["bantime"]) );
	 $BansData[$c]["expire"]   = date(OSS_DATE_FORMAT, strtotime($row["expire"]) );
	 $BansData[$c]["expire_date"]   = ( $row["expire"] ) ;
	 
	 
	 $BansData[$c]["num"]  = ($c+1);
	  
	 $c++;
	 }
?>