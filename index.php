<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name = "color-scheme" content="light dark"><style>input { font-size:16px; }</style></head><body>
<?php
function xrfb_disp_cash($amt)
{
$cash = $amt / 100;
$cash = sprintf("%.2f", $cash);
$cash = "$" . $cash;
return ($cash);
}

$db = new SQLite3('/var/sum.sqlite');
$db->exec("CREATE TABLE IF NOT EXISTS sum (value INTEGER, memo TEXT)");

$do = $_POST['do'] ?? '';
if ($do != '')
{
	$value = $_POST['value'] ?? 0;
	$memo = strip_tags($_POST['memo']) ?? '';
	$value = $value * 100;
	if ($do == "SUB") $value = $value * -1;
	
	$addvalue = $db->prepare('INSERT INTO sum (value, memo) VALUES (:value, :memo)');
	$addvalue->bindValue(':value', $value);
	$addvalue->bindValue(':memo', $memo);
	$addvalueresult = $addvalue->execute();
}
?>
<form action="index.php" method="POST">
<table><tr><td>Amount:</td><td><input type="text" name="value" title="Amount only, no commas or symbols" pattern="(0\.((0[1-9]{1})|([1-9]{1}([0-9]{1})?)))|(([1-9]+[0-9]*)(\.([0-9]{1,2}))?)" required> <input type="submit" name="do" value="ADD" style="width:50px;"></td></tr>
<tr><td>Memo:</td><td><input type="text" name="memo" title="Optional note"> <input type="submit" name="do" value="SUB" style="width:50px;"></td></tr></table>
</form><p><table>

<?php
$data = $db->query("SELECT value, memo FROM sum ORDER BY rowid DESC");
$i = 0; $total = 0;
while ($res = $data->fetchArray(SQLITE3_ASSOC)) {
	$value = $res['value'];
	$total = $total + $value;
	echo "<tr><td align=right>" . xrfb_disp_cash($value) . "</td><td>" . $res['memo'] . "</td></tr>";
	$i++;
}
?>
</table><h2><?php echo xrfb_disp_cash($total); ?></h2></body></html>