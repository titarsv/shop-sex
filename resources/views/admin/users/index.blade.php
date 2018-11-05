@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    {{ $title }}
@endsection
@section('content')

    <div class="content-title">
        <div class="row">
            <div class="col-sm-12">
                <h1>{{ $title }}</h1>
            </div>
        </div>
    </div>

    @if (session('message-success'))
        <div class="alert alert-success">
            {{ session('message-success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('message-error'))
        <div class="alert alert-danger">
            {{ session('message-error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @forelse($users as $user)
            <div class="col-sm-3">
                <div class="panel-group user-info">
                    <div class="panel panel-default">
                        <div class="panel-avatar">
                            <div class="avatar-container">
                                <img
                                        @if(in_array('user', $user->role())) src="/img/user-reg.png"
                                        @elseif(in_array('unregister_user', $user->role())) src="/img/user-unreg.png"
                                        @endif
                                        alt="user-avatar" />
                            </div>
                        </div>
                        <div class="panel-heading">
                            <p class="name">{!! $user->first_name or '' !!} {!! $user->last_name or '' !!}</p>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-4"><label>Телефон:</label></div>
                                <div class="col-sm-8"><p class="info">{!! $user->phone or '' !!}</p></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label>E-mail:</label></div>
                                <div class="col-sm-8"><p class="info">{!! $user->email or '' !!}</p></div>
                            </div>
                            <ul class="nav row">
                                <li class="col-xs-4">
                                    <a href="/admin/users/stat/{!! $user->id !!}" data-toggle="tooltip" data-placement="top" title="Список заказов">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        <span class="badge">{{ count($user->orders) }}</span>
                                    </a>
                                </li>
                                <li class="col-xs-4">
                                    <a href="/admin/users/reviews/{!! $user->id !!}" data-toggle="tooltip" data-placement="top" title="Список отзывов">
                                        <i class="fa fa-comments" aria-hidden="true"></i>
                                        <span class="badge">{{ count($user->reviews) }}</span>
                                    </a>
                                </li>
                                <li class="col-xs-4">
                                    <a href="/admin/users/wishlist/{!! $user->id !!}"  data-toggle="tooltip" data-placement="top" title="Список закладок">
                                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                                        <span class="badge">{{ $user->wishlist()->count() }}</span>
                                    </a>
                                </li>
                            </ul>
                            <a class="btn btn-primary" href="/admin/users/edit/{!! $user->id !!}">Редактировать</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Пока нет пользователей.</h4>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if(!$users->isEmpty())
        <div class="panel-footer text-right">
            {{ $users->links() }}
        </div>
    @endif

@endsection
