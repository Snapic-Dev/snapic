<?php

namespace App\Providers;

use App\Model\Stream;
use App\Model\Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class StreamsServiceProvider extends ServiceProvider
{
    const BITMOVIN_API_ENDPOINT = 'https://api.bitmovin.com/v1/streams/live';

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        //
    }

    public static function userPaidForStream($userId, $streamId)
    {
        return Transaction::query()->where([
            'stream_id' => $streamId,
            'sender_user_id' => $userId,
            'type' => Transaction::STREAM_ACCESS,
            'status' => Transaction::APPROVED_STATUS
        ])->exists();
    }

    public static function getUserInProgressStream($addDurationTag = true, $userId = null)
    {
        if ($userId === null) {
            $userId = Auth::id();
        }
        $stream = Stream::query()
            ->where(['user_id' => $userId, 'status' => Stream::IN_PROGRESS_STATUS])
            ->first();

        if ($stream && $addDurationTag) {
            $stream->duration = $stream->created_at->diffInMinutes($stream->ended_at);
            $stream->duration = $stream->duration === 0 ? 1 : $stream->duration;
            $stream->created_at_short = $stream->created_at->format('d M y ');
        }
        return $stream;
    }

    public static function getUserStreams()
    {
        $streams = Stream::query()
            ->where(['user_id' => Auth::id(), 'status' => Stream::ENDED_STATUS])
            ->orderBy('created_at', 'DESC')
            ->paginate(6);
        $streams->getCollection()->transform(function ($stream) {
            $stream->duration = $stream->created_at->diffInMinutes($stream->ended_at);
            $stream->duration = $stream->duration === 0 ? 1 : $stream->duration;
            $stream->created_at_short = $stream->created_at->format('d M y ');
            return $stream;
        });
        return $streams;
    }

    public static function initiateStreamingByUser($options)
    {
        try {
            $stream = self::getUserInProgressStream();
            if (!$stream) {
                $bitmovinStreaming = self::createBitmovinStreaming([
                    'title' => $options['name'],
                    'description' => $options['description'] ?? '',
                    'domainRestrictionId' => $options['domainRestrictionId'] ?? ''
                ]);

                if (
                    $bitmovinStreaming && isset($bitmovinStreaming['status'])
                    && $bitmovinStreaming['status'] === 'SUCCESS'
                    && isset($bitmovinStreaming['data']['result']['id'])
                    && isset($bitmovinStreaming['data']['result']['streamKey'])
                    && isset($bitmovinStreaming['data']['result']['broadcastingUrl'])
                    && isset($bitmovinStreaming['data']['result']['playerUrl'])
                ) {
                    $stream = Stream::create([
                        'user_id' => Auth::id(),
                        'status' => Stream::IN_PROGRESS_STATUS,
                        'name' => $options['name'],
                        'poster' => $options['poster'] ?? 'default_poster_value',
                        'slug' => Str::slug($options['name']),
                        'price' => $options['price'],
                        'requires_subscription' => $options['requires_subscription'] === 'true' ? 1 : 0,
                        'is_public' => $options['is_public'] === 'true' ? 1 : 0,
                        'bitmovin_id' => $bitmovinStreaming['data']['result']['id'],
                        'stream_key' => $bitmovinStreaming['data']['result']['streamKey'],
                        'broadcasting_url' => $bitmovinStreaming['data']['result']['broadcastingUrl'],
                        'player_url' => $bitmovinStreaming['data']['result']['playerUrl']
                    ]);
                }
            } else {
                return ['success' => false, 'message' => __('You can only have one active stream at a time.')];
            }
            return ['success' => true, 'data' => $stream];
        } catch (\Exception $exception) {
            Log::error('Error initiating stream: ' . $exception->getMessage());
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    public static function createBitmovinStreaming($options)
    {
        $httpClient = new Client();

        $params = [
            'title' => $options['name'],
            'description' => isset($options['description']) ? $options['description'] : '',
            'domainRestrictionId' => isset($options['domainRestrictionId']) ? $options['domainRestrictionId'] : ''
        ];

        try {
            $response = $httpClient->request(
                'POST',
                self::BITMOVIN_API_ENDPOINT,
                [
                    'headers' => [
                        'X-Api-Key' => env('BITMOVIN_API_KEY'),
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $params,
                    'verify' => false
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error creating Bitmovin stream: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function getBitmovinStreamingDetails($id)
    {
        $httpClient = new Client();

        try {
            $response = $httpClient->request(
                'GET',
                self::BITMOVIN_API_ENDPOINT . '/' . $id,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . env('BITMOVIN_API_KEY'),
                    ]
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error getting Bitmovin stream details: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function getBitmovinStreamingDvr($id)
    {
        $httpClient = new Client();

        try {
            $response = $httpClient->request(
                'GET',
                self::BITMOVIN_API_ENDPOINT . '/' . $id . '/dvr',
                [
                    'http_errors' => false,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . env('BITMOVIN_API_KEY'),
                    ]
                ]
            );

            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(), true);
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error getting Bitmovin DVR: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function destroyBitmovinStream($id)
    {
        $httpClient = new Client();

        try {
            $response = $httpClient->request(
                'DELETE',
                self::BITMOVIN_API_ENDPOINT . '/' . $id,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . env('BITMOVIN_API_KEY'),
                    ]
                ]
            );

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error deleting Bitmovin stream: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function getPublicStreams($options)
    {
        $streams = Stream::where('is_public', 1);
        if (isset($options['status'])) {
            if ($options['status'] == 'live') {
                $streams->where('status', Stream::IN_PROGRESS_STATUS);
            } elseif ($options['status'] == 'ended') {
                $streams->where('status', Stream::ENDED_STATUS);
            } else {
                $streams->whereIn('status', [Stream::ENDED_STATUS, Stream::IN_PROGRESS_STATUS]);
            }
        }

        if (isset($options['userId'])) {
            $streams->where('user_id', $options['userId']);
        }

        if (isset($options['searchTerm'])) {
            $streams->where('name', 'like', '%' . $options['searchTerm'] . '%');
        }

        $blockedUsers = ListsHelperServiceProvider::getListMembers(Auth::user()->lists->firstWhere('type', 'blocked')->id);
        $streams->whereNotIn('user_id', $blockedUsers);

        $showUsername = isset($options['showUsername']) ? $options['showUsername'] : true;

        $streams = isset($options['pageNumber'])
            ? $streams->paginate(9, ['*'], 'page', $options['pageNumber'])
            : $streams->paginate(9);

        if ($showUsername) {
            $streams->getCollection()->transform(function ($stream) {
                $stream->username = $stream->user->name;
                return $stream;
            });
        }

        return $streams;
    }

    public static function getPublicLiveStreamsCount()
    {
        return Stream::where('is_public', 1)
            ->where('status', Stream::IN_PROGRESS_STATUS)
            ->count();
    }

    public static function userIsEligibleForStreaming()
    {
        $streamsCount = Stream::where('user_id', Auth::id())->count();
        $maxStreamsAllowed = (int) getSetting('streams.max_streams_allowed_per_user');

        return $streamsCount < $maxStreamsAllowed;
    }
}
