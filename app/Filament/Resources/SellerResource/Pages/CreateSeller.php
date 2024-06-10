<?php

namespace App\Filament\Resources\SellerResource\Pages;

use App\Filament\Resources\SellerResource;
use App\Models\Seller;
use App\Models\User;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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

        return $data;
    }

    /**
     * @throws Exception
     */
    protected function handleRecordCreation(array $data): Model
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['phone_number']),
            ]);

            $user->assignRole('sales');

            $seller = Seller::create([
                'user_id' => $user->id,
                'phone_number' => $data['phone_number'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'birthday' => $data['birthday'],
                'hire_date' => $data['hire_date'],
                'status' => $data['status'],
                'branch_id' => $data['branch_id']
            ]);

            DB::commit();
            return $seller;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
