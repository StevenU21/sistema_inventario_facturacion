@php
    $currentSort = request('sort', 'id');
    $currentDirection = request('direction', 'desc');
    $newDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
    $params = array_merge(request()->except(['sort','direction']), ['sort' => $field, 'direction' => $newDirection]);
    // Si no se pasa la ruta, usar el primer segmento de la URL actual para armar el nombre del recurso
    if (!isset($route)) {
        $firstSegment = request()->segment(1);
        $routeName = $firstSegment ? $firstSegment . '.search' : 'categories.search';
    } else {
        $routeName = $route;
    }
    $url = route($routeName, $params);
    $arrow = '';
    if ($currentSort === $field) {
        $arrow = $currentDirection === 'asc' ? '▲' : '▼';
    }
@endphp
<a href="{{ $url }}" class="hover:underline flex items-center">{!! $icon !!} {{ $label }} <span class="ml-1">{{ $arrow }}</span></a>
