<div class="d-flex justify-content-start align-items-center">

    <!-- Status Toggle Button -->
    @if($editPermission ?? false)
        @php
            $isVisible = ($currentStatus ?? 'draft') === 'visible';
        @endphp
        <button
            class="btn {{ $isVisible ? 'btn-outline-success' : 'btn-outline-secondary' }} me-2 p-1 toggle-store-status"
            data-id="{{ $id }}"
            data-title="{{ $title }}"
            data-current-status="{{ $currentStatus ?? 'draft' }}"
            title="{{ $isVisible ? 'Set to Draft' : 'Set to Visible' }}"
        >
            @if($isVisible)
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                     class="icon icon-tabler icons-tabler-filled icon-tabler-toggle-right m-0">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M16 9a3 3 0 1 1 -3 3l.005 -.176a3 3 0 0 1 2.995 -2.824"/>
                    <path d="M16 5a7 7 0 0 1 0 14h-8a7 7 0 0 1 0 -14zm0 2h-8a5 5 0 1 0 0 10h8a5 5 0 0 0 0 -10"/>
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                     class="icon icon-tabler icons-tabler-filled icon-tabler-toggle-left m-0">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M8 9a3 3 0 1 0 3 3l-.005 .176a3 3 0 0 0 -2.995 2.824"/>
                    <path d="M8 5a7 7 0 0 0 0 14h8a7 7 0 0 0 0 -14zm0 2h8a5 5 0 0 1 0 10h-8a5 5 0 0 1 0 -10"/>
                </svg>
            @endif
        </button>
    @endif

    <!-- Use existing partial-actions component for edit/delete/view -->
    <x-partial-actions 
        modelName="{{ $modelName }}" 
        id="{{ $id }}" 
        title="{{ $title }}" 
        mode="{{ $mode }}"
        route="{{ $route ?? null }}" 
        editPermission="{{ $editPermission ?? false }}"
        deletePermission="{{ $deletePermission ?? false }}"
        viewPermission="{{ $viewPermission ?? false }}"
    />

</div>