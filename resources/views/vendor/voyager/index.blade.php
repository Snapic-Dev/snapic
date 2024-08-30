@extends('voyager::master')

@section('page_header')
    @include('voyager::alerts')
@stop
@section('content')
    <div class="page-content">
        <div class="analytics-container">


            @if (!checkMysqlndForPDO() || !checkForMysqlND())
                <div class="storage-incorrect-bucket-config tab-additional-info">
                    <div class="alert alert-warning alert-dismissible mb-1">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <div class="info-label">
                            <div class="icon voyager-info-circled"></div><strong>{{ __('Warning!') }}</strong>
                        </div>
                        <div class=""> {{ __("Your PHP's pdo_mysql extension is not using mysqlnd driver. ") }}
                            {{ __('This might cause different UI related issues.') }}
                            <div class="mt-05">
                                {{ __('Please contact your hosting provider and check if they can enable mysqlnd for pdo_mysql as default driver. Alternatively, you can check if the other PHP versions act the same. ') }}
                            </div>
                            <div class="mt-05">
                                <ul>
                                    <li>{{ __('Mysqlnd loaded:') }}
                                        <strong>{{ checkForMysqlND() ? __('True') : __('False') }}</strong>
                                    </li>
                                    <li>{{ __('Mysqlnd for PDO:') }}
                                        <strong>{{ checkMysqlndForPDO() ? __('True') : __('False') }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
            @endif

            @include('elements.admin.metrics')


        </div>

    </div>
@stop
