<?php

namespace Michaelruther95\LaravelFileHandler\Services;

use Michaelruther95\LaravelFileHandler\Services\S3Service;
use Michaelruther95\LaravelFileHandler\Models\FileHandlerFile as FileMedia;
use Michaelruther95\LaravelFileHandler\Models\FileHandlerS3Upload as S3Upload;

class MediaService {

    public static function upload ($public = false, $fileIndex, $path = '', $type = 's3') {

        $upload = null;
        if ($type == 's3') {
            $upload = S3Service::upload($fileIndex, $path, $public);
            if (!$upload['success']) {
                return $upload;
            }

            $s3Media = new S3Upload();
            $s3Media->path = $upload['path'];
            $s3Media->signed_url = null;
            $s3Media->signed_url_expiry = null;
            $s3Media->public = $public;
            $s3Media->save();

            $media = new FileMedia();
            $media->file_name = $upload['file_details']['name'];
            $media->file_extension = $upload['file_details']['extension'];
            $media->file_type = $upload['file_details']['type'];
            $media->s3_upload_id = $s3Media->id ;
            $media->save();

            $mediaDetails = FileMedia::with('s3Media')
                ->where('id', $media->id)
                ->first();

            return [
                'success' => true,
                'media' => $mediaDetails
            ];
        }

        return [
            'success' => false,
            'error' => [
                'code' => 'upload_type_not_supported',
                'title' => 'Upload Type Not Supported',
                'message' => 'The upload type you are trying to use is currently not supported.'
            ]
        ];

    }

    public static function get ($id, $type = 'id', $expiryMinutes = 5) {
        $media = FileMedia::where($type, $id)->first();
        if (!$media) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'media_not_found',
                    'title' => 'Media Not Found',
                    'message' => 'The media you are trying to access could not be found'
                ]
            ];
        }

        if ($media->s3_upload_id) {
            $s3Media = S3Upload::where('id', $media->s3_upload_id)
                ->first();
                
            if ($s3Media) {
                if (!$s3Media->public) {
                    $generateSignedURL = false;
                    if (!$s3Media->signed_url || !$s3Media->signed_url_expiry || ($s3Media->signed_url_expiry && $s3Media->signed_url_expiry <= date('Y-m-d H:i:s'))) {
                        $generateResponse = S3Service::generateSignedURL($s3Media->path, $expiryMinutes);
                        if (!$generateResponse['success']) {
                            return $generateResponse;
                        }

                        $s3Media->signed_url = $generateResponse['signed_url'];
                        $s3Media->signed_url_expiry = date("Y-m-d H:i:s", strtotime("+" . $expiryMinutes - 1 . " minutes"));
                        $s3Media->save();
                    }
                }
            }
        }

        $media = FileMedia::with('s3Media')
            ->where($type, $id)
            ->first();

        return $media;
    }

    public static function delete ($id, $type = 'id') {
        $media = FileMedia::where($type, $id)->first();
        if (!$media) {
            return [
                'success' => false,
                'error' => [
                    'code' => 'media_not_found',
                    'title' => 'Media Not Found',
                    'message' => 'The media you are trying to access could not be found'
                ]
            ];
        }

        if ($media->s3_upload_id) {
            $s3Media = S3Upload::where('id', $media->s3_upload_id)
                ->first();
            if (!$s3Media) {
                return [
                    'success' => false,
                    'error' => [
                        'code' => 'media_not_found',
                        'title' => 'Media Not Found',
                        'message' => 'The media you are trying to delete could not be found'
                    ]
                ];
            }

            $delete = S3Service::delete($s3Media->path);
            if (!$delete['success']) {
                return $delete;
            }

            $s3Media = S3Upload::where('id', $media->s3_upload_id)
                ->delete();

            return $delete;
        }
    }

    public static function list () {
        
    }

}