<?php

namespace App\Filament\Resources\SellerResource\Pages;

use App\Filament\Resources\SellerResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateSeller extends CreateRecord
{
    protected static string $resource = SellerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone_number'] = str_replace('-', '', $data['phone_number']);

        $user = User::create([
            'name' => $data['user.name'],
            'email' => $data['user.email'],
            'password' => Hash::make($data['phone_number'])
        ]);

        $user->assignRole('sales');

        return array_merge($data, ['user_id' => $user->id]);
    }
}
