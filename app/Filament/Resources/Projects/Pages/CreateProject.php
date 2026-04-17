<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'planning';
        return $data;
    }

    protected function afterCreate(): void
    {
        $createdBy = $this->record->created_by;

        $alreadyMember = $this->record->members()->where('user_id', $createdBy)->exists();

        if (! $alreadyMember) {
            $this->record->members()->attach($createdBy, ['role' => 'manager']);
        }
    }
}
