<section id="pagination">
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            @if ($paginator->onFirstPage())

            @else
                <li class="page-item">
                    <a class="page-link" onclick="loadAjaxProduct('{{ $paginator->previousPageUrl() }}')" href="javascript:;" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (count($element) < 2)

                @else
                    @foreach ($element as $key => $el)
                        @if ($key == $paginator->currentPage())
                            <li class="page-item"><a class="page-link page_active" href="javascript::void()">{{ $key }}</a></li>
                        @else
                            <li class="page-item"><a class="page-link" onclick="loadAjaxProduct('{{ $el }}')" href="javascript:;">{{ $key }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" onclick="loadAjaxProduct('{{ $paginator->nextPageUrl() }}')" href="javascript:;" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @endif


        </ul>
    </nav>
</section>
