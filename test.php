<?php

/**
 * Crypt Extension TEST Script
 *
 * Author 秋水之冰 <27206617@qq.com>
 *
 * Copyright 2018 秋水之冰
 *
 * This file is part of NervSys.
 *
 * NervSys is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NervSys is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NervSys. If not, see <http://www.gnu.org/licenses/>.
 */

namespace demo;

use \ext\crypt;
use ext\mpc;
use \ext\redis_queue;
use \core\ctr\router;

class test
{
    public static $key = [
        'crypt' => [],

        'queue_start' => ['run'],
        'queue_test'  => ['value'],
        'queue_run'   => [],
        'queue'       => [],

        'mpc'      => [],
        'mpc_test' => ['mpc_value']
    ];

    /**
     * Check data equality
     *
     * @param string $name
     * @param array  $data
     */
    private static function chk_eq(string $name, array $data): void
    {
        echo $name . ': ' . ($data[0] === $data[1] ? 'PASSED!' : 'Failed! ' . (string)$data[0] . ' !== ' . (string)$data[1]);
        echo PHP_EOL;
    }

    /**
     * Check greater than
     *
     * @param string $name
     * @param array  $data
     */
    private static function chk_gt(string $name, array $data): void
    {
        echo $name . ': ' . ($data[0] > $data[1] ? 'PASSED!' : 'Failed! ' . $data[0] . ' <= ' . $data[1]);
        echo PHP_EOL;
    }

    /**
     * Check less than
     *
     * @param string $name
     * @param array  $data
     */
    private static function chk_lt(string $name, array $data): void
    {
        echo $name . ': ' . ($data[0] < $data[1] ? 'PASSED!' : 'Failed! ' . $data[0] . ' >= ' . $data[1]);
        echo PHP_EOL;
    }

    /**
     * Crypt tests
     */
    public static function crypt(): void
    {
        echo 'Crypt Test Starts:';
        echo PHP_EOL;
        echo 'Make sure to set the right path of "openssl.cnf" in "cfg.php"';
        echo PHP_EOL;
        echo 'You can provide your own "keygen" class script in "cfg.php"';
        echo PHP_EOL;
        echo PHP_EOL;

        $string = (string)mt_rand();

        $aes_key = forward_static_call([crypt::$keygen, 'create']);


        $enc = crypt::encrypt($string, $aes_key);
        $dec = crypt::decrypt($enc, $aes_key);
        self::chk_eq('encrypt/decrypt', [$string, $dec]);


        $rsa_key = crypt::rsa_keys();

        $enc = crypt::rsa_encrypt($string, $rsa_key['public']);
        $dec = crypt::rsa_decrypt($enc, $rsa_key['private']);
        self::chk_eq('rsa_encrypt(pub)/rsa_decrypt(pri)', [$string, $dec]);


        $enc = crypt::rsa_encrypt($string, $rsa_key['private']);
        $dec = crypt::rsa_decrypt($enc, $rsa_key['public']);
        self::chk_eq('rsa_encrypt(pri)/rsa_decrypt(pub)', [$string, $dec]);


        $enc = crypt::sign($string);
        $dec = crypt::verify($enc);
        self::chk_eq('sign/verify', [$string, $dec]);


        $enc = crypt::sign($string, $rsa_key['public']);
        $dec = crypt::verify($enc, $rsa_key['private']);
        self::chk_eq('sign(pub)/verify(pri)', [$string, $dec]);


        $enc = crypt::sign($string, $rsa_key['private']);
        $dec = crypt::verify($enc, $rsa_key['public']);
        self::chk_eq('sign(pri)/verify(pub)', [$string, $dec]);


        $hash = crypt::hash_pwd($string, $aes_key);
        $pwd_chk = crypt::check_pwd($string, $aes_key, $hash);
        self::chk_eq('hash_pwd/check_pwd', [$pwd_chk, true]);
    }

    /**
     * Queue main process
     *
     * php api.php -r result --cmd "demo/test-queue_start" --data "run"
     */
    public static function queue_start(): void
    {
        echo 'Queue main process now is running!';

        //Add child process command
        redis_queue::$cmd = 'demo/test-queue_run';

        //Start main process
        redis_queue::start();
    }

    /**
     * Queue child process
     */
    public static function queue_run(): void
    {
        redis_queue::run();
    }

    /**
     * Queue tests
     */
    public static function queue_test(): bool
    {
        return router::$data['value'];
    }

    /**
     * Queue tests
     */
    public static function queue(): void
    {
        echo 'Queue Test Starts:';
        echo PHP_EOL;
        echo 'The test will stop all running queue processes!';
        echo PHP_EOL;
        echo 'Stop queue processes...';
        echo PHP_EOL;

        redis_queue::stop();

        echo 'Now, start the main queue process!';
        echo PHP_EOL;
        echo PHP_EOL;

        echo 'Main process started? (y/n):';
        $input = trim(fgets(STDIN));

        if ('' === $input || 'n' === strtolower($input)) {
            echo PHP_EOL;
            echo 'Main process NOT started. Test now exits!';
            return;
        }

        $cmd = strtr(__CLASS__, '\\', '/') . '-queue_test';


        $add = redis_queue::add('test', ['cmd' => &$cmd, 'value' => true]);
        self::chk_gt('Queue Add', [$add, 0]);

        sleep(redis_queue::$idle_wait);

        $jobs = redis_queue::queue_list();
        self::chk_eq('Queue Process', [array_sum($jobs), 0]);


        $fail_rec = redis_queue::fail_list(0, 1)['len'];

        redis_queue::add('test', ['cmd' => &$cmd, 'value' => false]);

        sleep(redis_queue::$idle_wait);

        $fail_now = redis_queue::fail_list(0, 1)['len'];

        self::chk_eq('Queue fail check', [$fail_now - $fail_rec, 1]);


        $left = $jobs = 200;
        for ($i = 0; $i < $jobs; ++$i) redis_queue::add('test', ['cmd' => &$cmd, 'value' => true]);

        do {
            sleep(redis_queue::$idle_wait);
            if ($jobs < $left) $left = $jobs;
            $jobs = array_sum(redis_queue::queue_list());
        } while (0 < $jobs && $left > $jobs);

        self::chk_eq('Queue (200 jobs)', [$jobs, 0]);


        $left = $jobs = 1000;
        for ($i = 0; $i < $jobs; ++$i) redis_queue::add('test', ['cmd' => &$cmd, 'value' => true]);

        do {
            if ($jobs < $left) $left = $jobs;
            sleep(redis_queue::$idle_wait * 2);
            $jobs = array_sum(redis_queue::queue_list());
        } while (0 < $jobs && $left > $jobs);

        self::chk_eq('Queue (1000 jobs)', [$jobs, 0]);
    }

    /**
     * mpc Test
     */
    public static function mpc(): void
    {
        echo 'MPC Test Starts:';
        echo PHP_EOL;
        echo 'Make sure to configured the "cfg.ini" if needed!';
        echo PHP_EOL;
        echo PHP_EOL;

        $cmd = strtr(__CLASS__, '\\', '/') . '-mpc_test';

        $val = 'mpc_test';

        $int_val = 1800000;


        $time = microtime(true);
        mpc::begin();
        mpc::add($cmd, ['mpc_value' => $val]);
        $result = mpc::commit();
        echo 'Time Taken:' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;
        self::chk_eq('MPC (1 job)', [$result[0]['data'], $val]);
        echo PHP_EOL;


        $time = microtime(true);
        mpc::begin();

        $data = [];
        for ($i = 0; $i < 10; ++$i) {
            $data[$i] = 'test_' . $i;
            mpc::add($cmd, ['mpc_value' => $data[$i]]);
        }

        $result = mpc::commit();
        echo 'Time Taken:' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (10 jobs)', [$pass, true]);
        echo PHP_EOL;


        $time = microtime(true);
        mpc::begin();

        $data = [];
        for ($i = 0; $i < 20; ++$i) {
            $data[$i] = 'test_' . $i;
            mpc::add($cmd, ['mpc_value' => $data[$i]]);
        }

        $result = mpc::commit();
        echo 'Time Taken:' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (20 jobs)', [$pass, true]);
        echo PHP_EOL;


        $time = microtime(true);
        mpc::begin();

        $data = [];
        for ($i = 0; $i < 100; ++$i) {
            $data[$i] = 'test_' . $i;
            mpc::add($cmd, ['mpc_value' => $data[$i]]);
        }

        $result = mpc::commit();
        echo 'Time Taken:' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (100 jobs)', [$pass, true]);
        echo PHP_EOL;


        $time = microtime(true);
        mpc::begin();

        $data = [];
        for ($i = 1; $i <= 20; ++$i) {
            $data[] = $int_val;
            mpc::add($cmd, ['mpc_value' => $int_val]);
        }

        $result = mpc::commit();
        echo 'Time Taken:' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (20 sleep jobs: sleep for 1.8s * 20 = 36s)', [$pass, true]);
        echo PHP_EOL;


        $time = microtime(true);
        mpc::begin();

        $data = [];
        for ($i = 1; $i <= 100; ++$i) {
            $data[] = $int_val;
            mpc::add($cmd, ['mpc_value' => $int_val]);
        }

        $result = mpc::commit();
        echo 'Time Taken:' . round(microtime(true) - $time, 4) . 's';
        echo PHP_EOL;

        $pass = true;
        foreach ($data as $key => $value) {
            if (!isset($result[$key]) || $result[$key]['data'] !== $value) {
                $pass = false;
                break;
            }
        }

        self::chk_eq('MPC (100 sleep jobs: sleep for 1.8s * 100 = 180s)', [$pass, true]);
        echo PHP_EOL;
    }

    /**
     * mpc callable function for test
     *
     * @return string
     */
    public static function mpc_test(): string
    {
        if (is_int(router::$data['mpc_value'])) usleep(router::$data['mpc_value']);
        return router::$data['mpc_value'];
    }
}