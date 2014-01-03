<?php

defined('COT_CODE') or die('Wrong URL');

class debugconsole
{

	private $code = '',
		$timers = array(),
		$counter = 0,
		$vars = array();

	function debugconsole()
	{
		$this->allow = true;
	}

	/* Вывести все в консоли */

	public function end()
	{
		if ($this->allow)
		{
			
			$this->code = '<script type="text/javascript" id="debug">(function(){' . $this->code;
			if (sizeof($this->vars) > 0)
			{
				$dump = 'function dump(a,b){var c="";if(!b)b=0;var d="";for(var j=0;j++<=b;)d+=" ";if(typeof(a)==\'object\'){for(var e in a){var f=a[e];if(typeof(f)==\'object\'){c+=d+"\'"+e+"\' ...\n";c+=dump(f,b+1)}else{c+=d+"\'"+e+"\' => \""+f+"\"\n"}}}return c}';
				$this->code .= $dump;
			}
			$this->code = str_replace(array("\n", "\r"), array('\n', ''), $this->code);
			return $this->code."})();</script>";
		}
		else
			return null;
	}

	/*
	 * Группировка
	 */

	public function group($name)
	{
		$this->code .= "console.groupCollapsed('".$name."');";
		return $this;
	}

	public function groupEnd()
	{
		$this->code .= "console.groupEnd();";
		return $this;
	}

	/*
	 * Начало запуска таймера
	 */

	public function time($name)
	{
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$this->timers[$name] = $mtime;
		return $this;
	}

	/*
	 * Остановка таймера
	 */

	public function timeEnd($name)
	{
		$timeStart = $this->timers[$name];
		if ($timeStart)
		{
			$mtime = microtime();
			$mtime = explode(" ", $mtime);
			$mtime = $mtime[1] + $mtime[0];
			$endtime = $mtime;
			$totaltime = $endtime - $timeStart;
			$this->info("$name: $totaltime seconds");
			$this->timers[$name] = null;
		}
		return $this;
	}

	// Стандартное сообщение в консоль
	// 'log' error warn info
	public function log($msg, $mode = 'log')
	{
		if (is_string($msg))
		{
			$msg = str_replace(array("\n", "'"), array('\n','\\\''), $msg);
			$msg = "'$msg'";
		}
		if (is_array($msg))
		{
			$name = "o".($this->counter++);
			$this->code .= $this->js_hash($msg, $name);
			$this->code .= "console.".$mode."(dump(".$name."));";
		}

		if (!$name)
		{
			$this->code .= "console.".$mode."(".$msg.");";
		}
		return $this;
	}

	/*
	 * Посмтроение объекта JS из PHP массива
	 */

	private function js_hash($arr, $name, & $code = '')
	{
		if (!$this->vars[$this->counter])
		{
			$code .= "var ";
			$this->vars[$this->counter] = true;
		}

		$code .= $name."={};";

		foreach ($arr as $key => $value)
		{

			$outKey = (is_int($key)) ? '['.$key.']' : ".$key";

			if (is_array($value))
			{
				$this->js_hash($value, $name.$outKey, $code);
				continue;
			}

			$code .= $name.$outKey."=";

			if (is_string($value))
			{
				$value = str_replace(array( "'"), array('\\\''), $value);
				$code .= "'".$value."';";
			}
			else if ($value === false)
			{
				$code .= "false;";
			}
			else if ($value === NULL)
			{
				$code .= "null;";
			}
			else if ($value === true)
			{
				$code .= "true;";
			}
			else
			{
				$code .= $value.";";
			}
		}
		return $code;
	}

}