<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Anggota Tim';

    public const SPECIALIZATIONS = [
        'frontend'    => 'Frontend Developer',
        'backend'     => 'Backend Developer',
        'ui_ux'       => 'UI/UX Designer',
        'qa'          => 'QA Engineer',
        'devops'      => 'DevOps Engineer',
        'mobile'      => 'Mobile Developer',
        'pentesting'  => 'Pentesting Web/Mobile',
    ];

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'manager'   => 'warning',
                        'developer' => 'success',
                        default     => 'gray',
                    }),
                TextColumn::make('pivot.specialization')
                    ->label('Spesialisasi')
                    ->formatStateUsing(fn ($state) => self::SPECIALIZATIONS[$state] ?? '-')
                    ->placeholder('-'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Member')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn ($query) => $query->where('role', 'developer')
                        ->orWhere('role', 'project_manager'))
                    ->form(fn (AttachAction $action) => [
                        $action->getRecordSelect()
                            ->label('User')
                            ->required(),
                        Select::make('role')
                            ->label('Role di Project')
                            ->options(['manager' => 'Project Manager', 'developer' => 'Developer'])
                            ->required()
                            ->live(),
                        Select::make('specialization')
                            ->label('Spesialisasi')
                            ->options(self::SPECIALIZATIONS)
                            ->visible(fn (Get $get) => $get('role') === 'developer')
                            ->required(fn (Get $get) => $get('role') === 'developer'),
                    ])
                    ->before(function (AttachAction $action, array $data) {
                        $project = $this->getOwnerRecord();

                        if ($data['role'] === 'manager') {
                            $count = $project->members()->wherePivot('role', 'manager')->count();
                            if ($count >= 1) {
                                Notification::make()
                                    ->title('Gagal menambah member')
                                    ->body('Project sudah memiliki 1 Project Manager. Maksimal hanya 1.')
                                    ->danger()
                                    ->send();
                                $action->halt();
                            }
                        }

                        if ($data['role'] === 'developer') {
                            $count = $project->members()->wherePivot('role', 'developer')->count();
                            if ($count >= 6) {
                                Notification::make()
                                    ->title('Gagal menambah member')
                                    ->body('Project sudah memiliki 6 Developer. Maksimal hanya 6.')
                                    ->danger()
                                    ->send();
                                $action->halt();
                            }
                        }
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        Select::make('role')
                            ->label('Role di Project')
                            ->options(['manager' => 'Project Manager', 'developer' => 'Developer'])
                            ->required()
                            ->live(),
                        Select::make('specialization')
                            ->label('Spesialisasi')
                            ->options(self::SPECIALIZATIONS)
                            ->visible(fn (Get $get) => $get('role') === 'developer')
                            ->required(fn (Get $get) => $get('role') === 'developer'),
                    ])
                    ->before(function (EditAction $action, array $data, $record) {
                        $project = $this->getOwnerRecord();

                        if ($data['role'] === 'manager') {
                            $count = $project->members()
                                ->wherePivot('role', 'manager')
                                ->where('users.id', '!=', $record->id)
                                ->count();
                            if ($count >= 1) {
                                Notification::make()
                                    ->title('Gagal mengubah role')
                                    ->body('Project sudah memiliki 1 Project Manager. Maksimal hanya 1.')
                                    ->danger()
                                    ->send();
                                $action->halt();
                            }
                        }

                        if ($data['role'] === 'developer') {
                            $count = $project->members()
                                ->wherePivot('role', 'developer')
                                ->where('users.id', '!=', $record->id)
                                ->count();
                            if ($count >= 6) {
                                Notification::make()
                                    ->title('Gagal mengubah role')
                                    ->body('Project sudah memiliki 6 Developer. Maksimal hanya 6.')
                                    ->danger()
                                    ->send();
                                $action->halt();
                            }
                        }
                    }),
                DetachAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                DetachBulkAction::make()->label('Hapus yang dipilih'),
            ]);
    }
}
