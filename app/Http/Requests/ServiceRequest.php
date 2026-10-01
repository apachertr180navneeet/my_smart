<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('service_preferences')) {
            $prefs = $this->service_preferences;
            if (is_string($prefs)) {
                $decoded = json_decode($prefs, true);
                if (is_array($decoded)) {
                    $prefs = $decoded;
                }
            }
            if (is_array($prefs) && !empty($prefs)) {
                $minPrice = collect($prefs)->min('price');
                if ($minPrice !== null && !$this->has('price')) {
                    $this->merge(['price' => $minPrice]);
                }
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->id;
        $rules = [
            'name'                           => 'required|unique:services,name,'.$id,
            'category_id'                    => 'required',
            'type'                           => 'required',
            'price'                          => 'required|min:0',
            'status'                         => 'required',
        ];

        if ($this->is('api/*') || $this->has('service_preferences')) {
            $rules['service_preferences'] = ['required', function ($attribute, $value, $fail) {
                $decoded = is_string($value) ? json_decode($value, true) : $value;
                if (!is_array($decoded)) {
                    return $fail('The service preferences must be a valid JSON array.');
                }
                if (count($decoded) < 1 || count($decoded) > 3) {
                    return $fail('The service preferences must contain between 1 and 3 preferences.');
                }
                $allowedTypes = ['provider_location', 'customer_location', 'virtual'];
                $seenTypes = [];
                foreach ($decoded as $item) {
                    if (!is_array($item) || !isset($item['type']) || !isset($item['price'])) {
                        return $fail('Each service preference must contain a valid type and price.');
                    }
                    if (!in_array($item['type'], $allowedTypes)) {
                        return $fail('Invalid service preference type: ' . $item['type']);
                    }
                    if (!is_numeric($item['price']) || (float)$item['price'] < 0) {
                        return $fail('Preference price must be a numeric value greater than or equal to 0.');
                    }
                    if (in_array($item['type'], $seenTypes)) {
                        return $fail('Duplicate service preference type: ' . $item['type']);
                    }
                    $seenTypes[] = $item['type'];
                }
            }];
        }

        // Only apply SEO validation if SEO is enabled
        if (request()->has('seo_enabled') && request()->seo_enabled) {
            $rules['meta_title'] = 'required|string|max:255|unique:services,meta_title,'.$id;
            $rules['meta_description'] = 'required|string|max:200';
            $rules['meta_keywords'] = 'required|string';
        }

        return $rules;
    }
    public function messages()
    {
        return [];
    }

    protected function failedValidation(Validator $validator)
    {
        if ( request()->is('api*')){
            $data = [
                'status' => 'false',
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];

            throw new HttpResponseException(response()->json($data,422));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}
