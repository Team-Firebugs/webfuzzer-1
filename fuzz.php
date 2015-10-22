<?php

if(!isset($_SERVER['argc'])) {
        die("Please run via command line.");
}

$fuzz_file = "fuzzing.txt";

class web_fuzz {
	
	public $target;
	public $type;

	public function check_status($status, $url) {
		if(strpos($status, "200") !== false) {
			return "200 Found at: ".$url;
		} elseif(strpos($status, "404") !== false) {
			return "404 File not found: ".$url;
		} elseif(strpos($status, "401") !== false) {
			return "401 Protected Dir: ".$url;
		} elseif(strpos($status, "403") !== false) {
			return "403 Forbidden at: ".$url;
		} elseif(strpos($status, "302") !== false) {
			return "302 Redirection at: ".$url;
		}
	}

	public function get_status($url) {
		$headers = get_headers($url);
		$status_code = $headers[0];
		return $status_code;
	}

	public function download_fuzz_list() {
		$url = "https://raw.githubusercontent.com/danielmiessler/SecLists/master/Discovery/Web_Content/CMS/";
		if($this->type === 1) {
			$output = file_get_contents($url."wordpress.fuzz.txt");
		} elseif($this->type === 2) {
			$ouptut = file_get_contents($url."wp_plugins.fuzz.txt");
		} elseif($this->type === 3) {
			$output = file_get_contents($url."joomla_plugins.fuzz.txt");
		} elseif($this->type === 4) {
			$output = file_get_contents($url."joomla_themes.fuzz.txt");
		} elseif($this->type === 5) {
			$output = file_get_contents($url."ColdFusion.fuzz.txt");
		} elseif($this->type === 6) {
			$output = file_get_contents($url."php-nuke.fuzz.txt");
		} elseif($this->type === 7) {
			$output = file_get_contents($url."sharepoint.txt");
		} elseif($this->type === 8) {
			$output = file_get_contents($url."sitemap_magento.txt");
		} elseif($this->type === 9) {
			$output = file_get_contents($url."drupal_plugins.fuzz.txt");
		} elseif($this->type === 10) {
			$output = file_get_contents($url."drupal_themes.fuzz.txt");
		} else {
			$output = "";
		}
		$array = explode("\n", $output);
		return $array;
	}

	public function open_fuzz_list($file) {
		$list = file($file, FILE_IGNORE_NEW_LINES);
		return $list;
	}

	public function write_to_file($file, $content) {
		$file = fopen($file, "w") or die("Unable to open the file.");
		foreach($content as $data) {
			fwrite($file, $data."\n");
		}
		fclose($file);
	}

	public function run_check($file) {
		$list = $this->open_fuzz_list($file);
		foreach($list as $link) {
			echo $this->check_status($this->get_status($this->target.$link), $this->target.$link).PHP_EOL;
		}
	}

	public function banner() {
		echo '
              ___.                            _____                     
__  _  __ ____\_ |__ _____  ______ ______   _/ ____\_ __________________
\ \/ \/ // __ \| __ \\\__  \ \____ \\\____ \  \   __\  |  \___   /\___   /
 \     /\  ___/| \_\ \/ __ \|  |_> >  |_> >  |  | |  |  //    /  /    / 
  \/\_/  \___  >___  (____  /   __/|   __/   |__| |____//_____ \/_____ \
             \/    \/     \/|__|   |__|                       \/      \/
             ';
	}
	public function help() {
		echo $this->banner();
		echo '
        Usage: php fuzz.php http://target.com/ type
        Types: 
               1 = wordpress
        	   2 = wordpress plugins
        	   3 = joomla plugins
        	   4 = joomla themes
        	   5 = ColdFusion
        	   6 = php-nuke
        	   7 = sharepoint
        	   8 = Magento
        	   9 = drupal plugins
        	   10 = drupal themes
		';
	}
}

$fuzz = new web_fuzz();

if(empty($argv[1]) || empty($argv[2])) {
	die($fuzz->help());
}
echo $fuzz->banner();
$fuzz->type = $argv[2];
$fuzz->target = rtrim($argv[1], '/') . '/';
$fuzz->write_to_file($fuzz_file, $fuzz->download_fuzz_list());
$fuzz->run_check($fuzz_file);

?>
