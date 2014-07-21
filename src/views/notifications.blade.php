@foreach ($notifications as $notification)
<div class="notify alert alert-info" data-notifyid="{{{ $notification->getModel()->id }}}">
	@if(isset($notification->done))
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	@endif
	<a href="{{{ $notification->getActionLink() }}}" class="alert-link">{{{ $notification }}}</a>
</div>
@endforeach

@if (isset($links) && ! empty($links))
	{{ $links }}
@endif