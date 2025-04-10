<div class="py-2 space-y-2">

    @if ($saved)
        <div class="bg-green-100 bg-opacity-60 border border-green-100 text-green-800 px-4 py-2 rounded mb-4 transition-opacity duration-300 text-sm">
            🎉 Updated! <a href="" class="underline hover:text-green-600">Click here</a> to refresh the page if you need to make additional changes.
        </div>
    @else

        <label class="block text-sm font-medium text-gray-600">Customers Related to Work:</label>
        <select wire:model="selectedCustomerIds"
                multiple
                id="customer-select"
                class="w-full border rounded p-2">
            @foreach ($allCustomers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>

        <button
            wire:click="syncCustomers"
            class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500"
        >
            🤌 Update Customers
        </button>

    @endif
</div>

@push('scripts')
    <script>
        function initTomSelect() {
            const select = document.getElementById('customer-select');
            if (select && !select.tomselect) {
                new TomSelect(select, {
                    plugins: ['remove_button'],
                    maxItems: null,
                });
            }
        }

        document.addEventListener('DOMContentLoaded', initTomSelect);

        Livewire.hook('message.processed', (message, component) => {
            initTomSelect();
        });
    </script>
@endpush
