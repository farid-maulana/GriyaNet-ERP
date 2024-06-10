<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellerResource\Pages;
use App\Models\Branch;
use App\Models\Seller;
use Filament\Forms\Components\DatePicker;
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
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellerResource extends Resource
{
    protected static ?string $model = Seller::class;
    protected static ?string $slug = 'sellers';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Sales';
    protected static ?string $navigationGroup = 'HR Data';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Sales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextInput::make('user.name')
                                    ->label('Sales Name')
                                    ->required(),
                                Select::make('gender')
                                    ->options([
                                        'M' => 'Male',
                                        'F' => 'Female'
                                    ])
                                    ->required()
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('user.email')
                                    ->email()
                                    ->required(),
//                                    ->unique(User::class, 'email', ignoreRecord: true),
                                TextInput::make('phone_number')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                                    ->mask('9999-9999-99999')
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                Textarea::make('address')
                                    ->required()
                            ])->columns(1),
                        Group::make()->schema([
                            DatePicker::make('birthday')
                                ->required(),
                            DatePicker::make('hire_date')
                                ->label('Hire Date')
                                ->required(),
                        ])->columns(2),
                        Group::make()->schema([
                            Select::make('branch_id')
                                ->label('Branch')
                                ->options(
                                    Branch::pluck('name', 'id')->toArray()
                                )
                                ->searchable()
                                ->required(),
                            Select::make('status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive'
                                ])
                                ->required(),
                        ])->columns(2),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email'),
                TextColumn::make('phone_number')
                    ->label('Phone Number'),
                TextColumn::make('gender')
                    ->getStateUsing(function ($record): string {
                        return $record->gender == 'M' ? 'Male' : 'Female';
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(function ($record): string {
                        return ucwords($record->status);
                    })
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    }),
            ])
            ->defaultSort('status')
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive'
                ]),
                SelectFilter::make('gender')->options([
                    'M' => 'Male',
                    'F' => 'Female'
                ]),
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
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
            'index' => Pages\ListSellers::route('/'),
            'create' => Pages\CreateSeller::route('/create'),
            'edit' => Pages\EditSeller::route('/{record}/edit'),
            'view' => Pages\ViewSeller::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('user')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        return $details;
    }

    public static function infolist(Infolist $infolist): Infolist
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
                                            TextEntry::make('user.name')
                                                ->label('Name'),
                                            TextEntry::make('user.email')
                                                ->label('Email'),
                                            TextEntry::make('phone_number')
                                                ->label('Phone Number'),
                                            TextEntry::make('address')
                                        ]),
                                    GroupInfolist::make()
                                        ->schema([
                                            TextEntry::make('gender')
                                                ->getStateUsing(function ($record): string {
                                                    return $record->gender == 'M' ? 'Male' : 'Female';
                                                }),
                                            TextEntry::make('birthday'),
                                            TextEntry::make('hire_date')
                                                ->label('Hire Date'),
                                            TextEntry::make('status')
                                                ->badge()
                                                ->getStateUsing(function ($record): string {
                                                    return ucwords($record->status);
                                                })
                                                ->color(fn(string $state): string => match (strtolower($state)) {
                                                    'active' => 'success',
                                                    'inactive' => 'danger',
                                                }),
                                        ]),
                                ]),
                        ])
                    ]),
            ]);
    }
}
