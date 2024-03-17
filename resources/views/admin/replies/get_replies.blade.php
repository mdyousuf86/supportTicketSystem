<div class="predefine-reply-area">
    <ul class="reply-list">
        @if ($prevFolder)
            <li class="file "><a href="javascript:void(0)" class="predefineReply"
                    data-category_id="{{ @$prevFolder->parent_id ?? 0 }}">
                    <span class="predefine-content">
                        <span class="icon"><i class="fas fa-arrow-left "></i></span>
                        @lang('Back')</a></li>
                    </span>
        @endif
        @forelse ($replyCategories as $replyCategory)
            <li class="reply-list__folder folder"><a href="javascript:void(0)" class="predefineReply"
                    data-category_id="{{ $replyCategory->id }}">
                    <span class="predefine-content">
                        <span class="icon"><i
                                class="fas fa-folder text--warning"></i></span>
                        {{ __($replyCategory->name) }}</a>
                    </span>
                </li>
        @empty
            {{-- <li>@lang('No reply categories found')</li> --}}
        @endforelse

        @foreach ($replies as $reply)
            <li class="file"><a href="javascript:void(0)" class="reply__name" data-reply="{{ $reply->reply }}">
                    <span class="icon icon-two"><i class="las la-file-alt"></i></span>
                    {{ __($reply->name) }}</a> {{ strLimit($reply->reply, 50) }}</li>
        @endforeach

    </ul>
</div>
