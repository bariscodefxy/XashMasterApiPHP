<?php

namespace xashmaster;

use xashmaster\Rcon;

class ServerQuery {

	public function getServerInfo($server_ip, $server_port = 27015) {
		$rcon = new Rcon();
		$rcon->Connect($server_ip, $server_port);
		return $rcon->ServerInfo();
	}

}