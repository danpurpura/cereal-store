Cereal Store
============

A simple key-value store that easily serializes to a base64 encoded string. It's
especially useful for storing settings for a web app as part of the URL.

Implements Iterator, ArrayAccess, and Countable, so you can use it like an array.

Usage
-----

$store = new CerealStore();
$store->add('key', 'value');
echo $store; // __toString automatically serializes
