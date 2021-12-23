<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FieldValue;
use DB;
use Illuminate\Database\Query\JoinClause;

class ProductController extends Controller
{
    public function list(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $query = \DB::table('product_variants');
        $query
            ->select([
                'product_variants.id as id',
                'product_variants.code as code',
            ])
            ->leftJoin('products', 'product_variants.product_id', 'products.id');

        $numAlias = 1;
        $aliasFields = 'f' . $numAlias;
        $aliasCategory = 'c' . $numAlias;
        $aliasFieldValue = 'fv' . $numAlias;

        $query->leftJoin(
            "categories as $aliasCategory",
            "$aliasCategory.id",
            'products.category_id'
        );

        $query->leftJoin(
            "fields as $aliasFields",
            "$aliasFields.category_id",
            "$aliasCategory.id"
        );

        $query->leftJoin(
            "field_values as $aliasFieldValue",
            fn(JoinClause $q) => $q->on("$aliasFieldValue.field_id", "$aliasFields.id")
                ->on("$aliasFieldValue.product_variant_id", "product_variants.id")
        );

        $query->addSelect([
            "$aliasFields.code as $aliasFields" . '_code',
            "$aliasFieldValue.value as $aliasFieldValue" . '_value',
        ]);

        while ($category = $category->parent) {
            $numAlias++;
            $aliasFields = 'f' . $numAlias;
            $parentAliasCategory = 'c' . $numAlias;
            $aliasFieldValue = 'fv' . $numAlias;

            $query->leftJoin(
                "categories as $parentAliasCategory",
                "$parentAliasCategory.id",
                "$aliasCategory.parent_id"
            );

            $query->leftJoin(
                "fields as $aliasFields",
                "$aliasFields.category_id",
                "$parentAliasCategory.id"
            );

            $query->leftJoin(
                "field_values as $aliasFieldValue",
                fn(JoinClause $q) => $q->on("$aliasFieldValue.field_id", "$aliasFields.id")
                    ->on("$aliasFieldValue.product_variant_id", "product_variants.id")
            );

            $query->addSelect([
                "$aliasFields.code as $aliasFields" . '_code',
                "$aliasFieldValue.value as $aliasFieldValue" . '_value',
            ]);

            $aliasCategory = $parentAliasCategory;
        }

        return $query->get();
    }

    public function actionList(int $categoryId)
    {
        $filter = [
            'c1.f1' => '44',
            'c2.f2' => 'ffsd',
            'c3.f3' => 'asd',
        ];
        $query = DB::table('field_values')
            ->select(['field_values.product_variant_id',])
            ->distinct()
            ->join('fields', 'fields.id', 'field_values.field_id')
            ->join('categories', 'categories.id', 'fields.category_id')
            ->where('fields.category_id', $categoryId)
            ->groupBy('field_values.product_variant_id');

        foreach ($filter as $key => $value) {
            [$categoryCode, $fieldCode] = explode('.', $key);
            $query->orWhere(fn($q) => $q->where('categories.code', $categoryCode)
                ->where('fields.code', $fieldCode)
                ->where('field_values.value', $value)
            );
        }
        $query->havingRaw('COUNT(field_values.product_variant_id) >= ?', [count($filter)]);
        return $query->get();
        //попробовать с генерацией столбцов
    }

    public function actionList2($categoryId)
    {
        $filter = [
            'c1_f1' => '44',
            'c2_f2' => 'ffsd',
            'c3_f3' => 'aaa',
        ];
        $category = Category::findOrFail($categoryId);
        $query = \DB::table('product_variants');
        $query
            ->select([
                'product_variants.id as id',
                'product_variants.code as code',
            ])->distinct();

        do {
            $codeCategory = $category->code;
            $fields = $category->fields;

            foreach ($fields as $f) {
                $codeField = $f->code;
                $aliasFieldValue = $codeCategory . '_' . $codeField;
                $query->leftJoin(
                    "field_values as $aliasFieldValue",
                    fn(JoinClause $q) => $q->on("$aliasFieldValue.product_variant_id", "product_variants.id")
                        ->where("$aliasFieldValue.field_id", $f->id)
                );
                $column = $aliasFieldValue . '_' . 'field';
                $query->addSelect([
                    "$aliasFieldValue.value as $column",
                ]);
                if (isset($filter[$aliasFieldValue])) {
                    $query->where("$aliasFieldValue.value", $filter[$aliasFieldValue]);
                }
            }
        } while ($category = $category->parent);
        $paginator = $query->paginate(10);
        $productVariants = collect($paginator->items())->keyBy('id');

        //take multi fields
        $multiFieldValue = FieldValue::query()
            ->whereHas('field', function ($q) {
                $q->where('is_multi', true);
            })
            ->with('field.category')
            ->where('product_variant_id', $productVariants->pluck('id')->toArray())
            ->get()->groupBy('product_variant_id');
        $multiFieldValue->transform(fn($item) => $item->groupBy('field.category.code')
            ->transform(fn($item) => $item->groupBy('field.code'))
        );

        $multiFieldValue->each(
            fn($byProductVariant, $productVariantCode) => $byProductVariant
                ->each(fn($byCategory, $categoryCode) => $byCategory
                    ->each(static function ($byField, $fieldCode) use ($productVariantCode, $categoryCode, $productVariants) {
                        $pv = $productVariants->get($productVariantCode);
                        $pv->{$categoryCode . '_' . $fieldCode} = $byField->pluck('value');
                    })
                )
        );

        return $productVariants;
    }
}
