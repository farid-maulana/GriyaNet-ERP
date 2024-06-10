<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use Filament\Resources\Pages\EditRecord;

class EditPackage extends EditRecord
{
    protected static string $resource = PackageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['price'] =  $data['price'] * 100;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['price'] =  $data['price'] / 100;

        return $data;
    }
}
