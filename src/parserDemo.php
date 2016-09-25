<?php

require 'lib/parser/parser.php';

$query = "SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost`,`shipped`],SUM(`orders`:`cost`) AS totalCost] WHERE `customers`.`orders`:(shipped == true) AND `customers`:(totalCost > 200)";

echo "Query:\n".$query."\n\n";

$parser = new Parser($query);

echo "Result:\n";
print_r($parser->getParsed());
