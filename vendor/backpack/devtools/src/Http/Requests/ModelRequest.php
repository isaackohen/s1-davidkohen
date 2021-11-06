<?php

namespace Backpack\DevTools\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Str;

class ModelRequest extends FormRequest
{
    use BaseRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge($this->commonRules(), [
            'name' => 'required|min:2|max:255',
        ]);
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $model = request()->input('name');
            $table = Str::of($model)->snake()->plural();

            $this->validateModelExists($validator, $model);

            if (request()->boolean('run_migration')) {
                $this->validateTableExists($validator, $table);
                $this->validateMigrationExists($validator, $table);
            }

            $this->validateSingleIdOrBigIncrements($validator);

            $this->validateNoColumnsWithSameName($validator);

            $this->validateValueRequired($validator);

            $this->validateMorphsColumns($validator);

            $this->validateRelationships($validator);

            $this->validateDefaultModifier($validator);
        });
    }
}
