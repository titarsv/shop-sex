@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Библиотека файлов
@endsection
@section('content')
<div class="upload-php">
    <div class="wrap" id="wp-media-grid" data-search="">
        <h1 class="wp-heading-inline">Библиотека файлов</h1>

        @if($user->hasAccess(['media.create']))
        <a href="/admin/media-new" class="page-title-action aria-button-if-js" role="button"
           aria-expanded="false">Добавить новый</a>
        @endif
        <hr class="wp-header-end">
        <ul class="subsubsub">
            <li class="all"><a href="/admin/media"{!! empty($trash) ? ' class="current"' : '' !!} aria-current="page">Опубликованные <span class="count">({{ $active }})</span></a> |</li>
            <li class="trash"><a href="/admin/media/trash"{!! !empty($trash) ? ' class="current"' : '' !!}>Удалённые <span class="count">({{ $trashed }})</span></a></li>
        </ul>
    </div>
</div>
@endsection
@include('admin.media.assets', ['query_vars' => ['trash' => $is_trash]])
@section('after_footer')
    {{--<script type='text/javascript' src='/js/larchik/hoverIntent.js'></script>--}}
    {{--<script type='text/javascript' src='/js/larchik/common.js'></script>--}}
    {{--<script type='text/javascript' src='/js/larchik/admin-bar.js'></script>--}}
    {{--<script type='text/javascript' src='/js/larchik/mce-view.js'></script>--}}
    {{--<script type='text/javascript' src='/js/larchik/imgareaselect/jquery.imgareaselect.min.js'></script>--}}
    {{--<script type='text/javascript' src='/js/larchik/image-edit.js'></script>--}}
    <script type='text/javascript' src='/js/larchik/media-grid.js'></script>
    <script type='text/javascript' src='/js/larchik/media.js'></script>
    <script type='text/javascript' src='/js/larchik/svg-painter.js'></script>
@endsection