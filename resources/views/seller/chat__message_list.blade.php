@foreach ($messages as $msg_index => $message)
    @if ($message->send_seller == $auth->id)
        <div class="chat-item chat-right" style="">
            <img src="{{ $auth->image ? asset($auth->image) : asset($defaultProfile->image) }}">
            <div class="chat-details">
                <div class="chat-text">{{ $message->message }}</div>
                <div class="chat-time">{{ $message->created_at->format('d F, Y, H:i A') }}</div>
            </div>
        </div>
    @else
        <div class="chat-item chat-left" style="">
            <img src="{{ $customer->image ? asset($customer->image) : asset($defaultProfile->image) }}">
            <div class="chat-details">
                <div class="chat-text">{{ $message->message }}</div>
                <div class="chat-time">{{ $message->created_at->format('d F, Y, H:i A') }}</div>
            </div>
        </div>
    @endif
@endforeach
