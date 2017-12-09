<?php

/**
 * Socket extension test script
 *
 * Please view comments in every method
 */

namespace demo\socket;

use \ext\sock as sock;

class server
{
    /**
     * API Safe Key
     *
     * @var array
     */
    public static $key = [
        'tcp_start'  => [],
        'udp_start'  => [],
        'http_start' => [],
        'tcp_send'   => [],
        'udp_send'   => []
    ];

    /**
     * TCP Server
     * run with cmd: "php api.php demo/socket/server-tcp_start"
     */
    public static function tcp_start(): void
    {
        sock::$type = 'tcp:server';
        sock::$host = '0.0.0.0';
        sock::$port = 1000;
        $ok = sock::create();

        if (!$ok) exit('TCP Server creation failed!');

        //Set Client list alone
        $client = [];

        do {
            //Copy client list to read list
            $read = $client;

            //Listen to TCP port
            sock::listen($read);

            //Accept new connection
            sock::accept($read, $client);

            //Read TCP Data
            $msg = sock::read($read, $client);

            var_dump($msg);

            //Regroup data
            $data = [];

            //example: from message and send back to client
            foreach ($msg as $key => $value) {
                //Client socket resource
                $data[$key]['sock'] = $client[$key];
                //Message to be sent
                $data[$key]['msg'] = $value['msg'];
            }

            //Send data back and maintain clients
            $result = sock::write($data, $client);

            var_dump($result);

        } while (true);
    }

    /**
     * UDP Server
     * run with cmd: "php api.php demo/socket/server-udp_start"
     */
    public static function udp_start(): void
    {
        sock::$type = 'udp:server';
        sock::$host = '0.0.0.0';
        sock::$port = 2000;
        $ok = sock::create();

        if (!$ok) exit('UDP Server creation failed!');

        //Set Client list alone
        $client = [];

        do {
            //Listen to UDP port
            sock::listen($client);

            //Read UDP Data
            $msg = sock::read($client);

            var_dump($msg);

        } while (true);
    }

    /**
     * HTTP Server
     * run with cmd: "php api.php demo/socket/server-http_start"
     */
    public static function http_start(): void
    {
        sock::$type = 'http:server';
        sock::$host = '0.0.0.0';
        sock::$port = 80;
        $ok = sock::create();

        if (!$ok) exit('HTTP Server creation failed!');

        do {
            //Reset all clients
            $read = $client = [];

            //Accept new connection
            sock::accept($read, $client);

            //Read HTTP Request data
            $msg = sock::read($client);

            var_dump($msg);

            //Simply send to browser
            $data[0]['sock'] = current($client);
            $data[0]['msg'] = 'Hello World! I am a simple HTTP Server build by PHP~';

            //Send data to client
            $result = sock::write($data);

            var_dump($result);

        } while (true);
    }

    /**
     * TCP Sender
     * run with cmd: "php api.php demo/socket/server-tcp_send"
     */
    public static function tcp_send(): void
    {
        //Set TCP server host address and port
        sock::$host = '127.0.0.1';
        sock::$port = 1000;

        //Set Socket type to 'tcp:sender'
        sock::$type = 'tcp:sender';

        //Create Socket (sender)
        $ok = sock::create();

        if (!$ok) exit('UDP Sender creation failed!');

        //Data need to send
        //If no "host" and "sock" were set,
        //it'll be set to the sender itself
        $data = [
            ['msg' => 'Hello, my TCP Server!'],
            ['msg' => 'Nice to meet you for the first time.'],
            ['msg' => 'How are you going?'],
            ['msg' => 'Have you received my messages?'],
            ['msg' => 'I need your response if you received my messages.'],
            ['msg' => 'Thanks!'],
        ];

        //Send data to Server
        $result = sock::write($data);

        var_dump($result);

        //Read data from server
        $msg = sock::read();

        var_dump($msg);
    }

    /**
     * UDP Sender
     * run with cmd: "php api.php demo/socket/server-udp_send"
     */
    public static function udp_send(): void
    {
        //Set UDP server host address and port
        sock::$host = '127.0.0.1';
        sock::$port = 2000;

        //Set Socket type to 'udp:sender'
        sock::$type = 'udp:sender';

        //Create Socket (sender)
        $ok = sock::create();

        if (!$ok) exit('UDP Sender creation failed!');

        //Data need to send
        //If no "host" and "sock" were set,
        //it'll be set to the sender itself
        $data = [
            ['msg' => 'Hello, my UDP Server!'],
            ['msg' => 'Nice to meet you for the first time.'],
            ['msg' => 'How are you going?'],
            ['msg' => 'Have you received my messages?'],
            ['msg' => 'Don\'t send back to me, because I have no server script running here.'],
        ];

        //Send data to Server
        $result = sock::write($data);

        var_dump($result);

        //You don't need to read from sender side,
        //because if the server send message back,
        //it'll be received by server side instead of a UDP Sender when using UDP
    }
}