<?php

namespace App\Filament\Resources\PipelineStageResource\Pages;

use App\Filament\Resources\PipelineStageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPipelineStages extends ListRecords
{
    protected static string $resource = PipelineStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
          CreateAction::make(),
        ];
    }
}
