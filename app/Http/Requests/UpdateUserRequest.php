<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone_number' => ['required', 'numeric',  'digits_between:4,15', 'not_regex:/-/', Rule::unique('users', 'phone_number')->ignore($this->user->id)],
        ];
    }

    /**
     * Get the custom validation messages for the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan, silakan gunakan email lain.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.numeric' => 'Nomor telepon harus berupa angka.',
            'phone_number.unique' => 'Nomor telepon sudah digunakan, silakan gunakan Nomor telepon lain.',
            'phone_number.digits_between' => 'Nomor telepon harus memiliki antara 4 hingga 15 digit.',
            'phone_number.not_regex' => 'Nomor telepon tidak boleh terdapat minus',
            // 'gender.required' => 'Jenis kelamin wajib diisi.',
            // 'gender.in' => 'Jenis kelamin harus salah satu dari: laki-laki, perempuan.',
            'instance_id.required' => 'Instansi wajib diisi.',
            'instance_id.exists' => 'Instansi yang dipilih tidak valid.',
            // 'photo.file' => 'Foto harus berupa file.',
            // 'photo.mimes' => 'Foto harus berformat: jpeg, png, jpg, pdf.',
            // 'photo.max' => 'Ukuran file foto maksimal 2MB.',
        ];
    }
}
