@extends('layouts.app')

@section('title', __('messages.reprise_intro.page_title'))

@section('content')
<style>
    .reprise-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 40px;
        margin: 60px auto;
        max-width: 1200px;
        padding: 0 20px;
        flex-wrap: wrap;
    }

    .box {
        width: 400px;
        background-color: #ffd900;
        color: #003b87;
        padding: 30px;
        border-radius: 30px;
        font-size: 16px;
        line-height: 1.5;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .box h3 {
        font-size: 20px;
        margin-bottom: 15px;
    }

    .box.right {
        background-color: #003b87;
        color: white;
    }

    .reprise-btn {
        display: inline-block;
        margin: 40px auto 0;
        background-color: #003b87;
        color: #ffd900;
        font-weight: bold;
        padding: 15px 30px;
        border-radius: 0 30px 0 0;
        font-size: 20px;
        text-decoration: none;
        transition: 0.3s ease;
    }

    .reprise-btn:hover {
        background-color: #002d66;
    }
</style>

<div class="reprise-container">
    <div class="box">
        <h3>{{ __('messages.reprise_intro.box1_title_1') }}<br>{{ __('messages.reprise_intro.box1_title_2') }}</h3>
        <p>
            {!! __('messages.reprise_intro.box1_p') !!}
        </p>
    </div>

    <div class="box right">
        <h3>{{ __('messages.reprise_intro.box2_title_1') }}<br>{{ __('messages.reprise_intro.box2_title_2') }}</h3>
        <p>
            {{ __('messages.reprise_intro.box2_intro') }}
            <ul>
                <li>{{ __('messages.reprise_intro.li1') }}</li>
                <li>{{ __('messages.reprise_intro.li2') }}</li>
                <li>{{ __('messages.reprise_intro.li3') }}</li>
                <li>{{ __('messages.reprise_intro.li4') }}</li>
            </ul>
            {{ __('messages.reprise_intro.box2_outro') }}
        </p>
    </div>
</div>

<div style="text-align:center;">
    <a href="{{ route('reprise.form') }}" class="reprise-btn">{{ __('messages.reprise_intro.btn') }} ></a>
</div>
@endsection
