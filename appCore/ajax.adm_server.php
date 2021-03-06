<?php

/* ======================================================================== \
|   FORMA - The E-Learning Suite                                            |
|                                                                           |
|   Copyright (c) 2013 (Forma)                                              |
|   http://www.formalms.org                                                 |
|   License  http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt           |
|                                                                           |
|   from docebo 4.0.5 CE 2008-2012 (c) docebo                               |
|   License http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt            |
\ ======================================================================== */

define("CORE", true);
define("IN_FORMA", true);
define("IS_AJAX", true);
define("_deeppath_", '../');
require(dirname(__FILE__).'/../base.php');

// start buffer
ob_start();

// initialize
require(_base_.'/lib/lib.bootstrap.php');
Boot::init(BOOT_DATETIME);
if(Get::cfg('enable_plugins', false)) { PluginManager::initPlugins(); }

// not a pagewriter but something similar
$GLOBALS['operation_result'] = '';
if(!function_exists("aout")) {
	function aout($string) { $GLOBALS['operation_result'] .= $string; }
}
require_once(_adm_.'/lib/lib.permission.php');

// load the correct module
$aj_file = '';
$mn = Get::req('mn', DOTY_ALPHANUM, '');
$plf = Get::req('plf', DOTY_ALPHANUM, ( $_SESSION['current_action_platform'] ? $_SESSION['current_action_platform'] : Get::cur_plat() ));

// New MVC structure
if(isset($_GET['r'])) {
	$request = $_GET['r'];
	$r = explode('/', $request);
	$action = $r[1];
	if(count($r) == 3) {
		// Position, class and method defined in the path requested
		$mvc =ucfirst(strtolower($r[1])). ucfirst(strtolower($r[0])).'Controller';
		$action = $r[2];
	} else {
		// Only class and method defined in the path requested
		$mvc = ''.ucfirst(strtolower($r[0])).'AdmController';
		$action = $r[1];
	}
	ob_clean();
	$controller = new $mvc( strtolower($r[1]) );
	$controller->request($action);

	aout(ob_get_contents());
	ob_clean();

} else {
	if($mn == '') {

		$fl = Get::req('file', DOTY_ALPHANUM, '');
		$sf = Get::req('sf', DOTY_ALPHANUM, '');
		$aj_file = $GLOBALS['where_'.$plf].'/lib/'.( $sf ? $sf.'/' : '' ).'ajax.'.$fl.'.php';
	} else {

		if($plf == 'framework') $aj_file = $GLOBALS['where_'.$plf].'/modules/'.$mn.'/ajax.'.$mn.'.php';
		else $aj_file = $GLOBALS['where_'.$plf].'/admin/modules/'.$mn.'/ajax.'.$mn.'.php';
	}

	include($aj_file);
}

// finalize
Boot::finalize();

// remove all the echo
ob_clean();

// Print out the page
// header('Content-type: text/plain');
echo $GLOBALS['operation_result'];

// flush buffer
ob_end_flush();

?>