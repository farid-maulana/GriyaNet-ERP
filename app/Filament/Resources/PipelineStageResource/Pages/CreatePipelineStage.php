<?php

namespace App\Filament\Resources\PipelineStageResource\Pages;

use App\Filament\Resources\PipelineStageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePipelineStage extends CreateRecord
{
    protected static string $resource = PipelineStageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
