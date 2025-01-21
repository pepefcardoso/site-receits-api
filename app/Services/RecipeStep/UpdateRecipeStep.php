<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;
use Illuminate\Support\Facades\DB;

class UpdateRecipeStep
{
    public function update(RecipeStep $recipeStep, array $data): RecipeStep|string
    {
        try {
            DB::beginTransaction();

            $recipeStep->fill($data);
            $recipeStep->save();

            DB::commit();

            return $recipeStep;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
