<div class="col">
    <div class="metrics-container">

        <div class="row">
            <div class="mb-4 col-md-4">
                <div class="card shadow rounded p-5">
                    <div class="card-body text-muted font-weight-medium">
                        <div>
                            <p class="font-weight-bolder">Last 24 hours</p>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <p class="">{{ __('Users registered') }}: </p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getLast24HoursRegisteredUsersCount() }}
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <p class="">{{ __('New posts') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getLast24HoursPostsCount() }}
                                </h3>

                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <p class="">{{ __('New subscriptions') }}:
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getLast24HoursSubscriptionsCount() }}
                                </h3>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="m-0">{{ __('Total earned') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\DashboardServiceProvider::getLast24HoursTotalEarned()) }}
                                </h3>

                            </div>
                        </div>
                        <span class="pull-right"><a href="admin/users" class="primary-link">{{ __('Go to users') }}
                                ››</a></span>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-md-4">
                <div class="card shadow rounded p-5">
                    <div class="card-body text-muted font-weight-medium">
                        <div>
                            <p class="font-weight-bolder">Payments</p>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <p>{{ __('Active subscriptions') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getActiveSubscriptionsCount() }} </h3>
                            </div>
                            <div class="col-md-6">
                                <p>{{ __('Subscriptions revenue') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\DashboardServiceProvider::getTotalSubscriptionsRevenue()) }}
                                </h3>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <p>{{ __('Total transactions') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getTotalTransactionsCount() }}</h3>
                            </div>
                            <div class="col-md-6">
                                <p class="m-0">{{ __('Total amount earned') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\DashboardServiceProvider::getTotalEarned()) }}
                                </h3>
                            </div>
                        </div>
                        <span class="pull-right">
                            <a href="admin/transactions" class="primary-link">{{ __('Go to payments') }} ››</a>
                        </span>
                    </div>

                </div>
            </div>

            <div class="mb-4 col-md-4">
                <div class="card shadow rounded p-5">
                    <div class="card-body text-muted font-weight-medium">
                        <div>
                            <p class="font-weight-bolder">Content</p>
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <p>{{ __('Total posts') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getPostsCount() }}
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <p>{{ __('Post attachments') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getPostAttachmentsCount() }}
                                </h3>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <p>{{ __('Post comments') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getPostCommentsCount() }}
                                </h3>
                            </div>
                            <div class="col-md-6">
                                <p class="m-0">{{ __('Total reactions') }}:</p>
                                <h3 class="font-weight-bolder">
                                    {{ \App\Providers\DashboardServiceProvider::getReactionsCount() }}
                                </h3>
                            </div>
                        </div>
                        <span class="pull-right">
                            <a href="admin/user-posts" class="primary-link">{{ __('Go to content') }} ››</a>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row two-columns-graph-holder">
            @include('elements.admin.value_card', [
                'name' => 'newUsersValue',
                'route' => 'admin.metrics.new.users.value',
                'size' => 'col-xs-12 col-sm-12 col-md-6 col-lg-6',
                'title' => __('New users'),
                'form' => [
                    'trans' => [
                        ucfirst(trim(str_replace('1 ', '', trans_choice('days', 1, ['number' => 1])))),
                        ucfirst(trim(str_replace('2 ', '', trans_choice('days', 2, ['number' => 2])))),
                    ],
                    'function' => 'count',
                    'ranges' => [1, 7, 14, 30, 60, 90],
                    'range' => 30,
                ],
            ])
            @include('elements.admin.partition_card', [
                'name' => 'rolesPerUser',
                'chart' => [
                    'size' => 180,
                    'color' => '203, 12, 159',
                    'total' => true,
                ],
                'route' => 'admin.metrics.new.users.partition',
                'size' => 'col-xs-12 col-sm-12 col-md-6 col-lg-6',
                'title' => __('Users roles'),
                'form' => [
                    'function' => 'count',
                ],
            ])
        </div>
        <div class="row">
            @include('elements.admin.trend_card', [
                'name' => 'newUsersTrend',
                'chart' => [
                    'size' => 100,
                    'color_start' => 'rgba(255, 105, 220)',
                    'color_stop' => 'rgba(207, 60, 172, 0.5)',
                    'border_color' => 'rgba(203, 12, 159, 0.7)',
                    'point_radius' => 10,
                    'total' => true,
                ],
                'route' => 'admin.metrics.new.users.trend',
                'size' => 'col-12 col-lg-12 mb-4',
                'title' => __('Registered users'),
                'form' => [
                    'trans' => [
                        ucfirst(trim(str_replace('1 ', '', trans_choice('months', 1, ['number' => 1])))),
                        ucfirst(trim(str_replace('2 ', '', trans_choice('months', 2, ['number' => 2])))),
                    ],
                    'function' => 'count',
                    'unit' => 'month',
                    'ranges' => [3, 6, 12],
                    'range' => 12,
                ],
            ])
        </div>
    </div>
</div>
