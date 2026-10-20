<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LiveTable
{
    public const PER_PAGE = 10;

    public static function search(Request $request, string $param = 'q'): string
    {
        return trim((string) $request->query($param, ''));
    }

    public static function paginate($query, Request $request, string $pageName = 'page'): LengthAwarePaginator
    {
        return $query->paginate(self::PER_PAGE, ['*'], $pageName)->withQueryString();
    }

    public static function paginateCollection(Collection $items, Request $request, string $pageName = 'page'): LengthAwarePaginator
    {
        $page = max(1, (int) $request->query($pageName, 1));

        return (new LengthAwarePaginator(
            $items->forPage($page, self::PER_PAGE)->values(),
            $items->count(),
            self::PER_PAGE,
            $page,
            [
                'path' => $request->url(),
                'pageName' => $pageName,
            ]
        ))->withQueryString();
    }

    public static function matches(?string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        return str_contains(mb_strtolower((string) $haystack), mb_strtolower($needle));
    }

    public static function isAjaxFor(Request $request, string $table): bool
    {
        return $request->ajax() && $request->query('_table') === $table;
    }
}
