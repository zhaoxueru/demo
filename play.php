<?php

/**
 * Socket extension test script
 *
 * Please view comments in every method
 */

namespace demo;

use ext\sock;

class play
{
    //Server
    const host = 'api.nervsys.com';
    const port = 62000;

    /**
     * API Safe Key
     *
     * @var array
     */
    public static $key = [
        'tcp_server' => [],
        'tcp_sender' => []
    ];

    /**
     * TCP Server
     * run with cmd: "php api.php demo/play-tcp_server"
     */
    public static function tcp_server(): void
    {
        sock::$type = 'tcp:server';
        sock::$host = '0.0.0.0';
        sock::$port = self::port;
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

            $data = [];
            $connected = array_keys($msg);

            //example: from message and send back to client
            foreach ($msg as $key => $value) {
                foreach ($connected as $c) {
                    if ($key !== $c) {
                        //Regroup data
                        $data[$key]['sock'] = $client[$key];
                        //Message to be sent
                        $data[$key]['msg'] = $value['msg'];
                    } else {
                        //Regroup data
                        $data[$key]['sock'] = $client[$key];
                        //Message to be sent
                        $data[$key]['msg'] = 'OK! ' . count($connected) . ' players are online waiting!';
                    }
                }
            }

            //Send data back and maintain clients
            $result = sock::write($data, $client);

            var_dump($result);

        } while (true);
    }

    /**
     * TCP Sender
     * run with cmd: "php api.php demo/play-tcp_sender"
     */
    public static function tcp_sender(): void
    {
        //Set TCP server host address and port
        sock::$host = self::host;
        sock::$port = self::port;

        //Set Socket type to 'tcp:sender'
        sock::$type = 'tcp:sender';

        //Create Socket (sender)
        $ok = sock::create();

        if (!$ok) exit('TCP Sender creation failed!');

        do {
            //Data need to send
            //If no "host" and "sock" were set,
            //it'll be set to the sender itself
            $data = [];

            echo 'Please input your commands, we will send it to others: ';

            $data[] = ['msg' => fgets(STDIN)];

            //Send data to Server
            $result = sock::write($data);

            var_dump($result);

            //Listen to TCP port
            sock::listen();

            //Read data from server
            $msg = sock::read();

            var_dump($msg);

            if ('OK! ' === substr(current($msg)['msg'], 0, 4)) continue;

            echo 'Receive and run the msg? (y/n): ';

            $input = fgets(STDIN);

            if ('y' === strtolower(trim($input))) {
                exec(current($msg)['msg'], $out);
                var_dump($out);
            }
        } while (true);
    }
}