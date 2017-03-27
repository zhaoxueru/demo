<?php

/**
 * Class picker
 *
 * This is an example demo module for NervSys
 * It'll be showing you how to call methods atomically
 * As for a demo module, all needed comments will be written.
 *
 * USAGE
 *
 * 1. Turn ENABLE_GET to true for better controlling via url GET. You can use POST also and don't need to turn it to true.
 * Go to your server via the example url bellow: (Remember the change host_address to your own ip/host)
 *
 * 2. Visit http://host_address/api.php?format=json&cmd=fruit_picker/picker&color=yellow&smell=sweet
 * 3. Visit http://host_address/api.php?format=json&cmd=fruit_picker/picker&color=yellow&smell=sweet&shape=pear
 *
 * 4. Get the differences
 *
 * The demo url is in loose style, which means all data-structure matched methods will be calling
 * If you want to try strict style, visit the example urls bellow with GET enabled: (Remember the change host_address to your own ip/host)
 *
 * http://host_address/api.php?format=json&cmd=fruit_picker/picker,color,smell,guess&color=yellow&smell=sweet
 * http://host_address/api.php?format=json&cmd=fruit_picker/picker,color,smell,shape,guess&color=yellow&smell=sweet&shape=pear
 *
 * You can also modified the original data and the request url, whatever. Do it as your own.
 * You can link the module to your database or other modules to finish huge project as also.
 */
//The class name should be exactly the same as its file name
class picker
{
    //All variables should be static and use them as usual
    public static $data_1;
    protected static $data_2;
    private static $data_3;

    //Use const data as usual
    const data_4 = [];

    //Above are examples, not for use

    /**
     * I declare a list of fruits with some properties using const,
     * the structure is like the data just out of the database
     * And we need to make the property list simple, so that,
     * we suppose that, every property of a fruit only contains one value.
     * And the properties should all cross to each other's to make better sense.
     * Don't make strange properties, or, that'll make the simple demo too complex, though it can be done also.
     * That means, apple can be both green and red, but, we only take red as its color property
     *
     * In case of letting more people to know the name, we made the fruit names translated to Chinese
     */
    const fruits = [
        [
            'name' => 'apple 苹果',
            'color' => 'red',
            'size' => 'small',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'sweet'
        ],
        [
            'name' => 'pear 梨',
            'color' => 'yellow',
            'size' => 'small',
            'taste' => 'sweet',
            'shape' => 'pear',
            'smell' => 'sweet'
        ],
        [
            'name' => 'banana 香蕉',
            'color' => 'yellow',
            'size' => 'small',
            'taste' => 'sweet',
            'shape' => 'bar',
            'smell' => 'sweet'
        ],
        [
            'name' => 'watermelon 西瓜',
            'color' => 'green',
            'size' => 'big',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'none'
        ],
        [
            'name' => 'mango 芒果',
            'color' => 'yellow',
            'size' => 'small',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'sweet'
        ],
        [
            'name' => 'orange 桔子',
            'color' => 'yellow',
            'size' => 'small',
            'taste' => 'acidity',
            'shape' => 'round',
            'smell' => 'acidity'
        ],
        [
            'name' => 'pineapple 菠萝',
            'color' => 'yellow',
            'size' => 'medium',
            'taste' => 'acidity',
            'shape' => 'round',
            'smell' => 'acidity'
        ],
        [
            'name' => 'tomato 西红柿',
            'color' => 'red',
            'size' => 'small',
            'taste' => 'acidity',
            'shape' => 'round',
            'smell' => 'none'
        ],
        [
            'name' => 'grape 葡萄',
            'color' => 'purple',
            'size' => 'tiny',
            'taste' => 'sweet',
            'shape' => 'round',
            'smell' => 'none'
        ],
        [
            'name' => 'avocado 牛油果',
            'color' => 'green',
            'size' => 'small',
            'taste' => 'none',
            'shape' => 'round',
            'smell' => 'none'
        ]
    ];

    //Use to store the data of the correct format
    private static $data = [];
    private static $fruits = [];

    /**
     * Make an API Safe Zone for api calling
     *
     * The content format is "method name" => ["required_data_name_1", "required_data_name_2", "required_data_name_3", ...]
     * If a method need no required data, leave an empty array there like "method name" => [], or, it'll be ignored by api
     * NOTICE: Only put those required data name in the array, those data which are not required/null should not be put in the safe zone
     *
     * @var array
     */
    public static $api = [
        'color' => ['color'],
        'size' => ['size'],
        'taste' => ['taste'],
        'shape' => ['shape'],
        'smell' => ['smell'],
        'guess' => []//This method needs no data, leave an empty array here to allow it to be calling
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
        //Load the data_pool module as we need it, or, just ignore it because the api also loaded it.
        load_lib('core', 'data_pool');

        //We actually know the format we need, so, do it
        //Make a copy of original data
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
     * NOTICE: Remember, all callable methods should NOT pass variables, all variables stored in data_pool::$data
     */

    /**
     * For color
     */
    public static function color(): array
    {
        if (isset(data_pool::$data['color']) && isset(self::$data['color'][data_pool::$data['color']])) {
            self::$result['color'] = self::$data['color'][data_pool::$data['color']];
            $result = self::$data['color'][data_pool::$data['color']];
        } else $result = self::$result['color'] = [];
        return $result;
    }

    /**
     * For size
     */
    public static function size(): array
    {
        if (isset(data_pool::$data['size']) && isset(self::$data['size'][data_pool::$data['size']])) {
            self::$result['size'] = self::$data['size'][data_pool::$data['size']];
            $result = self::$data['size'][data_pool::$data['size']];
        } else $result = self::$result['size'] = [];
        return $result;
    }

    /**
     * For taste
     */
    public static function taste(): array
    {
        if (isset(data_pool::$data['taste']) && isset(self::$data['taste'][data_pool::$data['taste']])) {
            self::$result['taste'] = self::$data['taste'][data_pool::$data['taste']];
            $result = self::$data['taste'][data_pool::$data['taste']];
        } else $result = self::$result['taste'] = [];
        return $result;
    }

    /**
     * For shape
     */
    public static function shape(): array
    {
        if (isset(data_pool::$data['shape']) && isset(self::$data['shape'][data_pool::$data['shape']])) {
            self::$result['shape'] = self::$data['shape'][data_pool::$data['shape']];
            $result = self::$data['shape'][data_pool::$data['shape']];
        } else $result = self::$result['shape'] = [];
        return $result;
    }

    /**
     * For smell
     */
    public static function smell(): array
    {
        if (isset(data_pool::$data['smell']) && isset(self::$data['smell'][data_pool::$data['smell']])) {
            self::$result['smell'] = self::$data['smell'][data_pool::$data['smell']];
            $result = self::$data['smell'][data_pool::$data['smell']];
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
