<?php

namespace Backpack\DevTools\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Str;

class MigrationRequest extends FormRequest
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
            'table' => 'required|min:2|max:255',
            'generate_model' => 'required|boolean',
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
            $table = request()->input('table') ?? '';
            $model = Str::of($table)->studly()->plural();

            $this->validateTableExists($validator, $table);

            $this->validateMigrationExists($validator, $table);

            $this->validateSingleIdOrBigIncrements($validator);

            $this->validateNoColumnsWithSameName($validator);

            $this->validateValueRequired($validator);

            if (request('generate_model')) {
                $this->validateRelationships($validator);
            }

            $this->validateMorphsColumns($validator);

            $this->validateForeignIdColumns($validator);

            $this->validateDefaultModifier($validator);
        });
    }
}
