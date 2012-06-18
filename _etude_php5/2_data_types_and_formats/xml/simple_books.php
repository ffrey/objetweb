<?php
$file = 'books.xml';
$xml = simplexml_load_file($file); 

echo 'price of Midnight Rain'.PHP_EOL;

$price = $xml->xpath('/catalog/book[@id="bk101"]/price');
var_dump((float) $price[0] );

$price = $xml->book[0]->price;
var_dump((float) $price);

$books = $xml->book;
var_dump('nb of books', count($books), get_class($books) );
foreach ($books AS $b)
{
printf('title : %s'.PHP_EOL, $b->title);
}
