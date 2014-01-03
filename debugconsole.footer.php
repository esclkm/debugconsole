<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=footer.last
  Order=99
  [END_COT_EXT]
  ==================== */

/**
 * Header notifications
 *
 * @package contactphone
 * @version 2.1.0
 * @author Cotonti Team
 * @copyright (c) Cotonti Team 2008-2013
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

if (cot_auth('plug', 'debugconsole', 'R'))
{
	// Делаем группу с увлечениями
	$dconsole->group("Hooks");
	 // Пишем заголовок
	 $dconsole->info("list of used hooks");

	// закрываем группу
	$hooksss = $cot_hooks_fired;
	unset($hooksss[count($hooksss)-1]);
	foreach ($hooksss as $hook) 
	{
		$dconsole->log($hook);
	}
	$dconsole->groupEnd();
	
	
	// Creation time statistics
	$i = explode(' ', microtime());
	$sys['endtime'] = $i[1] + $i[0];
	$sys['creationtime'] = round(($sys['endtime'] - $sys['starttime']), 3);

	$out['creationtime'] = (!$cfg['disablesysinfos']) ? $L['foo_created'].' '.cot_declension($sys['creationtime'], $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	$out['sqlstatistics'] = ($cfg['showsqlstats']) ? $L['foo_sqltotal'].': '.cot_declension(round($db->timeCount, 3), $Ls['Seconds'], $onlyword = false, $canfrac = true).' - '.$L['foo_sqlqueries'].': '.$db->count. ' - '.$L['foo_sqlaverage'].': '.cot_declension(round(($db->timeCount / $db->count), 5), $Ls['Seconds'], $onlyword = false, $canfrac = true) : '';
	$out['bottomline'] = $cfg['bottomline'];
	$out['bottomline'] .= ($cfg['keepcrbottom']) ? $out['copyright'] : '';

	$dconsole->group("MySQL");
	$dconsole->info('MySQL queries | Begin: 0.000 ms - End: '.sprintf("%.3f", $sys['creationtime']).' ms | Total: '.round($db->timeCount, 4) ." | Queries: ".$db->count. " | Average: ".round(($db->timeCount / $db->count), 5)."s/q");

	if(is_array($sys['devmode']['queries']))
	{
		foreach ($sys['devmode']['queries'] as $k => $i)
		{
			$path = str_replace("\n → ", ' \ ', $i[3]);
			preg_match('/(.+)->(.+?)\(\);$/', $path, $mt);

			$dconsole->log($i[0].". ".htmlspecialchars($i[2])."\n → Duration: ".sprintf("%.3f", round($i[1] * 1000, 3)).' ms | Timeline: '
				.sprintf("%.3f", round($sys['devmode']['timeline'][$k] * 1000, 3))." ms \n → Stack: ". $path, $mt[2]);
		}
	}
	$dconsole->groupEnd();
	
	$dconsole->group('System');
	$dconsole->info('System info');

	$i = explode(' ', microtime());
	$endtime = $i[1] + $i[0];
	$creationtime = round(($endtime - $sys['starttime']), 3);

	$dconsole->log($L['foo_created'].' '.cot_declension($creationtime, $Ls['Seconds'], false, true));
	$dconsole->log($L['foo_sqltotal'].': '.cot_declension(round($db->timeCount, 3), $Ls['Seconds'], false, true).' - '.$L['foo_sqlqueries'].': '.$db->count. ' - '.$L['foo_sqlaverage'].': '.cot_declension(round(($db->timeCount / $db->count), 5), $Ls['Seconds'], false, true));
	$dconsole->groupEnd();
	
// Эксперементальная опция получить все темплейты

	$dump = XTemplate::debugData();

	if ($dump = XTemplate::debugData())
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
						unset($open_blocks[count($open_blocks)-1]);
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
					unset($open_blocks[count($open_blocks)-1]);
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
}