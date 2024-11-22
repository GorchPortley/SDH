<?php

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\{Columns\ToggleColumn,
    Table,
    Concerns\InteractsWithTable,
    Contracts\HasTable,
    Actions\Action,
    Actions\CreateAction,
    Actions\DeleteAction,
    Actions\EditAction,
    Actions\ViewAction,
    Columns\TextColumn
};
use Livewire\Volt\Component;
use function Laravel\Folio\{middleware, name};
use App\Models\Design;
use App\Models\Driver;

middleware('auth');
name('dashboard.designs');

new class extends Component implements HasForms, Tables\Contracts\HasTable {
    use InteractsWithForms, InteractsWithTable;

    public ?array $data = [];

    public Driver $driver;

    public function table(Table $table): Table
    {
        return $table
            ->query(Design::query()->where('user_id', auth()->id()))
            ->heading('Designs')
            ->description('Manage your Designs here!')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->form([
                        Toggle::make('active')
                            ->onColor('success'),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        FileUpload::make('card_image')
                            ->disk('public')
                            ->directory('designthumbs')
                            ->visibility('public'),
                        TextInput::make('tag')
                            ->maxLength(255),
                        Select::make('category')
                            ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                                , 'Three-Way' => 'Three-Way', 'Four-Way+', 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
                        TextInput::make('price')
                            ->inputMode('decimal')
                            ->numeric(),
                        TextInput::make('build_cost')
                            ->inputMode('decimal')
                            ->numeric(),
                        TextInput::make('impedance')
                            ->numeric(),
                        TextInput::make('power')
                            ->numeric(),
                        Textarea::make('summary')
                            ->columns(2),
                        RichEditor::make('description')
                            ->columns(2),
                        KeyValue::make('bill_of_materials'),
                        Repeater::make('components')
                            ->collapsible()
                            ->relationship()
                            ->itemLabel(fn (array $state): ?string => $state['position'] ?? null)
                            ->schema([
                                Select::make('driver_id')
                                    ->searchable(['brand','model'])
                                    ->searchPrompt('Search by Brand or Model')
                                    ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                            return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                        }))
                                    ->preload()
                                    ->native(false)
                                    ->label('Driver'),
                                Select::make('position')
                                    ->options(['LF'=>'LF','LMF'=>'LMF','MF'=>'MF','HMF'=>'HMF','HF'=>'HF','Other'=>'Other']),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('low_frequency')
                                    ->numeric(),
                                TextInput::make('high_frequency')
                                    ->numeric(),
                                TextInput::make('air_volume')
                                    ->numeric(),
                                MarkdownEditor::make('description'),
                                KeyValue::make('specifications')
                                    ->default([
                                        'fs' => '',
                                        'qts' => '',
                                        'vas' => '',
                                        'xmax' => '',
                                        'le' => '',
                                        're' => '',
                                        'bl' => '',
                                        'sd' => '',
                                        'mms' => '',
                                        'cms' => '',
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->editableKeys(false)
                                    ->keyLabel('Parameter')
                                    ->valueLabel('Value')
                            ])
                    ])

            ])
            ->columns([
                ToggleColumn::make('active')
                    ->onColor('success'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tag')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                ViewAction::make()
                    ->form([
                        Toggle::make('active')
                            ->onColor('success'),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tag')
                            ->maxLength(255),
                        Select::make('category')
                            ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                                , 'Three-Way' => 'Three-Way', 'Four-Way+', 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
                        TextInput::make('price')
                            ->inputMode('decimal')
                            ->numeric(),
                        TextInput::make('build_cost')
                            ->inputMode('decimal')
                            ->numeric(),
                        TextInput::make('impedance')
                            ->numeric(),
                        TextInput::make('power')
                            ->numeric(),
                        Textarea::make('summary')
                            ->columns(2),
                        RichEditor::make('description')
                            ->columns(2),
                        KeyValue::make('bill_of_materials'),
                        Repeater::make('components')
                            ->collapsible()
                            ->relationship()
                            ->itemLabel(fn (array $state): ?string => $state['position'] ?? null)
                            ->schema([
                                Select::make('driver_id')
                                    ->searchable(['brand','model'])
                                    ->searchPrompt('Search by Brand or Model')
                                    ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                        return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                    }))
                                    ->preload()
                                    ->native(false)
                                    ->label('Driver'),
                                Select::make('position')
                                    ->options(['LF'=>'LF','LMF'=>'LMF','MF'=>'MF','HMF'=>'HMF','HF'=>'HF','Other'=>'Other']),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('low_frequency')
                                    ->numeric(),
                                TextInput::make('high_frequency')
                                    ->numeric(),
                                TextInput::make('air_volume')
                                    ->numeric(),
                                MarkdownEditor::make('description'),
                                KeyValue::make('specifications')
                                    ->default([
                                        'fs' => '',
                                        'qts' => '',
                                        'vas' => '',
                                        'xmax' => '',
                                        'le' => '',
                                        're' => '',
                                        'bl' => '',
                                        'sd' => '',
                                        'mms' => '',
                                        'cms' => '',
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->editableKeys(false)
                                    ->keyLabel('Parameter')
                                    ->valueLabel('Value')
                            ])
                    ]),
                EditAction::make()
                    ->form([
                        Toggle::make('active')
                            ->onColor('success'),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tag')
                            ->maxLength(255),
                        FileUpload::make('card_image')
                            ->disk('public')
                            ->directory('designthumbs')
                            ->visibility('private'),
                        Select::make('category')
                            ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                                , 'Three-Way' => 'Three-Way', 'Four-Way+', 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
                        TextInput::make('price')
                            ->inputMode('decimal')
                            ->numeric(),
                        TextInput::make('build_cost')
                            ->inputMode('decimal')
                            ->numeric(),
                        TextInput::make('impedance')
                            ->numeric(),
                        TextInput::make('power')
                            ->numeric(),
                        Textarea::make('summary')
                            ->columns(2),
                        RichEditor::make('description')
                            ->columns(2),
                        KeyValue::make('bill_of_materials'),
                        Repeater::make('components')
                            ->collapsible()
                            ->relationship()
                            ->itemLabel(fn (array $state): ?string => $state['position'] ?? null)
                            ->schema([
                                Select::make('driver_id')
                                    ->searchable(['brand','model'])
                                    ->searchPrompt('Search by Brand or Model')
                                    ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                        return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                    }))
                                    ->preload()
                                    ->native(false)
                                    ->label('Driver'),
                                Select::make('position')
                                    ->options(['LF'=>'LF','LMF'=>'LMF','MF'=>'MF','HMF'=>'HMF','HF'=>'HF','Other'=>'Other']),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('low_frequency')
                                    ->numeric(),
                                TextInput::make('high_frequency')
                                    ->numeric(),
                                TextInput::make('air_volume')
                                    ->numeric(),
                                MarkdownEditor::make('description'),
                                KeyValue::make('specifications')
                                    ->default([
                                        'fs' => '',
                                        'qts' => '',
                                        'vas' => '',
                                        'xmax' => '',
                                        'le' => '',
                                        're' => '',
                                        'bl' => '',
                                        'sd' => '',
                                        'mms' => '',
                                        'cms' => '',
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->editableKeys(false)
                                    ->keyLabel('Parameter')
                                    ->valueLabel('Value')
                            ])
                    ]),
                DeleteAction::make()
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Project deleted')
                            ->send();
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    })
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Design created')
                            ->send();
                    }),
            ])
            ->filters([
                // Add any filters you want here
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('active'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('tag')
                    ->maxLength(255),
                Select::make('category')
                    ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                        , 'Three-Way' => 'Three-Way', 'Four-Way+', 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
                TextInput::make('price')
                    ->numeric(),
                TextInput::make('build_cost')
                    ->numeric(),
                TextInput::make('impedance')
                    ->numeric(),
                TextInput::make('power')
                    ->numeric(),
                Textarea::make('summary')
                    ->columns(2),
                MarkdownEditor::make('description'),
                KeyValue::make('bill_of_materials'),
            ])
            ->statePath('data');
    }

}
?>


<x-layouts.app>
    @volt('dashboard.designs')
    <div>
    {{ $this->table }}
    </div>
    @endvolt
</x-layouts.app>
