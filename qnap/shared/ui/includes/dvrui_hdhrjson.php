<?php

class DVRUI_HDHRjson {
	private $myhdhrurl = 'http://ipv4.my.hdhomerun.com/discover';
	private $hdhrkey_devID = 'DeviceID';
	private $hdhrkey_localIP = 'LocalIP';
	private $hdhrkey_baseURL = 'BaseURL';
	private $hdhrkey_discoverURL = 'DiscoverURL';
	private $hdhrkey_lineupURL = 'LineupURL';
	private $hdhrkey_modelNum = 'ModelNumber';
	private $hdhrkey_modelName = 'FriendlyName';
	private $hdhrkey_fwVer = 'FirmwareVersion';
	private $hdhrkey_fwName = 'FirmwareName';
	private $hdhrkey_tuners= 'TunerCount';
	
	private $hdhrlist = array();
	private $hdhrlist_key_channelcount = 'ChannelCount';
	
	public function DVRUI_HDHRjson() {
		$json = file_get_contents($this->myhdhrurl);
		$hdhr_data = json_decode($json, true);
		for ($i=0;$i<count($hdhr_data);$i++) {
			$hdhr = $hdhr_data[$i];
			$hdhr_base = $hdhr[$this->hdhrkey_baseURL];
			
			if ($hdhr[$this->hdhrkey_discoverURL] == null) {
				// Skip this HDHR - it doesn't support the newer HTTP interface
				// for DVR
				continue;
			}
			
			$hdhr_info_json = file_get_contents($hdhr[$this->hdhrkey_discoverURL]);
			$hdhr_info = json_decode($hdhr_info_json, true);
			$hdhr_lineup_json = file_get_contents($hdhr[$this->hdhrkey_lineupURL]);
			$hdhr_lineup = json_decode($hdhr_lineup_json, true);
			$this->hdhrlist[] = array( $this->hdhrkey_devID => $hdhr[$this->hdhrkey_devID],
										$this->hdhrkey_modelNum => $hdhr_info[$this->hdhrkey_modelNum],
										$this->hdhrlist_key_channelcount => count($hdhr_lineup),
										$this->hdhrkey_baseURL => $hdhr_base,
										$this->hdhrkey_lineupURL => $hdhr[$this->hdhrkey_lineupURL],
										$this->hdhrkey_modelName =>$hdhr_info[$this->hdhrkey_modelName],
										$this->hdhrkey_fwVer => $hdhr_info[$this->hdhrkey_fwVer],
										$this->hdhrkey_tuners => $hdhr_info[$this->hdhrkey_tuners],
										$this->hdhrkey_fwName => $hdhr_info[$this->hdhrkey_fwName]);
		}
	}
	
	public function device_count() {
		return count($this->hdhrlist);
	}

	public function get_device_info($pos) {
		$device = $this->hdhrlist[$pos];
		return ' DeviceID: ' . $device[$this->hdhrkey_devID] 
		          . ' Model Number: ' . $device[$this->hdhrkey_modelNum] 
		          . ' Channels: ' . $device[$this->hdhrlist_key_channelcount] . ' ';
	}
	
	public function get_device_id($pos) {
		$device = $this->hdhrlist[$pos];
		return $device[$this->hdhrkey_devID];
	}

	public function get_device_model($pos) {
		$device = $this->hdhrlist[$pos];
		return $device[$this->hdhrkey_modelNum];
	}

	public function get_device_channels($pos) {
		$device = $this->hdhrlist[$pos];
		return $device[$this->hdhrlist_key_channelcount];
	}

	public function get_device_lineup($pos) {
		$device = $this->hdhrlist[$pos];
		return $device[$this->hdhrkey_lineupURL];
	}

	public function get_device_baseurl($pos) {
		$device = $this->hdhrlist[$pos];
		return $device[$this->hdhrkey_baseURL];
	}

	public function get_device_firmware($pos) {
		$device = $this->hdhrlist[$pos];
		return $device[$this->hdhrkey_fwVer];
	}

	public function get_device_tuners($pos) {
		$device = $this->hdhrlist[$pos];
		return $device[$this->hdhrkey_tuners];
	}
}
?>
