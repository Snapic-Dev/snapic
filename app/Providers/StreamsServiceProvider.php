<?php

namespace App\Providers;

use App\Model\Stream;
use App\Model\Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use View;

class StreamsServiceProvider extends ServiceProvider
{
    const BITMOVIN_API_ENDPOINT = 'https://api.bitmovin.com/v1/streams/live';
    // const PUSHR_API_ENDPOINT = 'https://www.pushrcdn.com/api/v3/streams/stream';

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
                $settings = [
                    'encoder' => getSetting('streams.pushr_encoder'),
                    'dvr' => (int)getSetting('streams.allow_dvr'),
                    'mux' => (int)getSetting('streams.allow_mux'),
                    '360p' => (int)getSetting('streams.allow_360p'),
                    '480p' => (int)getSetting('streams.allow_480p'),
                    '576p' => (int)getSetting('streams.allow_576p'),
                    '720p' => (int)getSetting('streams.allow_720p'),
                    '1080p' => (int)getSetting('streams.allow_1080p'),
                ];
                $bitmovinStreaming = self::createBitmovinStreaming(['name' => $options['name'], 'settings' => $settings]);
                if (
                    $bitmovinStreaming && isset($bitmovinStreaming['status'])
                    && $bitmovinStreaming['status'] === 'success'
                    && isset($bitmovinStreaming['rtmp_key'])
                    && isset($bitmovinStreaming['rtmp_server'])
                    && isset($bitmovinStreaming['hls_link'])
                    && isset($bitmovinStreaming['player_link'])
                    && isset($bitmovinStreaming['id'])
                ) {
                    $stream = Stream::create([
                        'user_id' => Auth::user()->id,
                        'status' => Stream::IN_PROGRESS_STATUS,
                        'name' => $options['name'],
                        'poster' => $options['poster'],
                        'slug' => Str::slug($options['name']),
                        'price' => $options['price'],
                        'requires_subscription' => $options['requires_subscription'] == 'true' ? 1 : 0,
                        'is_public' => $options['is_public'] == 'true' ? 1 : 0,
                        'pushr_id' => $bitmovinStreaming['id'],
                        'rtmp_key' => $bitmovinStreaming['rtmp_key'],
                        'rtmp_server' => $bitmovinStreaming['rtmp_server'],
                        'hls_link' => $bitmovinStreaming['hls_link'],
                        'settings' => $settings
                    ]);
                } else {
                    return ['success' => false, 'message' => 'Failed to create streaming with Bitmovin.'];
                }
            } else {
                return ['success' => false, 'message' => __('You can only have one active stream at a time.')];
            }

            return ['success' => true, 'data' => $stream];
        } catch (\Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    // public static function createPushrStreaming($options)
    // {
    //     try {
    //         $httpClient = new Client();

    //         $response = $httpClient->request(
    //             'POST',
    //             self::PUSHR_API_ENDPOINT,
    //             [
    //                 'headers' => [
    //                     'Accept' => 'application/json',
    //                     'APIKEY' => config('services.pushr.api_key'),
    //                 ],
    //                 'form_params' => [
    //                     'action' => 'create',
    //                     'zone' => config('services.pushr.push_zone_id'),
    //                     'name' => $options['name'],
    //                     'encoder' => $options['settings']['encoder'] ?? 'eu', // Localização do encoder
    //                     'dvr' => $options['settings']['dvr'] ?? 0,
    //                     'mux' => $options['settings']['mux'] ?? 0,
    //                     '360p' => $options['settings']['360p'] ?? 0,
    //                     '480p' => $options['settings']['480p'] ?? 0,
    //                     '576p' => $options['settings']['576p'] ?? 0,
    //                     '720p' => $options['settings']['720p'] ?? 1,
    //                     '1080p' => $options['settings']['1080p'] ?? 0,
    //                 ],
    //             ]
    //         );

    //         $result = json_decode($response->getBody(), true);

    //         if (isset($result['status']) && $result['status'] === 'success') {
    //             return [
    //                 'status' => 'success',
    //                 'id' => $result['id'],
    //                 'rtmp_key' => $result['rtmp_key'],
    //                 'rtmp_server' => $result['rtmp_server'],
    //                 'hls_link' => $result['hls_link'],
    //                 'player_link' => $result['player_link'],
    //             ];
    //         }

    //         return ['status' => 'error', 'message' => 'Failed to create stream on Pushr.'];
    //     } catch (\Exception $e) {
    //         Log::error('Error creating Pushr stream: ' . $e->getMessage());
    //         return ['status' => 'error', 'message' => $e->getMessage()];
    //     }
    // }


    // public static function createBitmovinStreaming($options)
    // {
    //     $httpClient = new Client();

    //     $params = [
    //         'title' => $options['name'],
    //         'description' => $options['description'] ?? '',
    //         'domainRestrictionId' => $options['domainRestrictionId'] ?? ''
    //     ];

    //     try {
    //         $response = $httpClient->request(
    //             'POST',
    //             self::BITMOVIN_API_ENDPOINT,
    //             [
    //                 'headers' => [
    //                     'X-Api-Key' => env('BITMOVIN_API_KEY'),
    //                     'Accept' => 'application/json',
    //                     'Content-Type' => 'application/json',
    //                 ],
    //                 'json' => $params,
    //                 'verify' => false
    //             ]
    //         );

    //         Log::info('Bitmovin API Response: ' . $response->getBody());

    //         return json_decode($response->getBody(), true);
    //     } catch (\Exception $e) {
    //         Log::error('Error creating Bitmovin stream: ' . $e->getMessage());
    //         return ['status' => 'error', 'message' => $e->getMessage()];
    //     }
    // }]


    // public static function createBitmovinStreaming($options)
    // {
    //     $httpClient = new Client();
    //     $createStreamingRequest = $httpClient->request(
    //         'POST',
    //         self::PUSHR_API_ENDPOINT . '/stream',
    //         [
    //             'headers' => [
    //                 'Accept' => 'application/json',
    //                 'APIKEY' => getSetting('streams.pushr_key'),
    //             ],
    //             'form_params' => array_merge([
    //                 'action' => 'create',
    //                 'zone' => getSetting('streams.pushr_zone_id'),
    //                 'name' => $options['name'],
    //             ], $options['settings'])
    //         ]
    //     );
    //     return json_decode($createStreamingRequest->getBody(), true);
    // }

    public static function createBitmovinStreaming($options)
    {
        Log::info('Options:', $options);

        $userId = Auth::id();
        Log::info('userId:', $userId);

        return [
            'status' => 'success',
            'id' =>  $userId,
            'rtmp_key' => 'fake-rtmp-key',
            'rtmp_server' => 'fake-rtmp-server',
            'hls_link' => 'http://fake-hls-link',
            'player_link' => 'http://fake-player-link'
        ];
    }


    public static function getPushrStreamingDetails($id)
    {
        $httpClient = new Client();
        $createStreamingRequest = $httpClient->request(
            'GET',
            self::BITMOVIN_API_ENDPOINT . '/details?id=' . $id,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'APIKEY' => getSetting('streams.pushr_key'),
                ]
            ]
        );
        return json_decode($createStreamingRequest->getBody(), true);
    }

    public static function getPushrStreamingDvr($id)
    {
        $httpClient = new Client();
        $createStreamingRequest = $httpClient->request(
            'GET',
            self::BITMOVIN_API_ENDPOINT . '/dvr?id=' . $id,
            [
                'http_errors' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'APIKEY' => getSetting('streams.pushr_key'),
                ]
            ]
        );
        if ($createStreamingRequest->getStatusCode() == 200) {
            return json_decode($createStreamingRequest->getBody(), true);
        }
        return false;
    }

    /**
     * Destroy pushr streaming by id
     * @param $id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function destroyPushrStream($id)
    {
        $httpClient = new Client();
        $createStreamingRequest = $httpClient->request(
            'POST',
            self::BITMOVIN_API_ENDPOINT . '/destroy',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'APIKEY' => getSetting('streams.pushr_key'),
                ],
                'form_params' => [
                    'id' => $id
                ]
            ]
        );
        return json_decode($createStreamingRequest->getBody(), true);
    }

    /**
     * Gets all available public streams
     * @param $options
     * @return array
     */
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

        $showUsername = true;
        if (isset($options['showUsername']) && $options['showUsername'] == false) $showUsername = false;

        $streams->orderBy('created_at', 'DESC');
        if (isset($options['pageNumber'])) {
            $streams = $streams->paginate(9, ['*'], 'page', $options['pageNumber'])->appends(request()->query());
        } else {
            $streams = $streams->paginate(9)->appends(request()->query());
        }

        if (!isset($options['encodePostsToHtml'])) {
            $options['encodePostsToHtml'] = false;
        }
        if ($options['encodePostsToHtml']) {
            // Posts encoded as JSON
            $data = [
                'total' => $streams->total(),
                'currentPage' => $streams->currentPage(),
                'last_page' => $streams->lastPage(),
                'prev_page_url' => $streams->previousPageUrl(),
                'next_page_url' => $streams->nextPageUrl(),
                'first_page_url' => $streams->nextPageUrl(),
                'hasMore' => $streams->hasMorePages(),
            ];
            $postsData = $streams->map(function ($stream) use ($data, $options, $showUsername) {
                $stream->setAttribute('postPage', $data['currentPage']);
                $stream = ['id' => $stream->id, 'html' => View::make('elements.streams.stream-element-public')->with('stream', $stream)->with('showLiveIndicators', false)->with('showUsername', $showUsername)->render()];
                return $stream;
            });
            $data['users'] = $postsData;
        } else {
            // Collection data posts | To be rendered on the server side
            $postsCurrentPage = $streams->currentPage();
            $streams->map(function ($user) use ($postsCurrentPage) {
                $user->setAttribute('postPage', $postsCurrentPage);
                return $user;
            });
            $data = $streams;
        }
        return $data;
    }


    public static function getPublicLiveStreamsCount()
    {
        $blockedUsers = ListsHelperServiceProvider::getListMembers(Auth::user()->lists->firstWhere('type', 'blocked')->id);
        $streams = Stream::where('is_public', 1)->where('status', Stream::IN_PROGRESS_STATUS)->whereNotIn('user_id', $blockedUsers)->count();
        return $streams;
    }
}
