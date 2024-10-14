<div class="col">
    <div class="metrics-container">
        <div class="chartGraphic row mt-3">
            @include('elements.admin.trend_card', [
            'name' => 'newUsersTrend',
            'chart' => [
            'size' => 100,
            'color_start' => '#8B3BA460',
            'color_stop' => '#8B3BA460',
            'border_color' => '#8B3BA4',
            'point_radius' => 10,
            'total' => false,
            ],
            'route' => 'admin.metrics.new.users.trend', // Alteração aqui
            'size' => 'col-12 col-lg-12 mb-4',
            'title' => '',
            'form' => [
            'trans' => [
            ucfirst(trim(str_replace('1 ', '', trans_choice('day', 1, ['number' => 1])))),
            ucfirst(trim(str_replace('2 ', '', trans_choice('days', 2, ['number' => 2])))),
            ],
            'function' => 'count',
            'unit' => 'day',
            'ranges' => [7, 14, 30, 90, 180],
            'range' => 7,
            ],
            ])
        </div>
        <div class="mt-2 mb-2">
            <input class="dataFilter" type="date" />
        </div>
        <div class="parent">
            <div class="div1">
                <div id="metric1" class="cardMetric no-blur-effect">
                    <div class="headerCardMetric d-flex">
                        <p class="font-weight-bolder dashCardTitle">Registro</p>
                        <button class="metricsBtn" onclick="showMetrics(metric1)">
                            <i class="voyager-eye"></i>
                        </button>
                    </div>
                    <p class="dashCardMetric">
                        {{ \App\Providers\DashboardServiceProvider::getLast24HoursRegisteredUsersCount() }}
                    </p>
                    <p class="text-uppercase dashCardLabel">Usuários</p>
                </div>
            </div>
            <div class="div2">
                <div id="metric2" class="cardMetric no-blur-effect">
                    <div class="headerCardMetric d-flex">
                        <p class="font-weight-bolder dashCardTitle">Assinantes</p>
                        <button class="metricsBtn" onclick="showMetrics(metric2)">
                            <i class="voyager-eye"></i>
                        </button>
                    </div>
                    <p class="dashCardMetric">
                        {{ \App\Providers\DashboardServiceProvider::getActiveSubscriptionsCount() }}
                    </p>
                    <p class="text-uppercase dashCardLabel">Assinantes</p>
                </div>
            </div>


            <div class="div3">
                <div id="metric3" class="cardMetric no-blur-effect">
                    <div class="headerCardMetric d-flex">
                        <p class="font-weight-bolder dashCardTitle">Produtores de conteúdo</p>
                        <button class=" metricsBtn" onclick=" showMetrics(metric3)">
                            <i class=" voyager-eye"></i>
                        </button>
                    </div>
                    <p class="dashCardMetric">{{ \App\Providers\DashboardServiceProvider::influencerAmount() }}</p>
                    <p class="text-uppercase dashCardLabel">Produtores</p>
                </div>
            </div>
            <div class="div4">
                <div id="metric4" class="cardMetric no-blur-effect">
                    <div class="headerCardMetric d-flex">
                        <p class="font-weight-bolder dashCardTitle">Faturamento</p>
                        <button class=" metricsBtn" onclick=" showMetrics(metric4)">
                            <i class="voyager-eye"></i>
                        </button>
                    </div>
                    <!-- <p>{{ __('Active subscriptions') }}: {{ \App\Providers\DashboardServiceProvider::getActiveSubscriptionsCount() }}</p>
                        <p>{{ __('Subscriptions revenue') }}: {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\DashboardServiceProvider::getTotalSubscriptionsRevenue()) }}</p>
                        <p>{{ __('Total transactions') }}: {{ \App\Providers\DashboardServiceProvider::getTotalTransactionsCount() }}</p> -->
                    <p class="dashCardMetric">
                        {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\DashboardServiceProvider::getTotalEarned()) }}
                    </p>
                    <p class="text-uppercase dashCardLabel">Total</p>
                </div>
            </div>
            <div class="div5">
                <div id="metric5" class="cardMetric no-blur-effect">
                    <div class="headerCardMetric d-flex">
                        <p class="dashCardTitle">Comissão</p>
                        <button class="metricsBtn" onclick="showMetrics(metric5)">
                            <i class=" voyager-eye"></i>
                        </button>
                    </div>
                    <p class="dashCardMetric">
                        {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(\App\Providers\DashboardServiceProvider::comissionPaid()) }}
                    </p>
                    <p class="text-uppercase dashCardLabel">Total</p>
                    <!-- <p>{{ __('Post attachments') }}: {{ \App\Providers\DashboardServiceProvider::getPostAttachmentsCount() }}</p>
                        <p>{{ __('Post comments') }}: {{ \App\Providers\DashboardServiceProvider::getPostCommentsCount() }}</p>
                        <p class="m-0">{{ __('Total reactions') }}: {{ \App\Providers\DashboardServiceProvider::getReactionsCount() }}</p>
                        <span class="pull-right"><a href="admin/user-posts" class="primary-link">{{ __('Go to content') }} ››</a></span> -->
                </div>
            </div>
            <div class="div6">
                <div id="metric6" class="cardMetric no-blur-effect">
                    <div class="headerCardMetric d-flex">
                        <p class="dashCardTitle">Total de Postagens</p>
                        <button class="metricsBtn" onclick="showMetrics(metric6)">
                            <i class="voyager-eye"></i>
                        </button>
                    </div>
                    <p class="dashCardMetric">{{ \App\Providers\DashboardServiceProvider::getPostsCount() }}</p>
                    <p class="text-uppercase dashCardLabel">Post´s</p>
                    <!-- <p>{{ __('Post attachments') }}: {{ \App\Providers\DashboardServiceProvider::getPostAttachmentsCount() }}</p>
                        <p>{{ __('Post comments') }}: {{ \App\Providers\DashboardServiceProvider::getPostCommentsCount() }}</p>
                        <p class="m-0">{{ __('Total reactions') }}: {{ \App\Providers\DashboardServiceProvider::getReactionsCount() }}</p>
                        <span class="pull-right"><a href="admin/user-posts" class="primary-link">{{ __('Go to content') }} ››</a></span> -->
                </div>
            </div>
            <div class="div7">
                <div class="cardMetricLabel">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                        <path fill="#8c3ba5"
                            d="M400 0L176 0c-26.5 0-48.1 21.8-47.1 48.2c.2 5.3 .4 10.6 .7 15.8L24 64C10.7 64 0 74.7 0 88c0 92.6 33.5 157 78.5 200.7c44.3 43.1 98.3 64.8 138.1 75.8c23.4 6.5 39.4 26 39.4 45.6c0 20.9-17 37.9-37.9 37.9L192 448c-17.7 0-32 14.3-32 32s14.3 32 32 32l192 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-26.1 0C337 448 320 431 320 410.1c0-19.6 15.9-39.2 39.4-45.6c39.9-11 93.9-32.7 138.2-75.8C542.5 245 576 180.6 576 88c0-13.3-10.7-24-24-24L446.4 64c.3-5.2 .5-10.4 .7-15.8C448.1 21.8 426.5 0 400 0zM48.9 112l84.4 0c9.1 90.1 29.2 150.3 51.9 190.6c-24.9-11-50.8-26.5-73.2-48.3c-32-31.1-58-76-63-142.3zM464.1 254.3c-22.4 21.8-48.3 37.3-73.2 48.3c22.7-40.3 42.8-100.5 51.9-190.6l84.4 0c-5.1 66.3-31.1 111.2-63 142.3z" />
                    </svg>
                    <p class="cardMetricTextLabel">Top Produtores</p>
                </div>
                <table class="RankingTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Usuário</th>
                            <th>Assinantes</th>
                            <th>Faturamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $topInfluencers = \App\Providers\DashboardServiceProvider::topInfluencerList();
                        @endphp
                        @foreach ($topInfluencers as $index => $influencer)
                        <tr>
                            <td class="rankingNumber">{{ $index + 1 }}</td>
                            <td class="col-name">{{ $influencer->username }}</td>
                            <td class="col-transaction">
                                {{ \App\Providers\DashboardServiceProvider::getSubscriberRank($influencer->id) }}
                            </td>
                            <td class="col-totalEarned">R$
                                {{ number_format($influencer->total_earned, 2, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        let metric1 = document.getElementById("metric1");
        let metric2 = document.getElementById("metric2");
        let metric3 = document.getElementById("metric3");
        let metric4 = document.getElementById("metric4");
        let metric5 = document.getElementById("metric5");
        let metric6 = document.getElementById("metric6");

        let dataFilter = document.querySelector(".dataFilter")

        let dateForm = document.querySelector(".dateForm");

        function showMetrics(divElement) {
            divElement.classList.toggle('blur-effect');
            divElement.classList.toggle('no-blur-effect');
        }
        async function sendParameter() {
            let dataFiltered = dataFilter.value;
            let url = `${window.location.origin}/admin?date=${encodeURIComponent(dataFiltered)}`;

            window.location.href = url;

            const urlParams = new URLSearchParams(window.location.search);
            const date = urlParams.get('date');
        }


        dataFilter.addEventListener('change', sendParameter);

        function getTodayDate() {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            return `${year}-${month}-${day}`;
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            const url = new URL(window.location.href);

            const dateValue = url.searchParams.get('date');
            dataFilter.value = dateValue;
        });
    </script>
</div>