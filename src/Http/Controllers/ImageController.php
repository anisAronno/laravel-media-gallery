<?php

namespace AnisAronno\MediaGallery\Http\Controllers;

use AnisAronno\MediaGallery\Helpers\CacheKey;
use AnisAronno\MediaGallery\Helpers\ImageDataProcessor;
use AnisAronno\MediaGallery\Http\Requests\StoreImageRequest;
use AnisAronno\MediaGallery\Http\Requests\UpdateImageRequest;
use AnisAronno\MediaGallery\Http\Resources\ImageResources;
use AnisAronno\MediaGallery\Models\Image;
use AnisAronno\MediaHelper\Facades\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class ImageController extends Controller
{
    public function __construct()
    {
        $guard = config()->has('gallery.guard') ? config('gallery.guard') : [];
        $this->middleware($guard)->only(['store', 'update', 'destroy', 'groupDelete']); 
        $this->middleware($guard)->except(['index', 'show']); 
    }

    /**
     * Get ALl Image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $mediaGalleryCacheKey = CacheKey::getMediaGalleryCacheKey();
        $key =  $mediaGalleryCacheKey.md5($queryString);

        $cacheTTL = Config::get('gallery.cache_expiry_time', 1440);  

        $images = Cache::remember(
            $key,
            now()->addMinutes($cacheTTL),
            function () use ($request) {
                return Image::query()
                    ->when($request->has('search'), function ($query) use ($request) {
                        $query->where('title', 'LIKE', '%' . $request->input('search') . '%');
                    })
                    ->when($request->has('directory'), function ($query) use ($request) {
                        $query->where('directory', $request->input('directory'));
                    })
                    ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                        $query->whereBetween('created_at', [
                            new \DateTime($request->input('startDate')),
                            new \DateTime($request->input('endDate'))
                        ]);
                    })
                    ->orderBy($request->input('orderBy', 'id'), $request->input('order', 'desc'))
                    ->paginate(20)->withQueryString();
            }
        );

        Cache::put($mediaGalleryCacheKey, array_merge(Cache::get($mediaGalleryCacheKey, []), [$key]));

        return response()->json(ImageResources::collection($images));
    }


    /**
     * Show Image
     *
     * @param Image $image
     * @return JsonResponse
     */
    public function show(Image $image): JsonResponse
    {
        return  response()->json(new ImageResources($image));
    }

    /**
     *   Image store
     *
     * @param StoreImageRequest $request
     * @return JsonResponse
     */
    public function store(StoreImageRequest $request): JsonResponse
    {
        $data = ImageDataProcessor::process($request);
        $data['title'] = $request->input('title', 'Image');
        $data['user_id'] = $request->user() ? $request->user() : $request->user()->id ;

        try {
            Image::create($data);
            return response()->json(['message' => 'Created successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Image Update
     *
     * @param UpdateImageRequest $request
     * @param Image $image
     * @return JsonResponse
     */
    public function update(UpdateImageRequest $request, Image $image): JsonResponse
    {
        try {
            $image->update($request->only('title'));
            return response()->json(['message' => 'Update successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Delete image
     *
     * @param Image $image
     * @return JsonResponse
     */
    public function destroy(Image $image): JsonResponse
    {
        try {
            Media::delete($image->url);

            $image->delete();

            return response()->json(['message' => 'Deleted successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Deleted failed'], 400);
        }
    }

    /**
     * Image Group Delete
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function groupDelete(Request $request): JsonResponse
    {
        try {
            foreach ($request->data as  $image) {
                isset($image['url']) ? Media::delete($image['url']) : '';
            }

            $idArr = array_column($request->data, 'id');
            $result = Image::whereIn('id', $idArr)->delete();
            if ($result) {
                return response()->json(['message' => 'Deleted successfull']);
            }
            return response()->json(['message' => 'Deleted failed'], 400);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Deleted failed'], 400);
        }
    }
}
