<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductVariantsExport implements FromView
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function view(): View
    {
        $variants = $this->query->get();
        return view('exports.product_variants', compact('variants'));
    }
}
