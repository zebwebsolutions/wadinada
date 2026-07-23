<?php

namespace App\Http\Requests;

use App\Models\InventoryUnitIdentifier;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePurchaseBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchased_at' => ['required', 'date'],
            'payment_method' => ['nullable', Rule::in(['Cash', 'KNET', 'Bank Transfer', 'Link Payment', 'Other'])],
            'batch_notes' => ['nullable', 'string', 'max:3000'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_kuwait_id' => ['nullable', 'image', 'max:10240'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
            'items.*.name' => ['nullable', 'string', 'max:255'],
            'items.*.category' => ['nullable', 'string', 'max:80'],
            'items.*.brand' => ['nullable', 'string', 'max:120'],
            'items.*.color' => ['nullable', 'string', 'max:80'],
            'items.*.storage_capacity' => ['nullable', 'string', 'max:80'],
            'items.*.sku' => ['nullable', 'string', 'max:80'],
            'items.*.condition' => ['nullable', Rule::in(['New', 'Used', 'Open Box', 'Refurbished', 'Damaged'])],
            'items.*.tracking_method' => ['nullable', Rule::in(['imei', 'serial', 'barcode', 'internal'])],
            'items.*.sale_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:500'],
            'items.*.default_cost' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:2000'],
            'items.*.units' => ['nullable', 'array', 'max:500'],
            'items.*.units.*.identifier' => ['nullable', 'string', 'max:160'],
            'items.*.units.*.secondary_identifier' => ['nullable', 'string', 'max:160'],
            'items.*.units.*.cost_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $seenIdentifiers = [];
                $seenSkus = [];

                foreach ($this->input('items', []) as $itemIndex => $item) {
                    $product = filled($item['product_id'] ?? null)
                        ? Product::find($item['product_id'])
                        : null;

                    if (! $product) {
                        foreach (['name', 'category', 'condition', 'tracking_method'] as $field) {
                            if (blank($item[$field] ?? null)) {
                                $validator->errors()->add(
                                    "items.{$itemIndex}.{$field}",
                                    'Complete the new variant details or select an existing variant.'
                                );
                            }
                        }

                        $sku = trim((string) ($item['sku'] ?? ''));

                        if ($sku !== '') {
                            $normalizedSku = strtoupper($sku);

                            if (isset($seenSkus[$normalizedSku]) || Product::where('sku', $sku)->exists()) {
                                $validator->errors()->add("items.{$itemIndex}.sku", 'This SKU is already in use.');
                            }

                            $seenSkus[$normalizedSku] = true;
                        }
                    }

                    $trackingMethod = $product?->tracking_method ?? ($item['tracking_method'] ?? null);
                    $units = array_values($item['units'] ?? []);
                    $quantity = (int) ($item['quantity'] ?? 0);

                    if ($trackingMethod !== 'internal' && count($units) !== $quantity) {
                        $validator->errors()->add(
                            "items.{$itemIndex}.units",
                            "Scan exactly {$quantity} identifier(s); ".count($units).' provided.'
                        );
                    }

                    if ($trackingMethod === 'internal') {
                        continue;
                    }

                    foreach ($units as $unitIndex => $unit) {
                        $identifier = trim((string) ($unit['identifier'] ?? ''));

                        if ($identifier === '') {
                            $validator->errors()->add(
                                "items.{$itemIndex}.units.{$unitIndex}.identifier",
                                'Every unit needs an identifier.'
                            );

                            continue;
                        }

                        if ($trackingMethod === 'imei' && ! preg_match('/^\d{15}$/', preg_replace('/\D+/', '', $identifier))) {
                            $validator->errors()->add(
                                "items.{$itemIndex}.units.{$unitIndex}.identifier",
                                'An IMEI must contain exactly 15 digits.'
                            );
                        }

                        $this->validateIdentifier(
                            $validator,
                            $identifier,
                            $trackingMethod,
                            "items.{$itemIndex}.units.{$unitIndex}.identifier",
                            $seenIdentifiers
                        );

                        $secondary = trim((string) ($unit['secondary_identifier'] ?? ''));

                        if ($secondary !== '') {
                            $secondaryType = $trackingMethod === 'imei' ? 'imei2' : 'alternate';

                            if ($secondaryType === 'imei2' && ! preg_match('/^\d{15}$/', preg_replace('/\D+/', '', $secondary))) {
                                $validator->errors()->add(
                                    "items.{$itemIndex}.units.{$unitIndex}.secondary_identifier",
                                    'A second IMEI must contain exactly 15 digits.'
                                );
                            }

                            $this->validateIdentifier(
                                $validator,
                                $secondary,
                                $secondaryType,
                                "items.{$itemIndex}.units.{$unitIndex}.secondary_identifier",
                                $seenIdentifiers
                            );
                        }
                    }
                }
            },
        ];
    }

    private function validateIdentifier(
        Validator $validator,
        string $value,
        string $type,
        string $key,
        array &$seenIdentifiers
    ): void {
        $normalized = InventoryUnitIdentifier::normalize($value, $type);

        if ($normalized === '') {
            $validator->errors()->add($key, 'Enter a valid identifier.');

            return;
        }

        if (isset($seenIdentifiers[$normalized])) {
            $validator->errors()->add($key, 'This identifier appears more than once in this batch.');
        } elseif (InventoryUnitIdentifier::where('normalized_value', $normalized)->exists()) {
            $validator->errors()->add($key, 'This identifier already exists in inventory.');
        }

        $seenIdentifiers[$normalized] = true;
    }
}
