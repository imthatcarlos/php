<?php namespace App\Service;

use Illuminate\Support\Facades\App;

// This class stores, retrives, and deletes objects stored in Amazon S3

class AmazonS3Client
{

  public static function storeSourceFile($filename, $order_id)
  {
    $client = App::make('aws')->createClient('s3');
    $source_file = public_path() . "/uploads/" . $filename;
    $key = 'order-' . $order_id . '/' . $filename;

    $client->putObject([
      'Bucket'     => env("AWS_SOURCES_BUCKET"),
      'Key'        => $key,
      'Body'       => fopen($source_file, 'r+')
    ]);

    return $key;
  }

  public static function getSourceFileUrl($key)
  {
    $client = App::make('aws')->createClient('s3');
    $cmd = $client->getCommand('GetObject', [
      'Bucket' => env('AWS_SOURCES_BUCKET'),
      'Key'    => $key
    ]);
    $request = $client->createPresignedRequest($cmd, '+1 minutes');
    $presignedUrl = (string) $request->getUri();

    return $presignedUrl;
  }

  public static function storeCompletedMovie($filename, $order_id)
  {
    $client = App::make('aws')->createClient('s3');
    $source_file = public_path() . "/uploads/" . $filename;
    $key = 'order_id-' . $order_id . '/' . $filename;

    $client->putObject( [
      'Bucket'     => env("AWS_COMPLETED_BUCKET"),
      'Key'        => $key,
      'Body'       => fopen($source_file, 'r+')
    ]);
    return $key;
  }

  public static function getCompletedMovieUrl($key)
  {
    $client = App::make('aws')->createClient('s3');
    $cmd = $client->getCommand('GetObject', [
      'Bucket' => env('AWS_COMPLETED_BUCKET'),
      'Key'    => $key
    ]);
    $request = $client->createPresignedRequest($cmd, '+10 seconds');
    $presignedUrl = (string) $request->getUri();
    return $presignedUrl;
  }

  public static function deleteSourceFile($key)
  {
    $client = App::make('aws')->createClient('s3');
    $client->deleteObject([
      'Bucket'     => env("AWS_SOURCES_BUCKET"),
      'Key'        => $key,
    ]);
  }
}