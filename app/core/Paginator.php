<?php

class Paginator
{
    public static function meta(int $page, int $perPage, int $total): array
    {
        $totalPages = (int) ceil($total / $perPage);
        return [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }
}

