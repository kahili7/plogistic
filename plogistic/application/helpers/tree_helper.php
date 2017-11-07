<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

function transform2forest($rows, $idName, $pidName)
{
    $children = array();
    $ids = array();

    foreach ($rows as $i => $r)
    {
	$row = & $rows[$i];
	$id = $row[$idName];
	$pid = $row[$pidName];
	$children[$pid][$id] = & $row;

	if (!isset($children[$id]))
	    $children[$id] = array();

	$row['childNodes'] = & $children[$id];
	$ids[$row[$idName]] = true;
    }

    $forest = array();

    foreach ($rows as $i => $r)
    {
	$row = & $rows[$i];

	if (!isset($ids[$row[$pidName]]))
	{
	    $forest[$row[$idName]] = & $row;
	}
    }

    return $forest;
}

?>