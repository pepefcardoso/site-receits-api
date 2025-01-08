<?php

namespace App\Models;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    public mixed $user;

    protected $fillable = [
        'title',
        'description',
        'time',
        'portion',
        'difficulty',
        'image',
        'ingredients',
        'steps',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'steps' => 'array',
    ];

    public static function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'time' => 'required|integer',
            'portion' => 'required|integer',
            'difficulty' => ['required', Rule::in(RecipeDifficultyEnum::cases())],
            'image' => 'required|url',
            'ingredients' => 'required|array',
            'ingredients.*' => 'required|string',
            'steps' => 'required|array',
            'steps.*' => 'required|string',
            'category_id' => 'required|exists:recipe_categories,id',
            'diets' => 'array|required',
            'diets.*' => 'exists:recipe_diets,id',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diets(): BelongsToMany
    {
        return $this->belongsToMany(RecipeDiet::class, 'rl_recipe_diets', 'recipe_id', 'recipe_diet_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RecipeCategory::class);
    }
}
