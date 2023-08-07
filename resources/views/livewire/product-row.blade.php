@php
$item = $product->data;
@endphp
<tr>
    <td class="ps-4">
        <p class="text-xs font-weight-bold mb-0">{{ $product->id }}</p>
    </td>
    <td>
        <div>
            <img src="{{ $item['images'][0] ?? '' }}" class="avatar avatar-sm me-3">
        </div>
    </td>
    <td class="text-center">
        <p class="text-xs font-weight-bold mb-0">{{ $item['title'] }}</p>
    </td>
    <td class="text-center">
        <p class="text-xs font-weight-bold mb-0">{{ $product->category?->name ?? '-' }}</p>
    </td>
    <td class="text-center">
        <p class="text-xs font-weight-bold mb-0">{!! implode('<br>', $item['categories']) !!}</p>
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
