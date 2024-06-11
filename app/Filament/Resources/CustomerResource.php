<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\PipelineStage;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group as GroupInfolist;
use Filament\Infolists\Components\Section as SectionInfolist;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $slug = 'customers';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Customers';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Group::make()->schema([
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('phone_number')
                                ->label('Phone Number')
                                ->tel()
                                ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                ->mask('9999-9999-99999')
                                ->unique(ignoreRecord: true)
                                ->required(),
                        ])->columns(2),
                        Group::make()->schema([
                            Textarea::make('address')
                                ->required()
                        ])->columns(1),
                        Group::make()->schema([
                            Select::make('package_id')
                                ->label('Package')
                                ->relationship('package', 'name')
                                ->searchable()
                                ->required(),
                        ])->columns(1),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone_number'),
                TextColumn::make('package.name'),
                TextColumn::make('pipelineStage.name')
                    ->badge()
                    ->color(fn(Customer $record) => match ($record->pipelineStage->position) {
                        1 => 'gray',
                        2 => 'warning',
                        3 => 'primary',
                        4 => 'info',
                        5 => 'success',
                        6 => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordUrl(function ($record) {
                if ($record->trashed()) {
                    return null;
                }

                return Pages\ViewCustomer::getUrl([$record->id]);
            })
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('Move to Stage')
                        ->hidden(fn($record) => $record->trashed())
                        ->icon('heroicon-m-pencil-square')
                        ->form([
                            Select::make('pipeline_stage_id')
                                ->label('Status')
                                ->options(PipelineStage::pluck('name', 'id')->toArray())
                                ->default(function (Customer $record) {
                                    $currentPosition = $record->pipelineStage->position;
                                    return PipelineStage::where('position', '>', $currentPosition)->first()?->id;
                                }),
                        ])
                        ->action(function (Customer $customer, array $data): void {
                            $customer->pipeline_stage_id = $data['pipeline_stage_id'];
                            $customer->save();

                            Notification::make()
                                ->title('Customer Pipeline Updated')
                                ->success()
                                ->send();
                        }),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
            'view' => Pages\ViewCustomer::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('package', 'pipelineStage', 'seller', 'seller.user')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['package', 'pipelineStage']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->package) {
            $details['Package'] = $record->package->name;
        }

        if ($record->pipelineStage) {
            $details['PipelineStage'] = $record->pipelineStage->name;
        }

        return $details;
    }

    public static function infoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                SectionInfolist::make()
                    ->schema([
                        Split::make([
                            Grid::make(2)
                                ->schema([
                                    GroupInfolist::make()
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label('Name'),
                                            TextEntry::make('phone_number')
                                                ->label('Phone Number'),
                                            TextEntry::make('address')
                                        ]),
                                    GroupInfolist::make()
                                        ->schema([
                                            TextEntry::make('seller.user.name')
                                                ->label('Sales'),
                                            TextEntry::make('package.name')
                                                ->label('Package'),
                                            TextEntry::make('pipelineStage.name')
                                                ->badge()
                                        ]),
                                ]),
                        ])
                    ]),
            ]);
    }
}
