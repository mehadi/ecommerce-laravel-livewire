{{-- Thin delegating wrapper: real implementation lives in x-admin.sortable-th --}}
@props([
    'field',
    'label',
    'sortField' => null,
    'sortDirection' => 'asc',
    'align' => 'left',
])

<x-admin.sortable-th :field="$field" :label="$label" :sort-field="$sortField" :sort-direction="$sortDirection" :align="$align" />
