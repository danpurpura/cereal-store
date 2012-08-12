Cereal Store
============

A simple key-value store that easily serializes to a base64 encoded string. It's
especially useful for storing settings for a web app as part of the URL.

Implements Iterator, ArrayAccess, and Countable, so it can be used like an array.

Usage
-----

```php
$store = new CerealStore();
$store->add('key', 'value'); // or $store['key'] = 'value';
echo $store->has('key')."\n";
echo $store->get('key')."\n"; // or echo $store['key'];
echo $store; // automatically serializes to: q1bKTq1UslIqS8wpTVWqBQA=
```

Example
-------

See example.php for an example on how to use the store on a web app for storing
a user's settings.