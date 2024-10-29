<?php

namespace App\Helpers;

class ImageHandler
{
    public static function cropAlign($filename, $mime_type, $cropWidth, $cropHeight, $horizontalAlign = 'center', $verticalAlign = 'middle') {
        $image = null;

        switch($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($filename);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filename);
                break;
        }

        if(!$image) {
            return false;
        }

        $self = new self();

        $width = imagesx($image);
        $height = imagesy($image);
        $horizontalAlignPixels = $self->calculatePixelsForAlign($width, $cropWidth, $horizontalAlign);
        $verticalAlignPixels = $self->calculatePixelsForAlign($height, $cropHeight, $verticalAlign);

        $cropedImg = imageCrop($image, [
            'x' => $horizontalAlignPixels[0],
            'y' => $verticalAlignPixels[0],
            'width' => $horizontalAlignPixels[1],
            'height' => $verticalAlignPixels[1]
        ]);

        ob_start();
        switch($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($cropedImg);
                break;
            case 'image/png':
                imagepng($cropedImg);
                break;
        }
        $cropedImg = ob_get_contents();
        ob_clean();

        return $cropedImg;
    }

    private function calculatePixelsForAlign($imageSize, $cropSize, $align) {
        switch ($align) {
            case 'left':
            case 'top':
                return [0, min($cropSize, $imageSize)];
            case 'right':
            case 'bottom':
                return [max(0, $imageSize - $cropSize), min($cropSize, $imageSize)];
            case 'center':
            case 'middle':
                return [
                    max(0, floor(($imageSize / 2) - ($cropSize / 2))),
                    min($cropSize, $imageSize),
                ];
            default: return [0, $imageSize];
        }
    }

}
