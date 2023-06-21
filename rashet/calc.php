<? require_once("../acc/includes/db.inc.php");


$qty = $_GET["qty"];
$bag_sizes = $_GET["bag_sizes"];
$matherials = $_GET["matherials"];
$print_type = $_GET["print_type"];
$lamination = $_GET["lamination"];
$luvers = $_GET["luvers"];
$ruchki = $_GET["ruchki"];
$dlina_ruchki = $_GET["dlina_ruchki"];


echo $qty."<br>";
echo $bag_sizes."<br>";
echo $matherials."<br>";
echo $print_type."<br>";
echo $lamination."<br>";
echo $luvers."<br>";
echo $ruchki."<br>";
echo $dlina_ruchki."<br>";
?>