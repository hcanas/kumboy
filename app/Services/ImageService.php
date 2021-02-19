<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * @param UploadedFile $image
     * @return bool
     */
    public function isValid($image)
    {
        $ext = substr($image->getMimeType(), strpos($image->getMimeType(), '/') + 1);

        if (in_array($ext, ['jpeg', 'png']) === false) {
            return false;
        }

        // file size must not exceed 500kb
        if ($image->getSize() / 1024 > 500) {
            return false;
        }

        return true;
    }

    public function make(UploadedFile $image)
    {
        if (!$this->isValid($image)) {
            return null;
        }

        return Image::make($image->getRealPath());
    }

    /**
     * @param Image|UploadedFile $image
     * @param $w
     * @param $h
     * @param $p
     * @return string
     */
    public function resize($image, $w, $h, $p)
    {
        $image = $image instanceof Image ? $image : Image::make($image->getRealPath());
        $canvas_w = $w;
        $canvas_h = $h;
        $w -= $p;
        $h -= $p;

        if ($image->width() === $image->height()) {
            // square
            $image->resize($w, $h);
            $image->resizeCanvas($canvas_w, $canvas_h, 'center', false, '#ffffff');
        } elseif ($image->width() > $image->height()) {
            // horizontal, pad left and right
            $image->resize($w, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->resizeCanvas($canvas_w, $canvas_h, 'center', false, '#ffffff');
        } elseif ($image->width() < $image->height()) {
            // vertical, pad top and bottom
            $image->resize(null, $h, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->resizeCanvas($canvas_w, $canvas_h, 'center', false, '#ffffff');
        }

        return (string) $image->encode();
    }
}