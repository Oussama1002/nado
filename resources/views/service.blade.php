@extends('layouts.app')

@section('content')
<div class="bg-white py-12 px-4 sm:px-10 text-gray-800">

    {{-- Intro Section --}}
    <div class="max-w-6xl mx-auto mb-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-center mb-6">
            {{ __('messages.service.title_prefix') }} <span class="text-yellow-500">NADO.ma</span> {{ __('messages.service.title_and') }} <span class="text-yellow-500">AUTO ENCHÈRE</span> : {{ __('messages.service.title_suffix') }}
        </h2>
        <div class="flex flex-col md:flex-row items-center gap-6">
            <img src="{{ asset('images/service-header.png') }}" alt="{{ __('messages.service.alt_header') }}" class="w-full md:w-1/2 rounded shadow">
            <p class="text-lg leading-relaxed md:w-1/2">
                {!! __('messages.service.intro_p1') !!}<br><br>
                {{ __('messages.service.intro_p2') }}
            </p>
        </div>
    </div>

    {{-- Pourquoi choisir AUTO ENCHÈRE --}}
    <div class="bg-gray-50 py-16">
        <h3 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-8">
            {{ __('messages.service.why_choose') }} <span class="text-yellow-500">AUTO ENCHÈRE</span>
        </h3>

        <div class="max-w-3xl mx-auto text-center bg-blue-700 text-white p-6 rounded-md shadow-md mb-12">
            <p class="text-lg">
                {{ __('messages.service.why_box') }}
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto px-4 text-center">
            <div class="bg-yellow-400 p-6 rounded-xl shadow-lg">
                <img src="{{ asset('icons/expertise.png') }}" alt="{{ __('messages.service.alt_expertise') }}" class="w-12 h-12 mx-auto mb-4">
                <h4 class="font-bold text-lg mb-2">{{ __('messages.service.f1_title') }}</h4>
                <p>{{ __('messages.service.f1_desc') }}</p>
            </div>

            <div class="bg-yellow-400 p-6 rounded-xl shadow-lg">
                <img src="{{ asset('icons/solutions.png') }}" alt="{{ __('messages.service.alt_solutions') }}" class="w-12 h-12 mx-auto mb-4">
                <h4 class="font-bold text-lg mb-2">{{ __('messages.service.f2_title') }}</h4>
                <p>{{ __('messages.service.f2_desc') }}</p>
            </div>

            <div class="bg-yellow-400 p-6 rounded-xl shadow-lg">
                <img src="{{ asset('icons/partenariat.png') }}" alt="{{ __('messages.service.alt_partners') }}" class="w-12 h-12 mx-auto mb-4">
                <h4 class="font-bold text-lg mb-2">{{ __('messages.service.f3_title') }}</h4>
                <p>{{ __('messages.service.f3_desc') }}</p>
            </div>
        </div>
    </div>

    {{-- Nos Engagements --}}
    <div class="text-center mt-20 max-w-6xl mx-auto">
        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">
            {{ __('messages.service.engage_title_1') }} <span class="text-yellow-500">{{ __('messages.service.engage_title_2') }}</span>
        </h3>
        <p class="text-lg leading-relaxed mb-10 px-4 md:px-0">
            {{ __('messages.service.engage_p') }}
        </p>
        <img src="{{ asset('images/blue-car.png') }}" alt="{{ __('messages.service.alt_blue_car') }}" class="w-full max-w-md mx-auto rounded shadow-md">
    </div>
</div>
@endsection
