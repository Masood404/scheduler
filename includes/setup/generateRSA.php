<?php

// Define an array with the configuration settings for the keys to be generated.
$config = array(
    "digest_alg" => "sha512", // specifies the hash function to use
    "private_key_bits" => 2048, // specifies the size of the private key to be generated
    "private_key_type" => OPENSSL_KEYTYPE_RSA, // specifies the type of the private key to be generated (OPENSSL_KEYTYPE_RSA stands for RSA key).
);

// Generate a new private and public key pair using the defined configuration settings.
// The openssl_pkey_new() function returns a resource that holds the key pair.
$res = openssl_pkey_new($config);

// Extract the private key from the generated key pair.
// The openssl_pkey_export() function extracts the private key as a string and stores it in the $privKey variable.
openssl_pkey_export($res, $privKey);

// Extract the public key from the generated key pair.
// The openssl_pkey_get_details() function returns an array with the key details, including the public key.
// Here, we're interested in the 'key' element of the array, which holds the public key.
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey["key"];

return[
    "publicKey" => $pubKey,
    "privateKey" => $privKey
];
?>