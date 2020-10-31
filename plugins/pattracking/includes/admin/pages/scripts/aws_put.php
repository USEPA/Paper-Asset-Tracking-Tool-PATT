<?php
	if(isset($_FILES['image'])){
		$file_name = $_FILES['image']['name'];   
		$temp_file_location = $_FILES['image']['tmp_name']; 

		require 'vendor/autoload.php';

		$s3 = new Aws\S3\S3Client([
			'region'  => 'us-gov-west-1',
			'version' => 'latest',
			'credentials' => [
				'key'    => "AKIAR7FXZINYJOKVCEU3",
				'secret' => "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",
			]
		]);		

		$result = $s3->putObject([
			'Bucket' => 'cg-93218e1b-b84f-4f89-8749-31d182676000',
			'Key'    => $file_name,
			'SourceFile' => $temp_file_location			
		]);

		var_dump($result);
	}
?>

<form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">         
	<input type="file" name="image" />
	<input type="submit"/>
</form>