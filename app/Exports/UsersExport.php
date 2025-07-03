<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::with('roles')->get()->map(function ($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->password,
                $user->created_at,
                $user->updated_at,
                $user->roles->pluck('name')->implode(', '), // support multiple roles
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Password',
            'Created Date',
            'Updated Date',
            'Role',
        ];
    }
}
