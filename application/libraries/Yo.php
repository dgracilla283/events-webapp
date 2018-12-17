<?php
class Yo {
    static function log($var) {
        $bt = debug_backtrace();

    	$args = func_get_args();
    	$first_arg = func_get_arg(0);
    	$levels = 1;
    
    	if (is_string($first_arg) && preg_match('/^level:(\d+)$/', $first_arg, $matches))
    	{
    		$levels = $matches[1];
    		unset($args[0]);
    	}
    
    	if (isset($bt[$levels])) {
    		$caller = $bt[$levels]['function'];
    
    		if (isset($bt[$levels]['class'])) {
    			$caller = $bt[$levels]['class'] . "::" . $caller;
    		}
    		
    		if (isset($bt[$levels-1]['line'])) {
    			$caller = $bt[$levels-1]['line'] . "::" . $caller;
    		}
    		
    		if (isset($bt[$levels-1]['file'])) {
    			preg_match('@.*/([^/]*)?/([^/]+\.php)$@', $bt[$levels-1]['file'], $match);
    			$caller = (!empty($match[1])?$match[1].'/':'') . (!empty($match[2])?$match[2]:'') . "::" . $caller;
    		}
    	} else {
    		$caller = 'main';
    	}
    	
        echo "<pre style=\"text-align:left;\"><h1 style=\"font-size:14px;font-weight:bold;\">logger: $caller</h1>";
    	foreach ($args as $var)
        {
            if ($var === false)
            	echo "bool(false)";
    		else
    			echo print_r($var, true);
    			
    		echo "\n";
    	}
    	echo "</pre>";
    }
    static function dump($var, $title='') {
    	error_log(print_r($var, 1) , 3 , APPPATH . 'logs/debug.txt');
    }
}