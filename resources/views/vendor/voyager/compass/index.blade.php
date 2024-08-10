@extends('voyager::master')

@section('css')
    @include('voyager::compass.includes.styles')
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-compass"></i>
        <p>{{ __('voyager::generic.compass') }}</p>
    </h1>
@stop

@section('content')
    <div class="container-fluid">
        @include('voyager::alerts')
    </div>

    <div class="page-content compass container-fluid">

        <div class="accordion" id="accordionExample">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                            data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="voyager-html5"></i>
                            {{ __('voyager::compass.fonts.title') }}
                        </button>
                    </h2>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <div class="tab-content">
                            <div id="fonts" class="tab-pane fade in active">
                                <div class="row">

                                    @include('voyager::compass.includes.fonts')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="card mb-3">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                            data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="voyager-terminal"></i>
                            {{ __('voyager::compass.commands.title') }}
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        <div id="commands" class="tab fade in active">
                            <div class="row">

                                @include('voyager::compass.includes.commands')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                            data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="voyager-logbook"></i>
                            {{ __('voyager::compass.logs.title') }}
                        </button>
                    </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                    <div class="card-body">
                        @include('voyager::compass.includes.logs')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <!-- Remover JavaScript personalizado se não for necessário -->
@stop
