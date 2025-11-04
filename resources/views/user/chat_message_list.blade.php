@foreach ($messages as $msg_index => $message)
    @if($message->send_customer == $auth->id)
        <div class="wsus__chat_single single_chat_2">
            <div class="wsus__chat_single_img">
            <img src="{{ $auth->image ? asset($auth->image) : asset($defaultProfile->image) }}" alt="user" class="img-fluid">
            </div>
            <div class="wsus__chat_single_text">
            <p>{{ $message->message }}</p>
            <span>{{ $message->created_at->format('d F, Y, H:i A') }}</span>
            </div>
        </div>
    @else
        <div class="wsus__chat_single">
            <div class="wsus__chat_single_img">
            <img src="{{ $seller->image ? asset($seller->image) : asset($defaultProfile->image) }}" alt="user" class="img-fluid">
            </div>
            <div class="wsus__chat_single_text">
            <p>{{ $message->message }}</p>
            <span>{{ $message->created_at->format('d F, Y, H:i A') }}</span>
            </div>
        </div>
    @endif
@endforeach
