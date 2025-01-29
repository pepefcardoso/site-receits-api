<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\RecipeIngredient\CreateRecipeIngredient;
use App\Services\RecipeStep\CreateRecipeStep;
use Arr;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateRecipe
{
    protected CreateRecipeIngredient $createRecipeIngredient;
    protected CreateRecipeStep $createRecipeStep;

    public function __construct(
        CreateRecipeIngredient $createRecipeIngredient,
        CreateRecipeStep $createRecipeStep
    ) {
        $this->createRecipeIngredient = $createRecipeIngredient;
        $this->createRecipeStep = $createRecipeStep;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeData = array_merge(
                Arr::only($data, ['title', 'description', 'time', 'portion', 'difficulty', 'image', 'category_id']),
                ['user_id' => auth()->id()] // Assign the authenticated user
            );
            $recipe = Recipe::create($recipeData);

            $diets = data_get($data, 'diets');
            throw_if(empty($diets), Exception::class, 'Diets are required');
            $recipe->diets()->sync($diets);

            $ingredients = data_get($data, 'ingredients');
            throw_if(empty($ingredients), Exception::class, 'Ingredients are required');
            foreach ($ingredients as $ingredient) {
                $this->createRecipeIngredient->create([
                    'recipe_id' => $recipe->id,
                    'quantity' => $ingredient['quantity'],
                    'name' => $ingredient['name'],
                    'unit_id' => $ingredient['unit_id'],
                ]);
            }

            $steps = data_get($data, 'steps');
            throw_if(empty($steps), Exception::class, 'Steps are required');
            foreach ($steps as $step) {
                $this->createRecipeStep->create([
                    'recipe_id' => $recipe->id,
                    'order' => $step['order'],
                    'description' => $step['description'],
                ]);
            }

            DB::commit();
            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

}
