<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // registrasi terbuka untuk siapa saja
    }

    public function rules(): array
    {
        return [
            // Kolom users
            'username' => ['required', 'string', 'min:4', 'max:50', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Kolom buyer_profiles - hanya email di sini.
            // nama_lengkap, no_hp, alamat, tanggal_lahir dilengkapi belakangan
            // lewat UpdateProfileRequest setelah login.
            'email' => ['required', 'email', 'max:150', 'unique:buyer_profiles,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
