@extends('layouts.main')

@section('title', 'Ihre Benachrichtigungen')
@section('content')

<h1>Aktuelle Benachrichtigungen</h1>

@foreach ($notifications as $notification)
<div class="notify alert alert-info" data-notifyid="{{{ $notification->getModel()->id }}}">
    @if(isset($notification->done))
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    @endif
    @if(isset($notification->link))
    <a href="{{{ $notification->link }}}" class="alert-link">{{{ $notification }}}</a>
    @else
    <a href="#" class="alert-link">{{{ $notification }}}</a>
    @endif
</div>
@endforeach
@stop