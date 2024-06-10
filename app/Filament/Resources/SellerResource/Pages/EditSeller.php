<?php

namespace App\Filament\Resources\SellerResource\Pages;

use App\Filament\Resources\SellerResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditSeller extends EditRecord
{
    protected static string $resource = SellerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['phone_number'] = str_replace('-', '', $data['phone_number']);
        $user = User::find($data['user_id']);
        $user->password = Hash::make($data['phone_number']);
        $user->save();

        return $data;
    }
}
