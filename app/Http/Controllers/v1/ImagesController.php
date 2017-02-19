<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Validator;
use App\Image;
use Illuminate\Support\Facades\Input;
use Response;
use Intervention\Image\ImageManagerStatic as ImageManager;

class ImagesController extends App_Controller
{
    public function store(Request $request) {
        $this->validate($request, [
          'image' => 'required|mimes:png,jpeg',
        ]);

        if (Input::hasFile('image') && Input::file('image')->getSize() > 1000000) {
          return Response::json([
            'error' => [
              'fields' => [
                'image' => 'Image size must be less than 1 mb',
              ],
            ],
          ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        if (Input::hasFile('image')) {
          $sizes = [1280, 604, 130];

          $image = Input::file('image');
          // $size = $image->getSize();
          // $mime = $image->getMimeType();

          $uuid = Uuid::uuid4()->toString();
          $destination_path = 'uploads/' . substr($uuid, 0, 2) . '/' . substr($uuid, 2, 2);
          $extension = $image->getClientOriginalExtension();
          $image->move($destination_path, $uuid . '.' . $extension);

          $image_creator = ImageManager::make(public_path($image->path() . $destination_path . '/' . $uuid . '.' . $extension));
          $image_sizes = [$image_creator->width(), $image_creator->height()];
          foreach ($sizes as $size) {
            $image_creator
              ->widen($size, function ($constraint) {
                  $constraint->upsize();
              });

            if ($image_sizes[1] > 1280) {
              $image_creator
                ->heighten(1280, function ($constraint) {
                  $constraint->upsize();
              });
            }

            $image_creator
              ->save($destination_path . '/' .$uuid . '.' . $size . 'x' . $size . '.' . $extension);
          }

          $image = Image::create([
            'id' => $uuid,
          ]);

          return Response::json([
            'response' => [
              'uuid' => $image->id,
            ],
          ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return Response::json([
          'error' => [
            'fields' => [
              'image' => 'Image file is required',
            ],
          ],
        ], 422, [], JSON_UNESCAPED_UNICODE);
    }
}
