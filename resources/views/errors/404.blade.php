@extends('public.layouts.main')
@section('meta')
    <title>{{ trans('app.error_404_page_not_found') }}</title>
@endsection
@section('content')
    <section class="section-1 err404" style="background: #000 center url(/images/main.jpg) no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-7 col-xs-8">
                    <p class="main-title">{{ trans('app.error_404') }}</p>
                    <p class="main-title">{{ trans('this_page_seems_to_be_right_now_a_friend_is_spending_the_night_or_she_does_not_exist') }}</p>
                    <span>{{ trans('app.we_have_already_called_the_police_and_they_are_working_on_this_issue_we_are_sorry_for_the_inconvenience') }}</span>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <a href="/" class="banner-btn" onclick="window.history.back();">< {{ trans('app.back_to') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection