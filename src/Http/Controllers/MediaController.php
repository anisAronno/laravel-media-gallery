<?php

namespace AnisAronno\MediaGallery\Http\Controllers;

use AnisAronno\MediaGallery\Helpers\CacheKey;
use AnisAronno\MediaGallery\Helpers\MediaDataProcessor;
use AnisAronno\MediaGallery\Http\Requests\StoreMediaRequest;
use AnisAronno\MediaGallery\Http\Requests\UpdateMediaRequest;
use AnisAronno\MediaGallery\Http\Resources\MediaResources;
use AnisAronno\MediaHelper\Facades\Media;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    public function __construct()
    {
        $guards      = config('media.guard', ['auth']);
        $this->middleware($guards);
    }

    /**
     * Get ALl Media.
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
        $key                  = $mediaGalleryCacheKey.config('media.view_all_media_anyone').md5($queryString);

        $cacheTTL = Config::get('media.cache_expiry_time', 1440);

        $media = Cache::remember(
            $key,
            now()->addMinutes($cacheTTL),
            function () use ($request)
            {
                return Media::query()
                    ->when($request->has('search'), function ($query) use ($request)
                    {
                        $query->where('title', 'LIKE', '%'.$request->input('search').'%');
                    })
                    ->when($request->has('directory'), function ($query) use ($request)
                    {
                        $query->where('directory', $request->input('directory'));
                    })
                    ->when($request->has('owner_id') || ! config('media.view_all_media_anyone'), function ($query) use ($request)
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

        return MediaResources::collection($media)->response();
    }

    /**
     * Show Media.
     *
     * @param [type] $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return  (new MediaResources(Media::query()->findOrFail($id)))->response();
    }

    /**
     * Media store.
     *
     * @param StoreMediaRequest $request
     * @return JsonResponse
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $data               = MediaDataProcessor::process($request);
        $data['title']      = $request->input('title', 'Media');
        $data['owner_id']   = auth()->id();
        $data['owner_type'] = $request->user() ? get_class($request->user()) : null;

        try {
            $media = Media::create($data);

            return response()->json([
                'message' => 'Created successful',
                'data'    => $media,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Media Update.
     *
     * @param UpdateMediaRequest $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function update(UpdateMediaRequest $request, $id): JsonResponse
    {
        $media = Media::query()->findOrFail($id);

        abort_unless($media->owner_id === auth()->id(), 403, 'You are not authorized to update this media');

        try {
            $media->update($request->only('title'));

            return response()->json(['message' => 'Updated successful']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     *  Delete media.
     *
     * @param [type] $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $media = Media::query()->findOrFail($id);

        abort_unless($media->owner_id === auth()->id(), 403, 'You are not authorized to delete this media');

        try {
            Media::delete($media->url);

            $media->delete();

            return response()->json(['message' => 'Deleted successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Deleted failed'], 400);
        }
    }

    /**
     * Media Batch Delete.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function batchDelete(Request $request): JsonResponse
    {
        $request->validate([
            'secret'       => 'required',
            'media'        => 'required|array',
        ]);

        abort_unless($request->secret === config('media.batch_delete_secret'), 403, 'You are not authorized to delete all media');

        try {
            return DB::transaction(function () use ($request)
            {
                $imageIds = $request->input('media');

                $existingMedia  = Media::whereIn('id', $imageIds)->get();
                $existingIds    = $existingMedia->pluck('id')->toArray();

                $missingIds = array_diff($imageIds, $existingIds);

                if (! empty($missingIds)) {
                    throw new ModelNotFoundException('Media with IDs: '.implode(',', $missingIds).' not found.');
                }

                $urlsToDelete = $existingMedia->pluck('url')->toArray();

                foreach ($urlsToDelete as $url) {
                    Media::delete($url);
                }

                $deletedMedia = $existingMedia->each->delete();

                if ($deletedMedia->count() === count($existingMedia)) {
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
