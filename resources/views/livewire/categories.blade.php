<div class="card">
    <div class="card-header pb-0">
        <h6>Список категорий и сопоставленные ozon категории</h6>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
                <thead>
                <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Название</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Товаров</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ozon
                        ID
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ozon
                        Name
                    </th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ozon
                        Updated
                    </th>
                    <th class="text-secondary opacity-7"></th>
                </tr>
                </thead>
                <tbody>
                @foreach( $categories as $category )
                    <tr>
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div class="me-3">
                                    {{ $category['id'] }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-xs text-secondary mb-0">{{ $category['name'] }}</p>
                        </td>
                        <td class="align-middle text-center text-sm">
                            @if( $category['products_count'] )
                                <span
                                    class="badge badge-sm bg-gradient-success">{{ $category['products_count'] }}</span>
                            @else
                                <span class="badge badge-sm bg-gradient-faded-dark">0</span>
                            @endif
                        </td>
                        <td class="align-middle text-center text-sm">
                            @if( $category['ozon_category_id'] )
                                <span
                                    class="badge badge-sm bg-gradient-success">{{ $category['ozon_category_id'] }}</span>
                            @else
                                <span class="badge badge-sm bg-gradient-faded-dark">-</span>

                            @endif
                        </td>
                        <td class="align-middle text-center" style="max-width: 300px; white-space: normal;">
                            <span
                                class="text-secondary text-xs font-weight-bold text-break">{{ $category['ozon_category']['name'] ?? '-' }}</span>
                        </td>
                        <td class="align-middle text-center">
                            <span
                                class="text-secondary text-xs font-weight-bold">{{  $category['updated_at'] }}</span>
                        </td>
                        <td class="align-middle">
                            <button class="btn btn-block btn-sm bg-gradient-primary mb-0"
                                    wire:click.prevent="$emit('editCategory', {{ $category['id'] }})"
                                    data-bs-toggle="modal" data-bs-target="#modal-categories"
                            >
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="modal-categories" tabindex="-1" role="dialog"
         aria-labelledby="modal-categories" aria-hidden="true">
        <div class="modal-dialog modal- modal-dialog-centered modal-" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="modal-title-default">Поиск категорий OZON</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header pb-0">
                            <input type="text" class="form-control" wire:model.debounce.500ms="search">
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Ozon ID
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Path
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $matchCategories as $id => $category )
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="me-3">
                                                        {{ $id }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           value="{{ $id }}"
                                                           id="check{{ $id }}"
                                                           wire:model="checkOzonCategory"
                                                    >
                                                    <label class="custom-control-label"
                                                           for="check{{ $id }}">{{ $category }}</label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn bg-gradient-primary" wire:click.prevent="saveOzonCategory">
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('modalClose', event => {
            let myModalEl = document.getElementById('modal-categories')
            let modal = bootstrap.Modal.getInstance(myModalEl) // Returns a Bootstrap modal instance
            modal.hide();
        });
    </script>
</div>
