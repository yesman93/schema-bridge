<?php

use Lumio\View\Components;



$pagination = Components\Pagination::build(
    page: (int)($this->page ?? 0),
    per_page: (int)($this->per_page ?? 0),
    total: (int)($this->total ?? 0),
    base_uri: $this->base_uri ?? '',
    filters: $this->filters ?? '',
    sorting: $this->sorting ?? '',
    params: $this->params ?? [],
);

echo json_encode([
    'controls' => $pagination->get(),
    'counts_info' => $pagination->get_counts_info(),
]);





