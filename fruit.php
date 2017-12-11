<?php

/**
 * Fruit Demo
 *
 * This is an example demo module for NervSys
 * It'll be showing you how to call methods atomically
 * As for a demo module, all needed comments will be written.
 *
 * USAGE
 *
 * 1. Enable "DEBUG" in cfg.php for better showing in debug mode
 * Go to your server via the example url bellow: (Remember the change host_address to your own ip/host)
 *
 * 2. Visit http://host_address/api.php?cmd=demo/fruit&color=yellow&smell=sweet
 * 3. Visit http://host_address/api.php?cmd=demo/fruit&color=yellow&smell=sweet&shape=pear
 *
 * 4. Get the differences
 *
 * The demo url is in loose style, which means all data-structure matched methods will be calling
 * If you want to try strict style, visit the example urls bellow with GET enabled: (Remember the change host_address to your own ip/host)
 *
 * http://host_address/api.php?cmd=demo/fruit-color-smell-guess&color=yellow&smell=sweet
 * http://host_address/api.php?cmd=demo/fruit-color-smell-shape-guess&color=yellow&smell=sweet&shape=pear
 *
 * In CLI mode, you can input as follows:
 *
 * php api.php -r result -c "demo/fruit" -d "color=yellow&smell=sweet&shape=pear"
 * php api.php -r result -l -c "demo/fruit" -d "color=yellow&smell=sweet&shape=pear"
 * php api.php -r result -l -c "demo/fruit-color-smell-shape-guess" -d "color=yellow&smell=sweet&shape=pear"
 *
 * or, any kind of these. Just see the comment in /core/ctr/router/cli.php, begin from line 72 to 82
 * for example: "c" can be written in "cmd", "d" can be "data", and more.
 *
 * You can also modified the original data and the request url, whatever. Do it as your own.
 * You can link the module to your database or other modules to finish huge project as also.
 */

//This is a Namespace demo example

namespace demo;

use \core\ctr\router as router;

//The class name should be exactly the same as the file name
class fruit
{
    /**
     * I declare a list of fruits with some properties using const,
     * the structure is like the data just out of the DB
     * And we need to make the property list simple, so that,
     * we suppose that, every property of a fruit only contains one value.
     * And the properties should all cross to each other's to make better sense.
     * Don't make strange properties, or, that'll make the simple demo too complex, though it can be done also.
     * That means, apple can be both green and red, but, we only take red as its color property
     */
    const fruits = [
        [
            'name'  => 'apple',
            'color' => 'red',
            'size'  => 'small',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'sweet'
        ],
        [
            'name'  => 'pear',
            'color' => 'yellow',
            'size'  => 'small',
            'taste' => 'sweet',
            'shape' => 'pear',
            'smell' => 'sweet'
        ],
        [
            'name'  => 'banana',
            'color' => 'yellow',
            'size'  => 'small',
            'taste' => 'sweet',
            'shape' => 'bar',
            'smell' => 'sweet'
        ],
        [
            'name'  => 'watermelon',
            'color' => 'green',
            'size'  => 'big',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'none'
        ],
        [
            'name'  => 'mango',
            'color' => 'yellow',
            'size'  => 'small',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'sweet'
        ],
        [
            'name'  => 'orange',
            'color' => 'yellow',
            'size'  => 'small',
            'taste' => 'acidity',
            'shape' => 'round',
            'smell' => 'acidity'
        ],
        [
            'name'  => 'pineapple',
            'color' => 'yellow',
            'size'  => 'medium',
            'taste' => 'acidity',
            'shape' => 'round',
            'smell' => 'acidity'
        ],
        [
            'name'  => 'tomato',
            'color' => 'red',
            'size'  => 'small',
            'taste' => 'acidity',
            'shape' => 'round',
            'smell' => 'none'
        ],
        [
            'name'  => 'grape',
            'color' => 'purple',
            'size'  => 'tiny',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'none'
        ],
        [
            'name'  => 'avocado',
            'color' => 'green',
            'size'  => 'small',
            'taste' => 'none',
            'shape' => 'round',
            'smell' => 'none'
        ]
    ];

    //Use to store the data of the correct format
    private static $data   = [];
    private static $fruits = [];

    /**
     * Make an API Safe Key for api calling
     *
     * The content format is "method name" => ["required_data_name_1", "required_data_name_2", "required_data_name_3", ...]
     * If a method need no required data, leave an empty array there like "method name" => [], or, it'll be ignored by api
     * NOTICE: Only put those required data name in the array, those data which are not required/null should not be put in the safe zone
     *
     * @var array
     */
    public static $key = [
        'sock'  => [],
        'color' => ['color'],
        'size'  => ['size'],
        'taste' => ['taste'],
        'shape' => ['shape'],
        'smell' => ['smell'],
        'guess' => []
        //This method needs no data, leave an empty array here to allow it to be calling
    ];

    //Store the result for every method
    private static $result = [];

    /**
     * This is the first calling method in a class via api request without API Safe Zone checking
     * Technically, just use it doing some preparations
     * Don't use it to do important processes
     * If you don't need init function, just don't write it.
     * You module can be fully functional without init.
     * It is not required strictly.
     *
     * We use it to restructure the data to the format we need
     */
    public static function init()
    {
        //We actually know the format we need, so, do it
        //Make a copy of original data

        $name = '';
        $raw_data = self::fruits;

        //Go over the list deeply
        foreach ($raw_data as $values) {
            foreach ($values as $key => $value) {
                //Regrouping
                if ('name' === $key) {
                    self::$fruits[] = $value;//Stored the fruit
                    $name = $value;//get the fruit's name
                } else {
                    //properties go here
                    if (!isset(self::$data[$key])) self::$data[$key][$value][] = $name;//for new property
                    else self::$data[$key][$value][] = $name;//for existed property
                }
            }
        }
        //We now should get the data with formatted structure in self::$data
    }

    /**
     * Methods bellow are processing single property, you can rewrite them shortly in one function
     * Here, we just show you that, every method is highly separated from each other.
     * NOTICE: Remember, all callable methods should NOT pass variables, all variables stored in abstract.router::$data
     */

    /**
     * For color
     */
    public static function color(): array
    {
        if (isset(router::$data['color']) && isset(self::$data['color'][router::$data['color']])) {
            self::$result['color'] = self::$data['color'][router::$data['color']];
            $result = self::$data['color'][router::$data['color']];
        } else $result = self::$result['color'] = [];
        return $result;
    }

    /**
     * For size
     */
    public static function size(): array
    {
        if (isset(router::$data['size']) && isset(self::$data['size'][router::$data['size']])) {
            self::$result['size'] = self::$data['size'][router::$data['size']];
            $result = self::$data['size'][router::$data['size']];
        } else $result = self::$result['size'] = [];
        return $result;
    }

    /**
     * For taste
     */
    public static function taste(): array
    {
        if (isset(router::$data['taste']) && isset(self::$data['taste'][router::$data['taste']])) {
            self::$result['taste'] = self::$data['taste'][router::$data['taste']];
            $result = self::$data['taste'][router::$data['taste']];
        } else $result = self::$result['taste'] = [];
        return $result;
    }

    /**
     * For shape
     */
    public static function shape(): array
    {
        if (isset(router::$data['shape']) && isset(self::$data['shape'][router::$data['shape']])) {
            self::$result['shape'] = self::$data['shape'][router::$data['shape']];
            $result = self::$data['shape'][router::$data['shape']];
        } else $result = self::$result['shape'] = [];
        return $result;
    }

    /**
     * For smell
     */
    public static function smell(): array
    {
        if (isset(router::$data['smell']) && isset(self::$data['smell'][router::$data['smell']])) {
            self::$result['smell'] = self::$data['smell'][router::$data['smell']];
            $result = self::$data['smell'][router::$data['smell']];
        } else $result = self::$result['smell'] = [];
        return $result;
    }

    /**
     * Now we need to get out what you may exactly want
     */

    public static function guess()
    {
        //Make a copy of all fruits
        $wanted = self::$fruits;

        //Get the property intersected list recursively
        foreach (self::$result as $value) $wanted = array_intersect($wanted, $value);

        //You can show the guessed result data via var_dump here
        //var_dump($wanted);

        //Return the result
        return $wanted;
    }
}