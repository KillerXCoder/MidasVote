<?php
#===================================================================
# Názov: Web interface pre zobrazenie archívu TOP hlasujúcich za mesiac
# Autor: KillerXCoder (Peter Federl)
# E-Mail: peter.federl@gmail.com
#===================================================================
*/
echo '<style>

.padd th, .padd td { padding: 10px 10px; vertical-align: middle }
.lh { line-height: 24px; }

.color tr:nth-child(even) { background: #ebebeb; }
.color tr:nth-child(odd) { background: #FFF; border: none; }

.mnu th { padding: 0; color: white; transition: 0.25s ease-out; background: #2A2A2A; border: none;  vertical-align: middle }
.mnu th:first-child { border-right: 1px solid rgba(255, 255, 255, 0.1); }
.mnu th:hover { background: #E64946 }
.mnu a, .mnu a:hover { padding: 10px 10px; color: white; text-decoration: none; display: block; height: 100% width: 100%; }
}

</style>';

$servername = '';
$username = '';
$password = '';
$dbname = '';

$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT * FROM mesacny";
$result = $conn->query($sql);
$pocet_mesiacov = $result->num_rows / 10;
$dodesat = 0;
$ratanie = 0;
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

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if($dodesat == 0){
		echo '<table class="widefat mnu"><tbody>  <tr><th width=50% style="text-align: center"><a  href="#" onclick="myFunction'. $ratanie .'()">'. $row['mesiac'] . '/' . $row['rok']. '</a></th></tr> </tbody></table>';
		echo '<table style="display:none" id="more'. $ratanie  .'" class="widefat color padd"><tbody>';
		$ratanie++;
		echo '<tr><th width=10% style="text-align: left">Poradie</th><th width=60% style="text-align: left">Meno hráča</th><th width=30% style="text-align: center">Počet hlasov</th></tr>';
		}
		if($dodesat < 11){
		echo '<tr><td align="right">'.$row['poradie'].'.</td><td style="vertical-align: middle"><img style="margin-right: 7px; border-radius: 5px" src="https://cravatar.eu/head/'.$row['nick'].'" width="24px" height="24px"><b class="lh">'.$row['nick'].'</b></td><td align="center">'.$row['pocet'].'</br></td></tr>';
		$dodesat++;
		}
		if($dodesat == 10){
			$dodesat = 0;
			echo '</tbody></table>';
		}
	}
} 


else
{
echo "prazdna tabulka";
}
$conn->close();
$ratanie = 0;
echo ' <script>';
while( $ratanie < $pocet_mesiacov){

echo '
function myFunction'. $ratanie .'() {
	var x'. $ratanie .' = document.getElementById("more'. $ratanie .'");
  if (x'. $ratanie .'.style.display === "none") {
	x'. $ratanie .'.style.display = "block";
  } else {
	x'. $ratanie .'.style.display = "none";
  }
}';
 $ratanie++;
  
  
	}
 
echo '
 </script>';
?>