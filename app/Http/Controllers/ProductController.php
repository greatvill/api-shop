<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
}
