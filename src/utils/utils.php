<?php

include 'config.php';

class Logger {

	//2019-09-12 22:32:21 /Applications/MAMP/htdocs/index.php [tag]: message
	public static function message($message, $file = "verbose", $tag = "info") {
		date_default_timezone_set('Europe/Minsk');
		$message = date('Y-m-d H:i:s').' '.$file.' ['.$tag.']: '.$message.PHP_EOL;
		file_put_contents('work.log', $message, FILE_APPEND);
	}

	//2019-09-13 00:07:02 Logger [Download]: Log downloaded by ::1
	//OS: Mac OS X
	public static function download() {
		$msg = "Log downloaded by ".$_SERVER['REMOTE_ADDR'].PHP_EOL.'OS: '.Utils::getOS();
		Logger::message($msg, "Logger", "Download");
		header('Content-disposition: attachment;filename=work.log');
		readfile("work.log");
	}

	//2019-09-12 23:50:58 Logger [getPlatformInfo]: Yandex Music Fisher 0.0.1
	//OS: Mac OS X
	public static function getPlatformInfo() {
		global $config;
		$msg = $config['title'].' '.$config['version'].PHP_EOL.'OS: '.Utils::getOS();
		Logger::message($msg, "Logger", "getPlatformInfo");
	}
}

class Utils {

    /**
     * pretty-print var_dump
     *
     * @param mixed $value
     */
	public static function dump($value) {
		print '<pre>'.print_r($value, true).'</pre>';
	}

    /**
     * pretty-print json
     *
     * @param mixed $data
     */
	public static function jsonEncode($data) {
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

	public static function getOS() { 
		$user_agent = $_SERVER["HTTP_USER_AGENT"];
    	$os_platform = "Unknown OS Platform";
    	$os_array = array(
            '/windows nt 10/i'     =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
    	foreach ($os_array as $regex => $value) { 
        	if (preg_match($regex, $user_agent)) {
            	$os_platform    =   $value;
        	}
    	}
    	return $os_platform;
    }

}

?>