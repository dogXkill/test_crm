<?
header('Content-Type: text/json; charset=utf-8');
require_once("../../includes/db.inc.php");
require_once("../../includes/lib.php");

$totalTiraz = 0;
$totalSkotch = 0;
$totalKley = 0;
$totalPaperList = 0;
$totalStretch = 0;

$uniquePodruchnkini = array();
$uniquePodruchnikiQuant = array();
$uniqueRuchki = array();
$uniqueRuchkiQuant = array();
$uniqueList = array();
$uniqueListQuant = array();
$uniqueScotch = array();
$uniqueScotchQuant = array();
$uniqueBottom = array();
$uniqueBottomQuant = array();

$id = $_GET['id'];
$data = array();
$data['id'] = $id;
$data['applications'] = array();
$data['uniquePodruchniki'] = array();
$data['uniquePodruchnikiQuant'] = array();
$data['uniqueRuchki'] = array();
$data['uniqueRuchkiQuant'] = array();

$q = "SELECT * FROM shipments WHERE id = $id";
mysql_query("SET NAMES 'utf8'");
$r = mysql_fetch_assoc(mysql_query("$q"));

$appNums = explode('||', $r['applications']);
$tirazNums = explode('||', $r['tiraz']);

foreach ($appNums as $key => $appId) {
  $application = array();
  $query = "SELECT * FROM applications WHERE num_ord = $appId";
  $res = mysql_fetch_assoc(mysql_query("$query"));
  $tiraz = explode('_', $tirazNums[$key])[1];
  $colInPack = $res['col_in_pack'];
  $shir = $res['izd_w'];
  $vis = $res['izd_v'];
  $bok = $res['izd_b'];
  $paperNumList = $res['paper_num_list'];
  $listH = $res['list_h'];
  $listW = $res['list_w'];
  $handColor = $res['hand_color'];
  $color_q = "SELECT * FROM colours WHERE cid = $handColor";
  $color_r = mysql_fetch_assoc(mysql_query("$color_q"));
  $ruchkiColor = $color_r['colour'];

  $application['numOrd'] = $res['num_ord'];
  $application['uid'] = $res['uid'];
  $application['tiraz'] = $tiraz;
  $application['shir'] = $shir;
  $application['vis'] = $vis;
  $application['bok'] = $bok;
  $application['bottomStrength'] = $tiraz;
  $application['ruchki'] = $tiraz * 2;
  $application['kley'] = $tiraz * 1.8 / 1000;
  $application['ruchkiColor'] = (!empty($ruchkiColor)) ? $ruchkiColor : 0;
  $ruchkiLength = $res['hand_length'];
  $application['ruchkiLength'] = (!empty($ruchkiLength)) ? $ruchkiLength : 0;
  $application['stickers'] = $tiraz / $colInPack;
  $application['paperNumList'] = ($paperNumList != 0) ? $paperNumList * $tiraz : 1 * $tiraz;
  $application['paperListSize'] = (!empty($listH) && !empty($listW)) ? '(выс ' . $listW . 'см, шир ' . $listH . 'см)' : '(размер не указан)';
  $application['scotch'] = ceil((($shir * 0.7 * 2) + ($vis + 5 + $bok * 0.7) * $paperNumList + $shir * 2) * $tiraz / 100);
  if (empty($application['scotch'])) {$application['scotch'] = 0;}
  switch ($res['gluing_material']) {
    case '1':
      $application['scotchType'] = 'белый';
      $countScotch = true;
      break;
    case '2':
    $application['scotchType'] = 'желтый';
      $countScotch = true;
      break;
    default:
      $application['scotchType'] = false;
      $application['scotch'] = 0;
      break;
  }
  if ($application['scotchType']) {
    if (!in_array($application['scotchType'], $uniqueScotch)) {
      array_push($uniqueScotch, $application['scotchType']);
      array_push($uniqueScotchQuant, $application['scotch']);
    } else {
      $numb = array_search($application['scotchType'], $uniqueScotch);
      $uniqueScotchQuant[$numb] = $uniqueScotchQuant[$numb] + $application['scotch'];
    }
  }

  $bottomStrSize = $shir . '-' . $bok;
  if (!in_array($bottomStrSize, $uniqueBottom)) {
    array_push($uniqueBottom, $bottomStrSize);
    array_push($uniqueBottomQuant, $tiraz);
  } else {
    $numb = array_search($bottomStrSize, $uniqueBottom);
    $uniqueBottomQuant[$numb] = $uniqueBottomQuant[$numb] + $tiraz;
  }

  $application['podruchniki'] = $tiraz * 2;
  $application['podruchnikiSizeName'] = 'выс 3 см, шир ' . ($shir - 1) . ' см';
  $application['stretch'] = ceil($tiraz * 1.5 / 100);

  $sizePodr = '3' . '-' . ($shir - 1);
  $application['podruchnikiSize'] = $sizePodr;
  if (!in_array($sizePodr, $uniquePodruchnkini)) {
    array_push($uniquePodruchnkini, $sizePodr);
    array_push($uniquePodruchnikiQuant, $application['podruchniki']);
  } else {
    $numb = array_search($sizePodr, $uniquePodruchnkini);
    $uniquePodruchnikiQuant[$numb] = $uniquePodruchnikiQuant[$numb] + $application['podruchniki'];
  }

  $uniqueRuchka = $application['ruchkiLength'] . '-' . $application['ruchkiColor'];
  if (!in_array($uniqueRuchka, $uniqueRuchki)) {
    array_push($uniqueRuchki, $uniqueRuchka);
    array_push($uniqueRuchkiQuant, $application['ruchki']);
  } else {
    $numb = array_search($uniqueRuchka, $uniqueRuchki);
    $uniqueRuchkiQuant[$numb] = $uniqueRuchkiQuant[$numb] + $application['ruchki'];
  }

  $sizeList = $listW . '-' . $listH;
  if (!in_array($sizeList, $uniqueList)) {
    array_push($uniqueList, $sizeList);
    array_push($uniqueListQuant, $application['paperNumList']);
  } else {
    $numb = array_search($sizeList, $uniqueList);
    $uniqueListQuant[$numb] = $uniqueListQuant[$numb] + $application['paperNumList'];
  }


  $totalTiraz = $totalTiraz + $tiraz;
  $totalSkotch = $totalSkotch + $application['scotch'];
  $totalKley = $totalKley + $application['kley'];
  $totalPaperList = $totalPaperList + $application['paperNumList'];
  $totalStretch = $totalStretch + $application['stretch'];
  // Норматив клея 2.33г на пакет
  // Норматив стрейчпленки 1.5г на пакет

  array_push($data['applications'], $application);
}

$data['bottomStrength'] = $totalTiraz;
$data['sideStrength'] = $totalTiraz * 2;
$data['ruchki'] = $totalTiraz * 2;
$data['totalTiraz'] = $totalTiraz;
$data['totalSkotch'] = $totalSkotch;
$data['totalKley'] = $totalKley;
$data['uniquePodruchniki'] = $uniquePodruchnkini;
$data['uniquePodruchnikiQuant'] = $uniquePodruchnikiQuant;
$data['uniqueRuchki'] = $uniqueRuchki;
$data['uniqueRuchkiQuant'] = $uniqueRuchkiQuant;
$data['uniqueList'] = $uniqueList;
$data['uniqueListQuant'] = $uniqueListQuant;
$data['totalPaperList'] = $totalPaperList;
$data['totalStretch'] = $totalStretch;
$data['uniqueScotch'] = $uniqueScotch;
$data['uniqueScotchQuant'] = $uniqueScotchQuant;
$data['uniqueBottom'] = $uniqueBottom;
$data['uniqueBottomQuant'] = $uniqueBottomQuant;

echo json_encode($data);
?>
