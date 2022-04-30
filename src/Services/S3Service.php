<?php

namespace Michaelruther95\LaravelFileHandler\Services;

use Storage, Str;

class S3Service {

    public static function upload ($file = null, $path = '', $public = false) {

        if (!request()->hasFile('file')) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'file_to_upload_not_found',
                    'title' => 'File To Upload Not Found',
                    'message' => 'The file you are trying to upload does not exist.'
                ]
            ];
        }

        $requestFile = request()->file($file);
        $fileDetails = $_FILES[$file];
        
        $fileType = $fileDetails['type'];
        $fileName = $fileDetails['name'];
        $fileExtension = $requestFile->extension();
        
        $storageFileName = Str::uuid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $fileExtension;
        $filePath = $storageFileName;
        if ($path) {
            $pathLastString = $path[strlen($path)-1];
            if ($pathLastString == '/')
                $filePath = $path . $storageFileName;
            else
                $filePath = $path . '/' . $storageFileName;
        }
        
        $fileToUpload = file_get_contents($requestFile);
        $response = $public ? Storage::disk('s3')->put($filePath, $fileToUpload, 'public') : Storage::disk('s3')->put($filePath, $fileToUpload);
        
        if (!$response) {
            return [
                'success' => false,
                'error' => [
                    'code' => 's3_upload_failed',
                    'title' => 'File Upload Failed',
                    'message' => 'The file you are trying to save failed to upload'
                ],
                'raw_response' => $response
            ];
        }

        return [
            'success' => true,
            'upload_response' => $response,
            'path' => $filePath,
            'file_details' => [
                'name' => $fileName,
                'type' => $fileType,
                'extension' => $fileExtension
            ]
        ];

    }

    public static function generateSignedURL ($path, $minutes) {
        $signedURL = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes($minutes));
        if (!$signedURL) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'signed_url_generation_failed',
                    'title' => 'Failed To Generate Signed URL',
                    'message' => 'The system failed to generated a signed URL for the file you are accessing.'
                ],
                'raw_response' => $signedURL
            ];
        }

        return [
            'success' => true,
            'signed_url' => $signedURL
        ];
    }

    public static function delete ($path) {
        $delete = Storage::disk('s3')->delete($path);
        if (!$delete) {
            return [
                'success' => false,
                'error' => [
                    'code' => 's3_delete_file_failed',
                    'title' => 'Failed To Delete File On S3 Bucket',
                    'message' => 'The system failed to delete the file on s3 bucket.'
                ],
                'raw_response' => $delete
            ];
        }


        return [
            'success' => true
        ];
    }

}