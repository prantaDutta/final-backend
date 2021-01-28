<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SSLCommerzRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request_data = json_decode($this->get('cart_json'), true);
        return [
            'cus_name' => 'required',
            'cus_email' => 'required',
            'cus_addr1' => 'required',
            'cus_phone' => 'required',
            'amount' => 'required'
        ];
    }
}
