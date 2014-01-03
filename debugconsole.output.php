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
	global $dconsole, $L, $sys, $Ls, $db;


		// Creation time statistics
	$i = explode(' ', microtime());
	$sys['endtime'] = $i[1] + $i[0];
	$creationtime = round(($sys['endtime'] - $sys['starttime']), 3);

	$dconsole->log($L['foo_created'].' '.cot_declension($creationtime, $Ls['Seconds'], false, true));
	// Делаем группу с увлечениями
	$dconsole->group("Hooks");
	// закрываем группу
	$hooksss = $cot_hooks_fired;
	unset($hooksss[count($hooksss)-1]);
	foreach ($hooksss as $hook) 
	{
		$dconsole->log($hook);
	}
	$dconsole->groupEnd();
	
	

	$dconsole->group("MySQL");
	$dconsole->log('MySQL queries | Begin: 0.000 ms - End: '.sprintf("%.3f", $sys['creationtime']).' ms | '.$L['foo_sqltotal'] .': '.round($db->timeCount, 4) ." | ".$L['foo_sqlqueries'] .": ".$db->count. ' | '.$L['foo_sqlaverage'].': '.round(($db->timeCount / $db->count), 5)."s/q", 'info');

	if(is_array($sys['devmode']['queries']))
	{
		foreach ($sys['devmode']['queries'] as $k => $i)
		{
			$path = str_replace("\n → ", ' \ ', $i[3]);
			preg_match('/(.+)->(.+?)\(\);$/', $path, $mt);

			$dconsole->log($i[0].". ".htmlspecialchars($i[2])."\n → Duration: ".sprintf("%.3f", round($i[1] * 1000, 3)).' ms | Timeline: '
				.sprintf("%.3f", round($sys['devmode']['timeline'][$k] * 1000, 3))." ms \n → Stack: ". $path . ' ('.$mt[2]. ')');
		}
	}
	$dconsole->groupEnd();
	
// Эксперементальная опция получить все темплейты

	if (method_exists('XTemplate', 'debugData') && $dump = XTemplate::debugData())
	{
		$dconsole->group('TPL Tags');
		foreach($dump as $tpl => $blocks)
		{
			$dconsole->group($tpl);

			krsort($blocks);
			
			$open_blocks = array();
			foreach ($blocks as $block_name => $block_var)
			{
				$blocks_tree = explode('.', $block_name);
				
				if ($more = count($open_blocks) - count($blocks_tree))
				{
					for ($i = 0; $i < $more; $i++)
					{
						$dconsole->groupEnd();
						array_pop($open_blocks);
					}
				}
				foreach ($blocks_tree as $bt)
				{
					if (!in_array($bt, $open_blocks))
					{
						$dconsole->group('<!-- BEGIN: '.$bt.' -->');
						$open_blocks[] = $bt;
					}
				}
				asort($block_var);
				foreach ($block_var as $var_name => $var_val)
				{
					$dconsole->log('{'.$var_name.'} => ' .str_replace("\n", '', $var_val));
				}
				$dconsole->log('<!-- END: '.end($blocks_tree).' -->');
				$dconsole->groupEnd();
				if (end($blocks_tree) == end($open_blocks))
				{
					array_pop($open_blocks);
				}
			}
			if(count($open_blocks))
			{
				$open_blocks = array_reverse($open_blocks);
				foreach ($open_blocks as $ob)
				{
					$dconsole->log('<!-- END: '.end($ob).' -->');
					$dconsole->groupEnd();
				}
			}
				
			$dconsole->groupEnd();
		}
		$dconsole->groupEnd();
	}
	
	
	$output = str_replace('</body>', $dconsole->end().'</body>', $output);


}