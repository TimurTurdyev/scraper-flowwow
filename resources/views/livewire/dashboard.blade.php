<main class="main-content position-relative border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            @foreach( $statistics as $statistic )
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">{{ $statistic['name'] }}</p>
                                        <h5 class="font-weight-bolder mb-0">
                                            {{ $statistic['total'] }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div
                                        class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                        <i class="{{ $statistic['icon'] }} text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row my-4">
            <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
                <livewire:categories></livewire:categories>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6>Ozon statistic</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-sm">
                            <span class="font-weight-bold">
                                Всего {{ $ozonStatistic['total']['usage'] }}
                            </span>
                            из {{ $ozonStatistic['total']['limit'] }} запросов
                            <br>
                            <span class="font-weight-bold">
                                Обновить {{ $ozonStatistic['daily_update']['usage'] }}
                            </span>
                            из {{ $ozonStatistic['daily_update']['limit'] }} запросов
                            <br>
                            <span class="font-weight-bold">
                                Создать {{ $ozonStatistic['daily_create']['usage'] }}
                            </span>
                            из {{ $ozonStatistic['daily_create']['limit'] }} запросов
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
