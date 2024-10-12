<div class="modal fade" tabindex="-1" role="dialog" id="stream-details-dialog" style="overflow-y: auto;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title p-2">{{ __('How to stream') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <p class="p-2 ml-1">{{ __('Your stream server is online. In order to get going, follow the steps below:') }}</p>


                <div class="mt-3 inline-border-tabs">
                    <nav class="nav nav-pills nav-justified" role="tablist">
                        <a class="nav-link active" data-toggle="tab" data-target="#nav-desktop" type="button"
                            role="tab" aria-controls="nav-desktop" aria-selected="true">
                            <div class="d-flex align-items-center justify-content-center">
                                @include('elements.icon', [
                                    'icon' => 'laptop-outline',
                                    'variant' => 'small',
                                    'classes' => 'mr-2',
                                ])
                                {{ __('Desktop') }}
                            </div>
                        </a>
                        <a class="nav-link" data-toggle="tab" data-target="#nav-mobile" type="button" role="tab"
                            aria-controls="nav-mobile" aria-selected="true">
                            <div class="d-flex align-items-center justify-content-center">
                                @include('elements.icon', [
                                    'icon' => 'phone-portrait-outline',
                                    'variant' => 'small',
                                    'classes' => 'mr-2',
                                ])
                                {{ __('Mobile') }}
                            </div>
                        </a>
                    </nav>
                </div>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-desktop" role="tabpanel">
                        <div class="mt-2">
                            <ol class="py-1">
                                <li class="mb-1">{{ __('Download') }} <a href="https://obsproject.com/download"
                                        target="_blank">OBS</a> {{ __('for desktop or mobile alternatives.') }}</li>
                                <li class="mb-1">{{ __('Go to') }} <code>{{ __('Settings > Stream') }}</code>.
                                    {{ __('For') }} <code>{{ __('Service') }}</code>, {{ __('select') }}
                                    <code>{{ __('Custom') }}</code>.</li>
                                <li class="mb-1">{{ ucfirst(__('for the')) }}
                                    <code>{{ __('Server & Stream key') }}</code>, {{ __('use the values below.') }}
                                </li>
                            </ol>
                            <div class="form-group ml-1">
                                <label for="colFormLabelSm"
                                    class="p-1">{{ __('Stream url') }}</label>
                                <div class="w-100 d-flex p-1 ml-1">
                                    <input type="text" class="form-control form-control-md streamInput" id="stream-url"
                                        placeholder="{{ __('Stream url') }}" readonly>
                                    <div class="col-sm-auto d-flex align-items-center justify-content-center">
                                        <span class="h-pill h-pill-accent rounded mr-2"
                                            onclick="Streams.copyStreamData('url')">
                                            @include('elements.icon', ['icon' => 'copy-outline'])
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ml-1">
                                <label for="colFormLabelSm"
                                    class="p-1">{{ __('Stream key') }}</label>
                                <div class="w-100 d-flex p-1 ml-1">
                                    <input type="text" class="form-control form-control-md streamInput" id="stream-key"
                                        placeholder="{{ __('Stream key') }}" readonly>
                    
                                    <div class="col-sm-auto d-flex align-items-center justify-content-center">
                                        <span class="h-pill h-pill-accent rounded mr-2"
                                            onClick="Streams.copyStreamData('key');">
                                            @include('elements.icon', ['icon' => 'copy-outline'])
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-mobile" role="tabpanel">
                        <div class="mt-2">
                            <ol class="py-1">
                                <li class="mb-1">{{ __('Download') }} StreamLabs <a
                                        href="https://apps.apple.com/us/app/streamlabs-live-streaming-app/id1294578643"
                                        target="_blank">iOS</a> {{ __('or') }} <a
                                        href="https://play.google.com/store/apps/details?id=com.streamlabs&hl=pt_BR"
                                        target="_blank">Android</a>.</li>
                                <li class="mb-1">{{ __('Go to') }}
                                    <code>Menu > Configurações da conta > Servidor RMTP</code>.</li>
                                <li class="mb-1">{{ ucfirst(__('for the')) }}
                                    <code>Stream url & Stream key</code>, {{ __('use the values below.') }}
                                </li>
                                <li class="mb-1">
                                    Iniciar Live.
                                </li>
                            </ol>
                            <div class="form-group ml-1">
                                <label for="colFormLabelSm"
                                    class="p-1">{{ __('Stream url') }}</label>
                                <div class="w-100 d-flex p-1">
                                    <input type="text" class="form-control form-control-md streamInput" id="stream-url"
                                        placeholder="{{ __('Stream url') }}" readonly >
                                    <div class="col-sm-auto d-flex align-items-center justify-content-center">
                                        <span class="h-pill h-pill-accent rounded mr-2"
                                            onclick="Streams.copyStreamData('url')">
                                            @include('elements.icon', ['icon' => 'copy-outline'])
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ml-1">
                                <label for="colFormLabelSm"
                                    class="p-1">{{ __('Stream key') }}</label>
                                <div class="w-100 d-flex p-1">
                                    <input type="text" class="form-control form-control-md streamInput" id="stream-key"
                                        placeholder="{{ __('Stream key') }}" readonly>
                    
                                    <div class="col-sm-auto d-flex align-items-center justify-content-center">
                                        <span class="h-pill h-pill-accent rounded mr-2"
                                            onClick="Streams.copyStreamData('key');">
                                            @include('elements.icon', ['icon' => 'copy-outline'])
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-round" data-dismiss="modal">{{ __('Got it') }}</button>
            </div>
        </div>
    </div>
</div>
