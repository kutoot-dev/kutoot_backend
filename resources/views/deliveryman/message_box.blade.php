@foreach ($deliveryMessage as $message)
@if ($message->sent_by=='customer')
<div class="row">
    <div class="col-md-6">
        <div class="card bg-primary">
            <div class="card-body">
                <img src="{{ $message->customer->image!=null?asset($message->customer->image):asset('backend/img/avatar/avatar-1.png') }}" height="50" width="50" style="border-radius:50%; float:left; overflow:hidden; margin-right:20px" alt="">
                <div class="text">
                    <span>{{ $message->message }}</span> <br>
                    <span>{{ \Carbon\Carbon::now()<=$message->created_at->addSeconds(30)?'Just now':\Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6"></div>
</div>
@elseif($message->sent_by=='deliveryman')
<div class="row">
    <div class="col-md-6"></div>
    <div class="col-md-6">
        <div class="card bg-primary">
            <div class="card-body">
                <div class="text" style="float: left; overflow:hidden; text-align:right; width:83%">
                <span>{{ $message->message }}</span> <br>
                <span>{{ \Carbon\Carbon::now()<=$message->created_at->addSeconds(30)?'Just now':\Carbon\Carbon::parse($message->created_at)->diffForHumans() }}</span>
                </div>
                <img src="{{ $message->deliveryman->man_image!=null?asset($message->deliveryman->man_image):asset('backend/img/avatar/avatar-2.png') }}" height="50" width="50" style="border-radius:50%; float:right; overflow:hidden;" alt="">
            </div>
        </div>
    </div>
</div>
@endif

@endforeach