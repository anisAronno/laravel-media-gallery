<?php

namespace AnisAronno\MediaGallery\Http\Controllers;

use AnisAronno\MediaGallery\Helpers\CacheKey;
use AnisAronno\MediaGallery\Helpers\ImageDataProcessor;
use AnisAronno\MediaGallery\Http\Requests\StoreImageRequest;
use AnisAronno\MediaGallery\Http\Requests\UpdateImageRequest;
use AnisAronno\MediaGallery\Http\Resources\ImageResources;
use AnisAronno\MediaGallery\Models\Image;
use AnisAronno\MediaHelper\Facades\Media;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function __construct()
    {
        $guards      = config('gallery.guard', ['auth']);
        $this->middleware($guards);
    }

    /**
     * Get ALl Image.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString          = http_build_query($queryParams);
        $mediaGalleryCacheKey = CacheKey::getMediaGalleryCacheKey();
        $key                  = $mediaGalleryCacheKey.config('gallery.view_all_media_anyone').md5($queryString);

        $cacheTTL = Config::get('gallery.cache_expiry_time', 1440);

        $images = Cache::remember(
            $key,
            now()->addMinutes($cacheTTL),
            function () use ($request)
            {
                return Image::query()
                    ->when($request->has('search'), function ($query) use ($request)
                    {
                        $query->where('title', 'LIKE', '%'.$request->input('search').'%');
                    })
                    ->when($request->has('directory'), function ($query) use ($request)
                    {
                        $query->where('directory', $request->input('directory'));
                    })
                    ->when($request->has('owner_id') || ! config('gallery.view_all_media_anyone'), function ($query) use ($request)
                    {
                        $query->where('owner_id', auth()->id());
                    })
                    ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request)
                    {
                        $query->whereBetween('created_at', [
                            new \DateTime($request->input('startDate')),
                            new \DateTime($request->input('endDate')),
                        ]);
                    })
                    ->orderBy($request->input('orderBy', 'id'), $request->input('order', 'desc'))
                    ->paginate(20)->withQueryString();
            }
        );

        Cache::put($mediaGalleryCacheKey, array_merge(Cache::get($mediaGalleryCacheKey, []), [$key]));

        return ImageResources::collection($images)->response();
    }

    /**
     * Show Image.
     *
     * @param [type] $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return  (new ImageResources(Image::query()->findOrFail($id)))->response();
    }

    /**
     *   Image store.
     *
     * @param StoreImageRequest $request
     * @return JsonResponse
     */
    public function store(StoreImageRequest $request): JsonResponse
    {
        $data               = ImageDataProcessor::process($request);
        $data['title']      = $request->input('title', 'Image');
        $data['owner_id']   = auth()->id();
        $data['owner_type'] = $request->user() ? get_class($request->user()) : null;

        try {
            $image = Image::create($data);

            return response()->json([
                'message' => 'Created successful',
                'data'    => $image,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Image Update.
     *
     * @param UpdateImageRequest $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function update(UpdateImageRequest $request, $id): JsonResponse
    {
        $image = Image::query()->findOrFail($id);

        abort_unless($image->owner_id === auth()->id(), 403, 'You are not authorized to update this media');

        try {
            $image->update($request->only('title'));

            return response()->json(['message' => 'Updated successful']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     *  Delete image.
     *
     * @param [type] $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $image = Image::query()->findOrFail($id);

        abort_unless($image->owner_id === auth()->id(), 403, 'You are not authorized to delete this media');

        try {
            Media::delete($image->url);

            $image->delete();

            return response()->json(['message' => 'Deleted successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Deleted failed'], 400);
        }
    }

    /**
     * Image Batch Delete.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function batchDelete(Request $request): JsonResponse
    {
        $request->validate([
            'secret'       => 'required',
            'images'       => 'required|array',
        ]);

        abort_unless($request->secret === config('gallery.batch_delete_secret'), 403, 'You are not authorized to delete all media');

        try {
            return DB::transaction(function () use ($request)
            {
                $imageIds = $request->input('images');

                $existingImages = Image::whereIn('id', $imageIds)->get();
                $existingIds    = $existingImages->pluck('id')->toArray();

                $missingIds = array_diff($imageIds, $existingIds);

                if (! empty($missingIds)) {
                    throw new ModelNotFoundException('Images with IDs: '.implode(',', $missingIds).' not found.');
                }

                $urlsToDelete = $existingImages->pluck('url')->toArray();

                foreach ($urlsToDelete as $url) {
                    Media::delete($url);
                }

                $deletedImages = $existingImages->each->delete();

                if ($deletedImages->count() === count($existingImages)) {
                    return response()->json(['message' => 'Deleted successfully']);
                }

                return response()->json(['message' => 'Something went wrong!'], 400);
            });
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
