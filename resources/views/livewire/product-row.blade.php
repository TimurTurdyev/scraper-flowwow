@php
$item = $product->data;
@endphp
<tr>
    <td class="ps-4">
        <p class="text-xs font-weight-bold mb-0">{{ $product->id }}</p>
    </td>
    <td>
        <div>
            <img src="{{ $item['images'][0] ?? '' }}" class="avatar avatar-sm me-3" alt="{{ $item['title'] }}">
        </div>
    </td>
    <td class="table-row-w400">
        <p class="text-xs font-weight-bold mb-0">{{ $item['title'] }}</p>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">[{{ $item['category_id'] }}]{{ $product->category?->name ?? '-' }}</p>
    </td>
    <td class="text-center">
        <div class="form-check form-switch ps-0 d-flex ">
            <input class="form-check-input ms-auto me-auto" type="checkbox" wire:model="yandex">
        </div>
    </td>
    <td class="text-center">
        <div class="form-check form-switch ps-0 d-flex ">
            <input class="form-check-input ms-auto me-auto" type="checkbox" wire:model="ozon">
        </div>
    </td>
    <td class="text-center">
        <span class="text-secondary text-xs font-weight-bold">{{ $product->updated_at }}</span>
    </td>
</tr>
