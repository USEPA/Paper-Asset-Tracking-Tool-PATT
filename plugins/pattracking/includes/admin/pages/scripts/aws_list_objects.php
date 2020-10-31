<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucket = 'cg-93218e1b-b84f-4f89-8749-31d182676000';

// Instantiate the client.
		$s3 = new Aws\S3\S3Client([
			'region'  => 'us-gov-west-1',
			'version' => 'latest',
			'credentials' => [
				'key'    => "AKIAR7FXZINYJOKVCEU3",
				'secret' => "oFwMhaos/cPZGKyHP3YIc706yf3O8VbECkflTkwm",
			]
		]);

// Use the high-level iterators (returns ALL of your objects).
try {
    $results = $s3->getPaginator('ListObjects', [
        'Bucket' => $bucket
    ]);

    foreach ($results as $result) {
        foreach ($result['Contents'] as $object) {
            echo $object['Key'] . PHP_EOL . '<br />';
        }
    }
} catch (S3Exception $e) {
    echo $e->getMessage() . PHP_EOL . '<br />';
}