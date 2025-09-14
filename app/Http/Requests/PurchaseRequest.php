<?php

namespace App\Http\Requests;

use App\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('post')) {
            return $this->user()->can('create', Purchase::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->user()->can('update', $this->route('purchase'));
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Datos de la compra
            'reference' => ['nullable', 'string', 'min:3', 'max:255'],
            // subtotal y total se calculan en el servidor
            'entity_id' => ['required', 'exists:entities,id'],
            // En modo existente no es obligatorio seleccionar almacén; se infiere del inventario
            'warehouse_id' => ['nullable', 'exists:warehouses,id', 'required_if:product_mode,new'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],

            // Modo de producto: nuevo o existente
            'product_mode' => ['required', 'in:new,existing'],

            // Producto (opcional si se envía product.id); si no existe, se crea
            'product.id' => [
                'nullable',
                'exists:products,id',
                // Si el modo es existente, el id es requerido
                'required_if:product_mode,existing',
                // Si el modo es nuevo, no se permite enviar id
                'prohibited_if:product_mode,new',
            ],
            // Si el modo es nuevo, se requieren los campos; si es existente, se prohíben para evitar mezcla
            'product.name' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'string', 'max:255'],
            'product.description' => ['nullable', 'string'],
            'product.barcode' => ['nullable', 'string', 'max:255'],
            'product.code' => ['nullable', 'string', 'max:255'],
            'product.sku' => ['nullable', 'string', 'max:255'],
            'product.status' => ['nullable', 'in:available,discontinued,out_of_stock'],
            // Solo brand_id es obligatorio, category_id ya no existe en products
            'product.brand_id' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'exists:brands,id'],
            'product.tax_id' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'exists:taxes,id'],
            'product.unit_measure_id' => ['required_if:product_mode,new', 'prohibited_if:product_mode,existing', 'exists:unit_measures,id'],

            // Detalles (variantes)
            'details' => ['required', 'array', 'min:1'],
            'details.*.color_id' => ['nullable', 'exists:colors,id'],
            'details.*.size_id' => ['nullable', 'exists:sizes,id'],
            'details.*.quantity' => ['required', 'integer', 'min:1'],
            'details.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $salePrice = $this->input("details.{$index}.sale_price");
                    if ($salePrice !== null && $value > $salePrice) {
                        $fail('El precio unitario no puede ser mayor al precio de venta.');
                    }
                }
            ],
            'details.*.sale_price' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($value === null)
                        return;
                    $index = explode('.', $attribute)[1];
                    $unitPrice = $this->input("details.{$index}.unit_price");
                    if ($unitPrice !== null && $value < $unitPrice) {
                        $fail('El precio de venta no puede ser menor al precio unitario.');
                    }
                }
            ],
            'details.*.min_stock' => ['nullable', 'integer', 'min:0'],
            'details.*.sku' => ['nullable', 'string', 'max:255'],
            'details.*.code' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.min' => 'La referencia debe tener al menos :min caracteres.',
            'reference.max' => 'La referencia no debe exceder de :max caracteres.',
            'entity_id.required' => 'La entidad es obligatoria.',
            'entity_id.exists' => 'La entidad seleccionada no existe.',
            'warehouse_id.required_if' => 'El almacén es obligatorio cuando el modo de producto es nuevo.',
            'warehouse_id.exists' => 'El almacén seleccionado no existe.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.exists' => 'El método de pago seleccionado no existe.',
            'product_mode.required' => 'El modo de producto es obligatorio.',
            'product_mode.in' => 'El modo de producto seleccionado no es válido.',
            'product.id.required_if' => 'El producto es obligatorio cuando el modo es existente.',
            'product.id.exists' => 'El producto seleccionado no existe.',
            'product.id.prohibited_if' => 'No se debe enviar un producto cuando el modo es nuevo.',
            'product.name.required_if' => 'El nombre del producto es obligatorio cuando el modo es nuevo.',
            'product.name.prohibited_if' => 'No se debe enviar el nombre del producto cuando el modo es existente.',
            'product.name.string' => 'El nombre del producto debe ser una cadena de texto.',
            'product.name.max' => 'El nombre del producto no debe exceder de :max caracteres.',
            'product.description.string' => 'La descripción del producto debe ser una cadena de texto.',
            'product.barcode.string' => 'El código de barras debe ser una cadena de texto.',
            'product.barcode.max' => 'El código de barras no debe exceder de :max caracteres.',
            'product.code.string' => 'El código debe ser una cadena de texto.',
            'product.code.max' => 'El código no debe exceder de :max caracteres.',
            'product.sku.string' => 'El SKU debe ser una cadena de texto.',
            'product.sku.max' => 'El SKU no debe exceder de :max caracteres.',
            'product.status.in' => 'El estado del producto seleccionado no es válido.',
            'product.brand_id.required_if' => 'La marca es obligatoria cuando el modo es nuevo.',
            'product.brand_id.prohibited_if' => 'No se debe enviar la marca cuando el modo es existente.',
            'product.brand_id.exists' => 'La marca seleccionada no existe.',
            'product.tax_id.required_if' => 'El impuesto es obligatorio cuando el modo es nuevo.',
            'product.tax_id.prohibited_if' => 'No se debe enviar el impuesto cuando el modo es existente.',
            'product.tax_id.exists' => 'El impuesto seleccionado no existe.',
            'product.unit_measure_id.required_if' => 'La unidad de medida es obligatoria cuando el modo es nuevo.',
            'product.unit_measure_id.prohibited_if' => 'No se debe enviar la unidad de medida cuando el modo es existente.',
            'product.unit_measure_id.exists' => 'La unidad de medida seleccionada no existe.',
            'details.required' => 'Los detalles de la compra son obligatorios.',
            'details.array' => 'Los detalles deben ser un arreglo.',
            'details.min' => 'Debe agregar al menos un detalle.',
            'details.*.color_id.exists' => 'El color seleccionado no existe.',
            'details.*.size_id.exists' => 'La talla seleccionada no existe.',
            'details.*.quantity.required' => 'La cantidad es obligatoria.',
            'details.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'details.*.quantity.min' => 'La cantidad debe ser al menos :min.',
            'details.*.unit_price.required' => 'El precio unitario es obligatorio.',
            'details.*.unit_price.numeric' => 'El precio unitario debe ser un valor numérico.',
            'details.*.unit_price.min' => 'El precio unitario no puede ser menor que :min.',
            'details.*.sale_price.numeric' => 'El precio de venta debe ser un valor numérico.',
            'details.*.sale_price.min' => 'El precio de venta no puede ser menor que :min.',
            'details.*.min_stock.integer' => 'El stock mínimo debe ser un número entero.',
            'details.*.min_stock.min' => 'El stock mínimo no puede ser menor que :min.',
            'details.*.sku.string' => 'El SKU debe ser una cadena de texto.',
            'details.*.sku.max' => 'El SKU no debe exceder de :max caracteres.',
            'details.*.code.string' => 'El código debe ser una cadena de texto.',
            'details.*.code.max' => 'El código no debe exceder de :max caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'reference' => 'referencia',
            'entity_id' => 'entidad',
            'warehouse_id' => 'almacén',
            'payment_method_id' => 'método de pago',
            'product_mode' => 'modo de producto',
            'product.id' => 'producto',
            'product.name' => 'nombre del producto',
            'product.description' => 'descripción del producto',
            'product.barcode' => 'código de barras',
            'product.code' => 'código',
            'product.sku' => 'SKU',
            'product.status' => 'estado del producto',
            'product.brand_id' => 'marca',
            'product.tax_id' => 'impuesto',
            'product.unit_measure_id' => 'unidad de medida',
            'details' => 'detalles',
            'details.*.color_id' => 'color',
            'details.*.size_id' => 'talla',
            'details.*.quantity' => 'cantidad',
            'details.*.unit_price' => 'precio unitario',
            'details.*.sale_price' => 'precio de venta',
            'details.*.min_stock' => 'stock mínimo',
            'details.*.sku' => 'SKU',
            'details.*.code' => 'código',
        ];
    }
}
