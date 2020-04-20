<?php
/*
#===================================================================
# Názov: Web interface pre Administrátorov požiadaviek pre notifikácie
# Autor: KillerXCoder (Peter Federl)
# E-Mail: peter.federl@gmail.com
#===================================================================
*/
date_default_timezone_set ("Europe/Bratislava");
$servername = '';
$username = '';
$password = '';
$dbname = '';
$conn = new mysqli($servername, $username, $password, $dbname);
$conn -> set_charset("utf8");
$sql = "SELECT * FROM midasvote ORDER BY id DESC";
$result = $conn->query($sql);
if (isset($_GET['strana'])) {
$strana = $_GET['strana'];
} else {
	$strana = 1;
}
$pocet_na_stranu = 10;
$offset = ($strana-1) * $pocet_na_stranu; 
$celkovo_stranky = ceil($result->num_rows / $pocet_na_stranu);
$celkovo_bany = $result->num_rows;


$sql = "SELECT * FROM midasvote ORDER BY id DESC LIMIT ".$offset.",". $pocet_na_stranu;
$result = $conn->query($sql);


echo '<style>

.padd th, .padd td { padding: 10px 10px; vertical-align: middle }
.lh { line-height: 24px; }

.color tr:nth-child(even) { background: #ebebeb; }
.color tr:nth-child(odd) { background: #FFF; border: none;}

.mnu th { padding: 0; color: white; transition: 0.25s ease-out; background: #2A2A2A; border: none;  vertical-align: middle }
.mnu th:first-child { border-right: 1px solid rgba(255, 255, 255, 0.1); }
.mnu th:hover { background: #E64946 }
.mnu a, .mnu a:hover { padding: 10px 10px; color: white; text-decoration: none; display: block; height: 100% width: 100%; }

</style>';
echo '
<style>
.pagination {

}

.pagination a {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
  transition: background-color .3s;
  border: 1px solid #ddd;
}

.pagination a.active {
  background-color: #4CAF50;
  color: white;
  border: 1px solid #4CAF50;
 }
.hladat{
  width: 20%;
  padding: 10px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}
.potvrdit{
  width: 10%;
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
.pravidla{

  width: 10%;
  background-color: #303030;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  color:white !IMPORTANT;
  text-decoration:none !IMPORTANT;
}
.pravidla:hover{

  width: 10%;
  background-color: #ff0000;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  color:white !IMPORTANT;
  text-decoration:none !IMPORTANT;
}

@media all and (max-width: 694px) {
	.pravidla{

	display:block;
	margin:0px;
	width: 90%;
	}
	
	.pravidla:hover{

	background-color: #ff0000;
	display:block;
	margin:0px;
	width: 90%;

	}
}

</style>';
echo "<h1 style='text-align:center;'>MidasVote</h1><br>";
echo "<br><p style='text-align:center;'>Celkový počet záznamov: ";
echo "<h2 style='text-align:center; font-weight:bold;'><i class='fas fa-envelope'></i> ". $celkovo_bany . "</h2></p>";
if(isset($_POST['id_zmazat']))
{
	header('Refresh: 0');
	$sql2 = "DELETE FROM midasvote WHERE id=". $_POST['id_zmazat'] ;
	$conn->query($sql2);
}

if(isset($_POST['cas']) and isset($_POST['nick_pridat']) and isset($_POST['email_pridat']))
{
	header('Refresh: 0');
	$sql2 = "INSERT INTO midasvote (nick, expiracia, email) VALUES ('". $_POST['nick_pridat'] ."', '". $_POST['cas']."', '". $_POST['email_pridat'] ."')";
	$conn->query($sql2);
}
if ($celkovo_bany <= 30) {

echo '<br>
<form style="text-align:center" method="post">
  <input type="hidden" name="cas" value="'. date("Y-m-d H:i:s", strtotime(" + 30 days")) .'">
  NICK:&nbsp;&nbsp;
  <input type="search" class="hladat" placeholder="Meno…" value="" name="nick_pridat" style="float:center">&nbsp;&nbsp;
  E-MAIL:&nbsp;&nbsp;
  <input type="search" class="hladat" placeholder="E-mail…" value="" name="email_pridat" style="float:center">&nbsp;&nbsp;
  <input type="submit" class="potvrdit" value="Pridať" style="float:center">
  </form>';
	
} 




echo '<br>
<form style="text-align:center" method="get">
  <input type="search" class="hladat" placeholder="Meno…" value="" name="nick" style="float:center">
  <input type="submit" class="potvrdit" value="Nájdi" style="float:center">
  </form>';
  
  
  
if (isset($_GET['nick'])) {
	$nickname = $_GET['nick'];
	$sql3 = "SELECT * FROM midasvote WHERE nick=\"". $_GET['nick'] . "\"";
	$result2 = $conn->query($sql3);
	
	echo '<br><br>';
	echo '<div style="overflow-x:auto !IMPORTANT;">';
	echo '<table class="widefat color padd"><tbody>';
	echo '<tr><th width=30% style="text-align: center">Herný nick</th><th width=30% style="text-align: center">E-mail</th><th width=30% style="text-align: center">Expirácia</th><th></th></tr>';
	if ($result2->num_rows > 0) {
		while($row = $result2->fetch_assoc()) {
			echo '<tr><td align="center"><img style="margin-right: 7px; border-radius: 5px" src="https://cravatar.eu/head/'.$row['nick'].'" width="24px" height="24px"> <br>'.$row['nick'] .'</td><td align="center">'. $row['email'] .'</td><td align="center">'. date("d.m.Y  H:i", strtotime($row['expiracia'])) .'</td><td align="center">
			
			<form action="" method="post">
			  <input type="hidden" id="custId" name="id_zmazat" value="'. $row['id'] .'">
			  <input type="submit" class="potvrdit" value="Zmazať">
			</form>
			
			
			</td></tr>';
		}
	} 
	else
	{
		echo "Hráč s daným nickom nemá aktivované zasielanie e-mailov!";
	}
	echo '</tbody></table>';
	echo '</div>';
} 
else {
	

echo '  
<div class="pagination">
  <a href="';
  if($strana <= 1){ echo '#'; } else { echo "?strana=".($strana - 1); } 
  echo '" style="float:left"><i class="fas fa-arrow-left"></i></a>
  <a href="';
  if($strana >= $celkovo_stranky){ echo '#'; } else { echo "?strana=".($strana + 1); }
  
  echo '"style="float:right"><i class="fas fa-arrow-right"></i></a>
</div>
';
echo '<br><br>';
echo '<div style="overflow-x:auto !IMPORTANT;">';
echo '<table class="widefat color padd"><tbody>';
echo '<tr><th width=30% style="text-align: center">Herný nick</th><th width=30% style="text-align: center">E-mail</th><th width=30% style="text-align: center">Expirácia</th><th></th></tr>';
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		echo '<tr><td align="center"><img style="margin-right: 7px; border-radius: 5px" src="https://cravatar.eu/head/'.$row['nick'].'" width="24px" height="24px"> <br>'.$row['nick'] .'</td><td align="center">'. $row['email'] .'</td><td align="center">'. date("d.m.Y  H:i", strtotime($row['expiracia'])) .'</td><td align="center">
		
		<form action="" method="post">
		  <input type="hidden" id="custId" name="id_zmazat" value="'. $row['id'] .'">
		  <input type="submit" class="potvrdit" value="Zmazať">
		</form>
		
		
		</td></tr>';
	}
} 
else
{
	echo "0 hráčov s aktívnym zasielaním emailov !";
}
echo '</tbody></table>';
echo '</div>';
$conn->close();

echo '

<div class="pagination">
  <a href="';
  if($strana <= 1){ echo '#'; } else { echo "?strana=".($strana - 1); } 
  echo '" style="float:left"><i class="fas fa-arrow-left"></i></a>
  <a href="';
  if($strana >= $celkovo_stranky){ echo '#'; } else { echo "?strana=".($strana + 1); }
  
  echo '"style="float:right"><i class="fas fa-arrow-right"></i></a>
</div>
';
}
?>