<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in! || Balance: ${{number_format(Auth::user()->balance, 2)}}
                </div>
            </div>
        </div>
    </div>
    <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2><center>Deposit Now</center></h2>
                    <form method="POST" action="{{ route('paynow') }}">
                        @csrf

                        <!-- Amount Address -->
                        <div>
                            <x-label for="amount" :value="__('Amount')" />

                            <x-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount')" required autofocus />
                        </div>

                        <div class="flex items-center justify-center mt-4">
                            <x-button class="ml-3">
                                {{ __('Deposit Now') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $deposits = \App\Models\Deposit::where('user_id', auth()->user()->id)->latest()->get();
    @endphp

    <h1><center>Deposit History</center></h1>

    @if (count($deposits) > 0)
        @foreach ($deposits as $data)
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            Amount: ${{$data->amount}} || Crypto Value: {{$data->amount_paid}} {{$data->coin_paid}} || Trx: {{$data->trans_id}} || Payment Ref: {{$data->payment_ref}} || Status: @if($data->status ==1) <font color="green">Successful</font> @else <font color="red">Pending</font> @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <center>No Deposit Yet</center>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
