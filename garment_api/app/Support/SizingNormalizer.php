<?php

namespace App\Support;

/**
 * Canonical brand/category identity for the sizing model (Workstream 0).
 *
 * The SAME two functions must produce the keys used both at INFERENCE
 * (WidgetController -> /predict-size) and at TRAINING export
 * (MerchantController -> /rebuild-model). If they ever diverge, the sizing model's
 * one-hot brand/category columns won't fire and it will silently return
 * a generic, brand-blind size. Keep this the single source of truth.
 */
class SizingNormalizer
{
    /**
     * Canonical brand key: lowercase, strip everything non-alphanumeric.
     *   "Levi's" -> "levis", "H & M" -> "hm", "Jack & Jones" -> "jackjones"
     */
    public static function brandKey(?string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $name)));
    }

    /**
     * Canonical category slug. Matches the raw category text against a keyword
     * map using LONGEST keyword first (so "sweatshirt" beats "shirt" and
     * "tshirt" beats "shirt"), after stripping punctuation/spaces.
     *   "T Shirts" / "TShirts" / "Tees" -> "t-shirt"
     *   "Sweatshirts" -> "hoodie" (NOT formal-shirt)
     */
    public static function categorySlug(?string $category): string
    {
        $s = preg_replace('/[^a-z0-9]/', '', strtolower((string) $category));
        if ($s === '') {
            return 'other';
        }

        // slug => keywords (will be normalized + sorted longest-first below)
        $map = [
            't-shirt'      => ['tshirt', 'tshirts', 'tee', 'tees'],
            'formal-shirt' => ['formalshirt', 'formalshirts', 'shirt', 'shirts'],
            'jeans'        => ['jeans', 'denim', 'trouser', 'trousers', 'pant', 'pants', 'chino', 'chinos'],
            'hoodie'       => ['hoodie', 'hoodies', 'sweatshirt', 'sweatshirts', 'pullover', 'sweater', 'jumper'],
            'jacket'       => ['jacket', 'jackets', 'blazer', 'blazers', 'coat', 'coats'],
            'dress'        => ['dress', 'dresses', 'gown', 'gowns', 'frock'],
            'shorts'       => ['shorts', 'short'],
        ];

        // Flatten to [normalizedKeyword => slug], then sort by keyword length desc.
        $pairs = [];
        foreach ($map as $slug => $keywords) {
            foreach ($keywords as $kw) {
                $pairs[preg_replace('/[^a-z0-9]/', '', $kw)] = $slug;
            }
        }
        uksort($pairs, fn($a, $b) => strlen($b) <=> strlen($a));

        foreach ($pairs as $kw => $slug) {
            if ($kw !== '' && str_contains($s, $kw)) {
                return $slug;
            }
        }

        return 'other';
    }
}
