<?php

namespace App\Helpers\Radio;

use App\Http\Resources\StreamResource;
use Exception;

/******************************************************************************************************
	*  Written by Jaka Prasnikar (http://prahec.com)
	*  This script parses Icecast&Shoutcast XML stats and displays them in Multi/Single server configuration
	*  Its very easy to use.
	********************************************************************************************************/

	// This class returns array of radio INFO (Loads of Information)
	class Radio {

		// private $stream;

		// function __construct($stream) {
		// 	$this->stream = $stream;
		// }

		// Faster access to script with webradio::info($streams_array, $serverID)
		// static public function info($stream, $serverID = '') {

		// 	$obj = new self($stream);
		// 	return $obj->getInfo($serverID);

		// }

		// A new function to debug WebRadio setup
		// This function is VERY messy. But it shows data for debugging process.
		// public static function debug($array, $serverID = '') {

		// 	// Create Object
		// 	$ob = new self($array);
		// 	$start = microtime(true);

		// 	if ($serverID == '') $key = key($array); else $key = $serverID;

		// 	// Output debug details
		// 	$html = 'WebRadio class will now start debugging process... <br /><br />';

		// 	$html .= '<b>...Connecting to the first server in array:</b>
		// 	<button onclick="document.getElementById(\'conn\').style.display = \'block\'; return false;">Show Details</button>
		// 	<br /><div id="conn" style="display: none;"><pre>';


		// 	// Call GetData
		// 	if ($array[$key]['type'] == 'icecast') {
		// 		$data = $ob->connect("http://{$array[$key]['ip']}:{$array[$key]['port']}/admin/stats", "{$array[$key]['user']}:{$array[$key]['pw']}");
		// 	} else {
		// 		$data = $ob->connect("http://{$array[$key]['ip']}:{$array[$key]['port']}/admin.cgi?sid={$array[$key]['sid']}&pass={$array[$key]['pw']}&mode=viewxml");
		// 	}

		// 	$html .= (($data === false) ? '<b style="color: red;">ERROR: Failed to get data from the server!</b>' : '<b style="color: green;">Successfully fetched data</b>') . '</pre></div>';

		// 	// First function
		// 	$html .= '<b>...Getting the data from first server in array: </b>
		// 	<button onclick="document.getElementById(\'first\').style.display = \'block\'; return false;">Show Details</button>
		// 	<br /><div id="first" style="display: none;"><pre style="background: #f9f9f9;">';

		// 	$data1 = $ob->getData($array[$key]['ip'], $array[$key]['port'], $array[$key]['pw'], $array[$key]['sid'],
		// 		$array[$key]['type'], $array[$key]['user'], $array[$key]['mount']);

		// 	$html .= print_r($data1, true) . '</pre></div>';

		// 	// Second Function
		// 	$html .= '<b>...Parsing data of the first server: </b>
		// 	<button onclick="document.getElementById(\'second\').style.display = \'block\'; return false;">Show Details</button>
		// 	<br /><div id="second" style="display: none;"><pre style="background: #f9f9f9;">';

		// 	$data2 = $ob->parse($key, $data1, $array[$key]['type'], $array[$key]['mount']);

		// 	$html .= print_r($data2, true) . '</pre></div>';

		// 	// Third Function
		// 	$html .= '<b>...Last check run totals function (all servers):</b>
		// 	<button onclick="document.getElementById(\'third\').style.display = \'block\'; return false;">Show Details</button>
		// 	<br /><div id="third" style="display: none;"><pre style="background: #f9f9f9;">';

		// 	$data3 = $ob->totals();

		// 	$html .= print_r($data3, true) . '</pre></div>';

		// 	// Time taken
		// 	$total = round(((microtime(true) - $start) * 1000), 2) / 1000;
		// 	$html .= '<br /><br />Debug process took ' . round($total, 2) . ' second(s) to complete.';

		// 	return $html;

		// }


		// Returns single server info (if empty first server in array will be used)
		// IMPORTANT NOTE: If you use this function, array must include ALL details of server.
		// public function getInfo($serverID = '') {

		// 	if ($serverID == '') $key = key($this->streams); else $key = $serverID;


		// 	if (!empty($this->streams[$key]['ip']) || !empty($this->streams[$key]['port'])) {

		// 		return ($this->parse($key, $this->getData($this->streams[$key]['ip'], $this->streams[$key]['port'], $this->streams[$key]['pw'], $this->streams[$key]['sid'],
		// 			$this->streams[$key]['type'], $this->streams[$key]['user'], $this->streams[$key]['mount']), $this->streams[$key]['type'], $this->streams[$key]['mount']));

		// 	} else {

		// 		return 'Specified Server doesn\'t exist or required details are missing !';

		// 	}

		// }

		// Calculates values from all servers in array and returns them to user
		// public function totals() {

		// 	$total = array();

		// 	if ($data = $this->handle()) { // Get arrays from shoutcast

		// 		foreach ($data as $server => $arr) {

		// 			if ($arr == false) { continue; } // Skip count if results ain't correct

		// 			$total['listeners']      += $arr['listeners'];
		// 			$total['peak']           += $arr['peak'];
		// 			$total['listen-time']    += $arr['avg'];
		// 			$total['hits']           += $arr['hits'];

		// 			if (is_array($arr['listeners-list'])) {
		// 				foreach($arr['listeners-list'] as $newarr) {
		// 					$total['listeners-list'][] = $newarr;
		// 				}
		// 			}

		// 			if ($total['status'] == "0") { // If any server before this one is offline, mark all as offline!
		// 				continue;
		// 			} else { // else set status
		// 				$total['status'] = $arr['status'];
		// 			}

		// 		}

		// 		return $total;
		// 		// Return our data and exit script

		// 	} else { // If there is failture in xml2arr just return false fuck it

		// 		return false;

		// 	}

		// }

		// This function uses APC cache to speed up loads of requests
		// public function cached($name = 'WebRadio') {

		// 	if (!$data = apc_fetch($name)) { // If not cached, create new cache

		// 		$new = $this->totals();
		// 		apc_store($name, $new, 60);
		// 		return $new;

		// 	} else { // If cached, return data.
		// 		return $data;
		// 	}

		// }

		// // New Function to handle Icecast/Shoutcast xml parses (Note: Icecast has to connect twice!)
		private function getData($radioStation) {

            $type = $radioStation->serverType;
            $url = $radioStation->serverURL;
            $serverPassword = $radioStation->serverPassword;
            $serverUsername = $radioStation->serverUsername;

			if ($type == 'shoutcast') { // Shoutcast handle
                $serverID = $radioStation->serverID;
				return self::xml2array($this->connect("{$url}/admin.cgi?sid={$serverID}&pass={$serverPassword}&mode=viewxml"));

			} elseif ($type == 'icecast') { // Icecast handle
                $serverMount = $radioStation->serverMount;
				$serverDetails = $this->connect("{$url}/admin/stats", "{$serverUsername}:{$serverPassword}");
				$listenDetails = $this->connect("{$url}/admin/listclients?mount=/{$serverMount}", "{$serverUsername}:{$serverPassword}");

				if (preg_match("/You need to authenticate/s", $serverDetails)) {

					// Log here if authorization fails
					echo 'Authorization failed!';
					return false;

				} else {

					return array_merge_recursive(self::xml2array($serverDetails), self::xml2array($listenDetails));

				}

			} else { // Type not icecast/shoutcast, return false.

				return false;

			}

		}

		// Simple connect function, returns error if remote connections are forbidden on server.
		private function connect($openURL, $auth = '') {

			if(function_exists('curl_version')) { // if CURL is enabled

				$cUrl = curl_init();
				curl_setopt($cUrl, CURLOPT_URL, $openURL);
				curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt($cUrl, CURLOPT_USERAGENT, "Mozilla");

				if (!empty($auth)) {
					curl_setopt($cUrl, CURLOPT_USERPWD, $auth);
				}

				$data = curl_exec($cUrl);
				curl_close($cUrl);

			} else { // Slower but works on CURL disabled servers

				if (!empty($auth)) {
					$a = stream_context_create(array('http' => array('header'  => "Authorization: Basic " . base64_encode($auth))));
				}

				$data = file_get_contents($openURL, false, $a);

			}

			return utf8_encode($data);

		}

		// Function to parse Shoutcast info from XML file and fixes listeners array for easier use.
		public function parse($radioStation, $type = 'shoutcast', $mount = '') {

			/****************************************************************************************************
			*   Remake SHOUTCAST array to match both servers array details (used for totals also)
			*****************************************************************************************************/
            $data = $this->getData(
                $radioStation
            );
			if ($type == 'shoutcast') {
				if( !isset($data['CURRENTLISTENERS']) ) {
					new Exception('Failed to get stats');
				}
				// Now the second part, we create a new array from old one, with details we need!
				$streamStats = array();
				$streamStats['listeners']        = $data['CURRENTLISTENERS'];
				$streamStats['peak']             = $data['PEAKLISTENERS'];
				$streamStats['genre']             = $data['SERVERGENRE'];
				$streamStats['title']            = $data['SONGTITLE'];
				// $streamStats['hits']             = $data['STREAMHITS'];
				$streamStats['status']           = $data['STREAMSTATUS'];
				// $streamStats['path']             = $data['STREAMPATH'];
				$streamStats['bitrate']          = $data['BITRATE'];
                // $streamStats['avg']              = $data['AVERAGETIME'];
				// $new['listeners-list']   = $data['LISTENERS'];


				/****************************************************************************************************
				*   Remake ICECAST array to match both servers array details (used for totals also)
				*****************************************************************************************************/
			} elseif ($type == 'icecast') {


				if( !isset($data['source']) && !isset($data['source']['listener'])) {
					new Exception('Failed to get stats');
				}
				$test = $data['source']['listener'];
				// Fix Listeners arrays !
				if (is_array($test)) { // Check if there is listeners
					unset($data['source']['listener']);
					if ($test['0'] != "") {

						$data['LISTENERS'] = $test;

					} else {

						$data['LISTENERS']['0'] = $test;

					}
				} else { $data['LISTENERS'] = array(); }

				// Change array keys of listeners to match shoutcast output
				$listeners = count($data['LISTENERS']);
				for ($i = 0; $i <= $listeners-1; $i++) {

					// Fix for Icecast KH8+
					if (is_array($data['LISTENERS'][$i]['@attributes'])) {
						unset($data['LISTENERS'][$i]['@attributes']);
					}

					// Icecast KH8+ Lag parse
					if (isset($data['LISTENERS'][$i]['lag'])) {
						$data['LISTENERS'][$i]['LAG'] = $data['LISTENERS'][$i]['lag'];
						unset($data['LISTENERS'][$i]['lag']);
					}

					// Rest of parse
					$data['LISTENERS'][$i]['HOSTNAME']    = $data['LISTENERS'][$i]['IP'];
					$data['LISTENERS'][$i]['USERAGENT']   = $data['LISTENERS'][$i]['UserAgent'];
					$data['LISTENERS'][$i]['CONNECTTIME'] = $data['LISTENERS'][$i]['Connected'];
					$data['LISTENERS'][$i]['UID']         = $data['LISTENERS'][$i]['ID'];

					unset($data['LISTENERS'][$i]['IP'], $data['LISTENERS'][$i]['UserAgent'], $data['LISTENERS'][$i]['Connected'], $data['LISTENERS'][$i]['ID']);
				}

				// Now the third part, we create a new array from old one, with details we need!
				$streamStats = array();

				if (empty($data['source'][1]) AND !empty($mount)) { // Fix for multiple Icecast streams (streamS$streamStats 1.01)

					// Added fix for listeners because Kh8+ has array insetead of string (WTF DEVS???)
					$streamStats['listeners']        = ((is_array($data['source']['listeners'])) ? $data['source']['listeners'][0] : $data['source']['listeners']);

					$streamStats['peak']             = $data['source']['listener_peak'];
					$streamStats['bitrate']          = ($data['source']['audio_bitrate'] / 1000);
					$streamStats['title']        = $data['source']['title'];
					$streamStats['artist']        = isSet($data['source']['artist'])? $data['source']['artist'] : null;
					$streamStats['path']             = '/' . preg_replace('/http:\/\/(.*)?\//s', '', $data['source']['listenurl']);

				} else {

					foreach ($data['source'] as $key => $data) {

						if (isset($data["@attributes"]["mount"]) && $data["@attributes"]["mount"] == "/{$mount}") {
							$streamStats['listeners']        = $data['source'][$key]['listeners'];
							$streamStats['peak']             = $data['source'][$key]['listener_peak'];
							$streamStats['bitrate']          = ($data['source'][$key]['audio_bitrate'] / 1000);
                            $streamStats['title']        = $data['source'][$key]['title'];
                            $streamStats['artist']        = isSet($data['source'][$key]['artist'])? $data['source'][$key]['artist'] : null;
							$streamStats['path']             = '/' . preg_replace('/http:\/\/(.*)?\//s', '', $data['source'][$key]['listenurl']);
						}
					}

				}

				$streamStats['avg']              = '0';
				$streamStats['hits']             = $data['listener_connections'];
				$streamStats['status']           = (($data['0'] == 'Source does not exist' OR $data['0'] == '<b>mount does not exist</b>') ? '0' : '1'); // Another newer Icecast fix...
				$streamStats['listeners-list']   = $data['LISTENERS'];
			}

			return new StreamResource((object)$streamStats);

		}

		// Very simple trick using json to ease up XML -> array with named keys instead of numbers.
		static function xml2array($data, $lower = false) {

			if ($lower != false) {
				$vals = json_decode(strtolower(json_encode((array)simplexml_load_string($data))),true);
			} else {
				$vals = json_decode(json_encode((array)simplexml_load_string($data)),true);
			}

			return $vals;

		}


	}
?>