<?php
/**
 * Created by PhpStorm.
 * User: sappy
 * Date: 18.06.17
 * Time: 19:20
 *
 * RPC is not secure so I decided to implement a simple intefrace to communicate
 * with geth over sockets.
 */

class SocketError extends Exception{
    //
}

class Ethereum {
    private $socket;
    private $buffer;
    public function __construct($ipc_location)
    {
        try {
            $this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
            if (!$this->socket) {
                throw new SocketError();
            }
        } catch (SocketError $e) {
            echo "socket_create socketerror";
        } catch (Exception $e) {
            echo "socket_create exception";
        }
        try {
            if (!socket_connect($this->socket, $ipc_location)) {
                throw new SocketError();
            }
        } catch (SocketError $e) {
            echo "socket_connect";
        }


    }
    /*
     * @sMsg string json message
     * @iLen integer recv msg len
     */
    public function send_msg($sMsg, $iLen)  {
        $jsonArray = json_decode($sMsg);
        if(!socket_set_block($this->socket)) {
            throw new SocketError();
        }
        if(!socket_send($this->socket, $sMsg, strlen($sMsg), MSG_EOF)) {
            throw new SocketError();
        }
        if(!socket_recv ( $this->socket , $this->buffer ,  $iLen ,MSG_WAITFORONE)) {
            throw new SocketError();
        }
        if(!socket_set_nonblock($this->socket)) {
            // Todo: Handle error
        }
        if(!socket_close($this->socket)) {
            // Todo: Handle error
        }
        return true;
    }
    private function getBuffer() {
        return $this->buffer;
    }
}