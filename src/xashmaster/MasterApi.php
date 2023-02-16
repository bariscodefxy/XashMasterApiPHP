<?php

/**
 * Copyright 2023 bariscodefx
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

declare(strict_types=1);

namespace xashmaster;

use xashmaster\ByteBuffer;
use xashmaster\Parts\SocketBase;

/**
 * MasterApi
 */
class MasterApi extends SocketBase
{
    /**
     * connect
     *
     * @param string $ip
     * @param integer $port
     * @return void
     */
    public function connect($ip = "ms.xash.su", $port = 27010)
    {
        parent::connect($ip, $port);
    }

    /**
     * getServers
     *
     * @param string $game
     * @return array|null
     */
    public function getServers($game = "cstrike"): ?array
    {
        $msg = "1\xff0.0.0.0:0\x00\\nat\\0\\gamedir\\$game\\clver\\0.19.3\x00";
        socket_send($this->socket, $msg, strlen($msg), 0);
        $read = socket_read($this->socket, 1024);
        $servers = [];
        $byteBuffer = ByteBuffer::wrap($read);
        $this->contentData = $byteBuffer;
        do {
            $firstOctet = $this->contentData->getByte();
            $secondOctet = $this->contentData->getByte();
            $thirdOctet = $this->contentData->getByte();
            $fourthOctet = $this->contentData->getByte();
            $portNumber = $this->contentData->getShort();
            $portNumber = (($portNumber & 0xFF) << 8) + ($portNumber >> 8);

            $servers[] = "$firstOctet.$secondOctet.$thirdOctet.$fourthOctet:$portNumber";
        } while ($this->contentData->remaining() > 0);
        array_shift($servers);
        array_pop($servers);
        return $servers;
    }
}
