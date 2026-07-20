<?php

namespace Cachet\Mcp\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Number;
use Laravel\Mcp\Request;

trait InteractsWithPagination
{
    protected function perPage(Request $request): int
    {
        return Number::clamp($request->integer('per_page', 15), min: 1, max: 100);
    }

    protected function page(Request $request): int
    {
        return max(1, $request->integer('page', 1));
    }

    /**
     * @return array{current_page: int, per_page: int, has_more: bool}
     */
    protected function paginationMeta(Paginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'has_more' => $paginator->hasMorePages(),
        ];
    }
}
