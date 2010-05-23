<?php
/**
 * @package pragyan
 * @copyright (c) 2008 Pragyan Team
 * @license http://www.gnu.org/licenses/ GNU Public License
 * For more details, see README
 */

/**
 * breadcrumbs.lib.php
 * To generate the bread crumbs required to show the location of the page with links to each level.
 * Generate breadcrumbs for a given page
 *
 * @param $pageIdArray Array of integers holding the page ids of the pages constituting the path to the current page
 * @param $separator The string with which Items in the generated breadcrumbs should be separated
 * @return HTML string representing the breadcrumbs to be displayed for the given page
 */
function breadcrumbs($pageIdArray) {
	$sqlOutputArray = array();
	$pageIdList = join($pageIdArray, ",");
	$query = 'SELECT `page_id`, `page_name`, `page_title` FROM `' . MYSQL_DATABASE_PREFIX . 'pages` WHERE `page_id` IN (' . $pageIdList . ')';
	$resultId = mysql_query($query);

	while ($row = mysql_fetch_assoc($resultId))
		$sqlOutputArray[$row['page_id']] = array($row['page_name'], $row['page_title']);

	global $urlRequestRoot;

	$str = '<div id="cms-breadcrumb"><ul>';
	$hrefString = $urlRequestRoot . '/home/';
	$parentPath = '/';
	$pageCount = count($pageIdArray);

	global $userId;
        $children = getChildren($pageIdArray[$pageCount - 1], $userId);
	$selectedId = $pageCount - 1;
        if ($pageCount == 1) {
		$selectedId = 0;
                $children = getChildren(0, $userId);
	}

	for ($i = 0; $i < $pageCount; ++$i) {
		if ($i) {
			$hrefString .= $sqlOutputArray[$pageIdArray[$i]][0] . '/';
			$parentPath .= $sqlOutputArray[$pageIdArray[$i]][0] . '/';
		}

		$str .= '<li class="cms-breadcrumbItem';
		if ($i == $selectedId)
			$str .= ' selected';
		$str .= '" rel="' . $parentPath . '"><a href="' . $hrefString . '">' . $sqlOutputArray[$pageIdArray[$i]][1] . '</a></li>';
	}

	$str .= '</ul></div>';

	if(showBreadcrumbSubmenu())
	{
		$childCount = count($children);
		$childHtml = "";
		for ($i = 0; $i < $childCount; ++$i)
			$childHtml .= '<li><a href="' . ($selectedId == 0 ? $urlRequestRoot . '/home/' : './') . $children[$i][1] . '">' . $children[$i][2] . '</a></li>';
		$childHtml = "<ul>$childHtml</ul>";

		$str .= '<div id="cms-breadcrumbsubmenu">' . $childHtml . '<div class="clearer"></div></div>';
	}
	return $str;
}