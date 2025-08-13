<div>
@foreach ($assets as $asset)
    @if ($asset->transactions->sum('quantity') !== 0)
        <p>
            {{ $asset->name }} — Quantity: {{ $asset->transactions->sum('quantity')  }} — Transactions value: {{ number_format($asset->transactions->sum('total_value'), 2 ,',', ' ') }} {{ $asset->transactions->first()->currency}} — Current value {{ number_format($asset->transactions->sum('quantity') * 477777, 2 ,',', ' ')  }} {{ $asset->transactions->first()->currency}}
        </p>
    @endif
@endforeach


</div>
