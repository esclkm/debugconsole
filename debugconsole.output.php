<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=output
  Order=99
  [END_COT_EXT]
  ==================== */

/**
 * Debug Console for Cotonti CMF
 *
 * @package contactphone
 * @version 2.1
 * @author esclkm
 * @copyright (c) esclkm 2008-2014
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

if (cot_auth('plug', 'debugconsole', 'R'))
{
	global $dconsole;


	$output = str_replace('</body>', $dconsole->end().'</body>', $output);


}