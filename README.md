# fruit
A simple demo module for NervSys

 * picker_1.php is a Namespace demo example
 * picker_2.php is a Non-Namespace demo example
 *
 * This is an example demo module for NervSys
 * It'll be showing you how to call methods atomically
 * As for a demo module, all needed comments will be written.
 *
 * EXAMPLE
 *
 * 1. Turn ENABLE_GET to true for better controlling via url GET. You can use POST also and don't need to turn it to true.
 * Go to your server via the example url bellow: (Remember the change host_address to your own ip/host)
 *
 * 2. Visit http://host_address/api.php?cmd=fruit/picker_1&color=yellow&smell=sweet
 * 3. Visit http://host_address/api.php?cmd=fruit/picker_1&color=yellow&smell=sweet&shape=pear
 *
 * 4. Get the differences
 *
 * The demo url is in loose style, which means all data-structure matched methods will be calling
 * If you want to try strict style, visit the example urls bellow with GET enabled: (Remember the change host_address to your own ip/host)
 *
 * http://host_address/api.php?cmd=fruit/picker_1-color-smell-guess&color=yellow&smell=sweet
 * http://host_address/api.php?cmd=fruit/picker_1-color-smell-shape-guess&color=yellow&smell=sweet&shape=pear
 *
 * You can also modified the original data and the request url, whatever. Do it as your own.
 * You can link the module to your database or other modules to finish huge project as also.
 *
 * Change "picker_1" to "picker_2", and you'll see the same results
 *
 * And you can do as follows, and you'll see both scripts worked as the same way
 * http://host_address/api.php?cmd=fruit/picker_1-fruit/picker_2&color=yellow&smell=sweet&shape=pear
 *
 * Just try you own while I don't have a documentation for Nervsys now. Sorry for that.
 * I need more help because it is not done yet...
