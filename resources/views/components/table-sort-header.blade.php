@php
    $currentSort = request('sort', 'id');
    $currentDirection = request('direction', 'desc');
    $newDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
    $params = array_merge(request()->except(['sort','direction']), ['sort' => $field, 'direction' => $newDirection]);
    $routeName = $route ?? 'categories.search';
    $url = route($routeName, $params);
    $arrow = '';
    if ($currentSort === $field) {
        $arrow = $currentDirection === 'asc' ? '▲' : '▼';
    }
@endphp
<a href="{{ $url }}" class="hover:underline flex items-center">{!! $icon !!} {{ $label }} <span class="ml-1">{{ $arrow }}</span></a>
