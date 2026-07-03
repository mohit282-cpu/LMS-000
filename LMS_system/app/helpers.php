<?php

declare(strict_types=1);

use App\Core\Csrf;
use App\Core\Session;

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

    if ($base === '/') {
        $base = '';
    }

    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">';
}

function old(string $key, mixed $default = ''): mixed
{
    $old = Session::peekFlash('old', []);

    return is_array($old) && array_key_exists($key, $old) ? $old[$key] : $default;
}

function selected(mixed $actual, mixed $expected): string
{
    return (string) $actual === (string) $expected ? 'selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? 'checked' : '';
}

function field_error(array $errors, string $field): string
{
    if (! isset($errors[$field][0])) {
        return '';
    }

    return '<div class="invalid-feedback d-block">' . e($errors[$field][0]) . '</div>';
}

function pagination_links(array $pagination, string $path, array $query = []): string
{
    $lastPage = (int) ($pagination['last_page'] ?? 1);
    $currentPage = (int) ($pagination['page'] ?? 1);

    if ($lastPage <= 1) {
        return '';
    }

    $html = '<nav aria-label="Table pagination"><ul class="pagination pagination-sm mb-0">';

    for ($page = 1; $page <= $lastPage; $page++) {
        $query['page'] = $page;
        $href = url($path . '?' . http_build_query(array_filter($query, static fn (mixed $value): bool => $value !== '' && $value !== null)));
        $active = $page === $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . e($href) . '">' . $page . '</a></li>';
    }

    $html .= '</ul></nav>';

    return $html;
}
