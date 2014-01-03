<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=input
  Order=1
  [END_COT_EXT]
  ==================== */

/**
 * Header notifications
 *
 * @package debugbar
 * @version 2.1.0
 * @author Cotonti Team
 * @copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');




if (cot_auth('plug', 'debugconsole', 'R'))
{
	$cfg['debug_mode'] = TRUE;

	require_once cot_incfile('debugconsole', 'plug');


	$dconsole = new debugconsole();
	$dconsole->time('Timer');

	function cot_console()
	{
		global $dconsole;
		$vars = func_get_args();
		foreach ($vars as $name => $var)
		{
			$dconsole->log($var);
		}

	}
/*
	$pdo = new DebugBar\DataCollector\PDO\TraceablePDO($db);
	$debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));*/
}