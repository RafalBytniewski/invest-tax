<div>
       {{-- NEW TRANSACTION MODAL --}}
<x-modal wire:model="showModal" :wallets="$wallets" :assets="$assets" :types="$types" :mode="$mode" :transaction-type="$type">
</x-modal>


</div>
