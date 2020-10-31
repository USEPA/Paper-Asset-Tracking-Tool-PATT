<?php

require 'vendor/autoload.php';


use Aws\S3\S3Client;

use Aws\S3\Exception\S3Exception;

$bucket = 'cg-93218e1b-b84f-4f89-8749-31d182676000';
$keyname = '1602616356_ezdesktop_2_0_98146.pdf';


		$s3 = new Aws\S3\S3Client([
			'region'  => 'us-gov-west-1',
			'version' => 'latest',
			'credentials' => [
				'key'    => "AKIAR7FXZINYJOKVCEU3",
				'secret' => "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",
			]
		]);	
		
// 1. Delete the object from the bucket.

try
{
    echo 'Attempting to delete ' . $keyname . '...' . PHP_EOL;

    $result = $s3->deleteObject([
        'Bucket' => $bucket,
        'Key'    => $keyname
    ]);

var_dump($result);

/*    if ($result['DeleteMarker'])
    {
        echo $keyname . ' was deleted or does not exist.' . PHP_EOL;
    } else {
        exit('Error: ' . $keyname . ' was not deleted.' . PHP_EOL);
    }
*/
}
catch (S3Exception $e) {
    exit('Error: ' . $e->getAwsErrorMessage() . PHP_EOL);
}

// 2. Check to see if the object was deleted.

try
{
    echo 'Checking to see if ' . $keyname . ' still exists...' . PHP_EOL;

    $result = $s3->getObject([
        'Bucket' => $bucket,

        'Key'    => $keyname
    ]);

    echo 'Error: ' . $keyname . ' still exists.';
}
catch (S3Exception $e) {
    exit($e->getAwsErrorMessage());
} 