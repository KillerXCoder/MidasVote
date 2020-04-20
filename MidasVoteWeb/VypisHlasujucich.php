<?php
define('HOSTNAME', ''); 
define('USERNAME', '');
define('PASSWORD', '');

try { $sql = new PDO('mysql:host='.HOSTNAME.'', USERNAME, PASSWORD); $sql -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); }
catch(PDOException $e) { die('Připojení se nezdařilo: '.$e -> getMessage()); }

$list = $sql -> prepare('SELECT * FROM MidasCraft.hlasy ORDER BY Pocet DESC LIMIT 70'); $list -> execute();
$sum = $sql -> prepare("SELECT SUM(Pocet) FROM MidasCraft.hlasy"); $sum -> execute(); $sum = $sum -> fetchColumn();

echo '<style>

.padd th, .padd td { padding: 10px 10px; vertical-align: middle }
.lh { line-height: 24px; }

.color tr:nth-child(even) { background: #ebebeb; }
.color tr:nth-child(odd) { background: #FFF; border: none;}

.mnu th { padding: 0; color: white; transition: 0.25s ease-out; background: #2A2A2A; border: none;  vertical-align: middle }
.mnu th:first-child { border-right: 1px solid rgba(255, 255, 255, 0.1); }
.mnu th:hover { background: #E64946 }
.mnu a, .mnu a:hover { padding: 10px 10px; color: white; text-decoration: none; display: block; height: 100% width: 100%; }
}

</style>';

echo '<table class="widefat mnu"><tbody><tr><th width=50% style="text-align: center"><a href="/zoznam-hlasujucich-za-aktualny-mesiac/">TOP hráči za aktuálny mesiac</a></th><th width=50% style="text-align: center"><a  href="/vyhladavanie-hracov/">Vyhľadávanie hráčov</a></th></tr></th></tbody><tbody><tr><th width=50% style="text-align: center"><a  href="/odohrany-cas-hracov">Celkový odohraný čas hráčov</a></th></tbody></table>';


echo '<p align="center">Celkový počet odovzdaných hlasov za aktuálny mesiac: <b>'.$sum.'</b></p>';

echo '<table class="widefat color padd"><tbody>';

echo '<tr><th width=10% style="text-align: left">Poradie</th><th width=60% style="text-align: left">Meno hráča</th><th width=30% style="text-align: center">Počet hlasov</th></tr>';

$i = 0;

while(($row = $list -> fetch()) && $i < 50) {

$i++;

$banned = $sql -> prepare("SELECT COUNT(*) FROM midascraftdb_.bans WHERE name=?"); $banned -> execute(array($row['Nick']));
$banned = $banned -> fetchColumn() ? true : false;

if(!$banned) {
echo '<tr><td align="right">'.$i.'.</td><td style="vertical-align: middle"><img style="margin-right: 7px; border-radius: 5px" src="https://cravatar.eu/head/'.$row['Nick'].'" width="24px" height="24px"><b class="lh">'.$row['Nick'].'</b></td><td align="center">'.$row['Pocet'].'</br></td></tr>';
} else { $i--; }

}
echo '</tbody></table>';

?>