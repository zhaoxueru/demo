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

            //example: from message and send back to client
            foreach ($client as $k => $v) {
                foreach ($msg as $key => $value) {
                    $data[$k]['sock'] = $v;
                    $data[$k]['msg'] = $key !== $k ? $value['msg'] : 'OK! ' . count($client) . ' players are online waiting!';
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
            $data = [];

            echo 'Please input your commands, we will send it to others: ';

            $msg = fgets(STDIN);

            $data[] = ['msg' => $msg];

            //Send data to Server
            $result = sock::write($data);

            echo PHP_EOL . '============================================' . PHP_EOL;
            echo $result[0] ? 'Message: "' . trim($msg) . '" sent successfully!' : 'Send failed!';
            echo PHP_EOL . '============================================' . PHP_EOL;

            //Listen to TCP port
            sock::listen();

            //Read data from server
            $msg = sock::read();

            $received = current($msg)['msg'];

            $list = false !== strpos($received, PHP_EOL) ? explode(PHP_EOL, $received) : [$received];

            $list = array_filter($list);

            if (empty($list)) continue;

            echo PHP_EOL . 'Wow, you have messages unread!' . PHP_EOL;
            echo PHP_EOL . '↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓' . PHP_EOL;

            foreach ($list as $cmd) {
                $cmd = trim($cmd);

                if ('OK! ' === substr($cmd, 0, 4)) {
                    echo PHP_EOL . $cmd . PHP_EOL . PHP_EOL;
                    continue;
                }

                echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' . PHP_EOL;
                echo $cmd . PHP_EOL;
                echo '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!' . PHP_EOL . PHP_EOL;
                echo '!!!Do NOT execute the command that you don\'t know!!!' . PHP_EOL;
                echo 'Execute the messages? (y/n): ';
                echo PHP_EOL . PHP_EOL;

                $input = fgets(STDIN);

                if ('y' === strtolower(trim($input))) {
                    exec($cmd, $out);
                    echo 'Executed. The command shows:' . PHP_EOL;
                    echo '============================================' . PHP_EOL;
                    var_dump($out);
                    echo '============================================' . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
                }
            }
        } while (true);
    }
}