<?php

namespace App\EventSubscriber;

use App\Event\MediaImagesUploadEvent;
use App\Service\MediaImagesUploader;

class MediaImagesSubscriber
{
    private $uploader;

    public function __construct(MediaImagesUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            MediaImagesUploadEvent::IMAGE_UPLOAD => [
                ['upload', 10],
               // ['remove', 20],
            ],
        ];
    }

    public function upload(MediaImagesUploadEvent $event)
    {
        $trick = $event->getTrick();
        $trick->setImage($this->uploader->upload($trick->getImageUpload()));

    }

    public function remove(MediaImagesUploadEvent $event)
    {
        $trick = $event->getTrick();
        $file = $trick->getImage();

        if(isset($file))
        {
            $file_path = $this->uploader->getMediaDirectory().'/'.$file;
            if(file_exists($file_path)) unlink($file_path);
        }

    }
}