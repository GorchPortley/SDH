<?php


use App\Models\Design;
use App\Models\Driver;
use App\Models\DesignDriver;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\{Columns\ToggleColumn,
    Table,
    Concerns\InteractsWithTable,
    Actions\CreateAction,
    Actions\DeleteAction,
    Actions\EditAction,
    Actions\ViewAction,
    Columns\TextColumn
};
use Livewire\Volt\Component;
use function Laravel\Folio\{middleware, name};

middleware('auth');
name('dashboard.designs');

new class extends Component implements HasForms, Tables\Contracts\HasTable {

    use InteractsWithForms, InteractsWithTable;

    public ?array $data = [];

    public Driver $driver;

    function getfrqpath($name, $position)
    {
        if (!$name || !$position) {
            return 'files/lost files/frequency files';
        }

        $userId = auth()->id();
        $designName = str($name);
        $position = str($position)->upper()->toString();

        return "files/{$userId}/{$designName}/{$position}/Frequency";
    }

    function getzpath($name, $position)
    {
        if (!$name || !$position) {
            return 'files/lost files/z files';
        }

        $userId = auth()->id();
        $designName = str($name);
        $position = str($position)->upper()->toString();

        return "files/{$userId}/{$designName}/{$position}/Impedance";
    }

    function getotherpath($name, $position)
    {
        if (!$name || !$position) {
            return 'files/lost files/other files';
        }

        $userId = auth()->id();
        $designName = str($name);
        $position = str($position)->upper()->toString();

        return "files/{$userId}/{$designName}/{$position}/Other";
    }

    function getenclosurepath($name)
    {
        if (!$name) {
            return 'files/lost files/enclosure files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Enclosure";
    }

    function getelectronicspath($name)
    {
        if (!$name) {
            return 'files/lost files/lost electronic files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Electronics";
    }

    function getdesignotherpath($name)
    {
        if (!$name) {
            return 'files/lost files/lost design other files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Other Files";
    }

    function getwidgetresponsepath($name)
    {
        if (!$name) {
            return 'files/lost files/lost display response files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Display Responses";
    }

    function getphotospath($name)
    {
        if (!$name) {
            return 'files/lost files/lost design photo files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Display Images";
    }

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
                            ->visibility('public')
                            ->default('demo/800x800.jpg'),
                        TextInput::make('tag')
                            ->maxLength(255),
                        Select::make('category')
                            ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                                , 'Three-Way' => 'Three-Way', 'Four-Way+' => 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
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
                        Placeholder::make('File Uploads')
                            ->content('You can upload files after creating and saving your design. Go to My Designs and edit to add files.'),
                        RichEditor::make('summary')
                            ->fileAttachmentsDirectory('attachments')
                            ->columns(2),
                        RichEditor::make('description')
                            ->fileAttachmentsDirectory('attachments')
                            ->columns(2),
                        KeyValue::make('bill_of_materials'),
                        Repeater::make('components')
                            ->collapsible()
                            ->relationship()
                            ->itemLabel(fn(array $state): ?string => $state['position'] ?? null)
                            ->schema([
                                Select::make('driver_id')
                                    ->searchable(['brand', 'model'])
                                    ->searchPrompt('Search by Brand or Model')
                                    ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                        return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                    }))
                                    ->preload()
                                    ->native(false)
                                    ->label('Driver'),
                                Select::make('position')
                                    ->options(['LF' => 'LF', 'LMF' => 'LMF', 'MF' => 'MF', 'HMF' => 'HMF', 'HF' => 'HF', 'Other' => 'Other']),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('low_frequency')
                                    ->numeric(),
                                TextInput::make('high_frequency')
                                    ->numeric(),
                                TextInput::make('air_volume')
                                    ->numeric(),
                                Placeholder::make('File Uploads')
                                    ->content('You can upload files after creating and saving your design. Go to My Designs and edit to add files.'),
                                RichEditor::make('description')
                                    ->fileAttachmentsDirectory('attachments'),
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
                TextColumn::make('sales_count')->counts('sales'),
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
                                , 'Three-Way' => 'Three-Way', 'Four-Way+' => 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
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
                        RichEditor::make('summary')
                            ->fileAttachmentsDirectory('attachments')
                            ->columns(2),
                        RichEditor::make('description')
                            ->fileAttachmentsDirectory('attachments')
                            ->columns(2),
                        KeyValue::make('bill_of_materials'),
                        Repeater::make('components')
                            ->collapsible()
                            ->relationship()
                            ->itemLabel(fn(array $state): ?string => $state['position'] ?? null)
                            ->schema([
                                Select::make('driver_id')
                                    ->searchable(['brand', 'model'])
                                    ->searchPrompt('Search by Brand or Model')
                                    ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                        return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                    }))
                                    ->preload()
                                    ->native(false)
                                    ->label('Driver'),
                                Select::make('position')
                                    ->options(['LF' => 'LF', 'LMF' => 'LMF', 'MF' => 'MF', 'HMF' => 'HMF', 'HF' => 'HF', 'Other' => 'Other']),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('low_frequency')
                                    ->numeric(),
                                TextInput::make('high_frequency')
                                    ->numeric(),
                                TextInput::make('air_volume')
                                    ->numeric(),
                                RichEditor::make('description')
                                    ->fileAttachmentsDirectory('attachments'),
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
                            ->live()
                            ->maxLength(255),
                        TextInput::make('tag')
                            ->maxLength(255),
                        FileUpload::make('card_image')
                            ->multiple()
                            ->disk('public')
                            ->directory(function ($get) {
                                $name = $get('name');
                                return $this->getphotospath($name);
                            })
                            ->visibility('private'),
                        Select::make('category')
                            ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                                , 'Three-Way' => 'Three-Way', 'Four-Way+' => 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
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
                        FileUpload::make('frd_files')
                            ->label('Response Previews')
                            ->multiple()
                            ->preserveFilenames()
                            ->directory(function ($get) {
                                $name = $get('name');
                                return $this->getwidgetresponsepath($name);
                            }),
                        FileUpload::make('enclosure_files')
                            ->label('Enclosure Files')
                            ->multiple()
                            ->preserveFilenames()
                            ->directory(function ($get) {
                                $name = $get('name');
                                return $this->getenclosurepath($name);
                            }),
                        FileUpload::make('electronic_files')
                            ->label('Electronics Files')
                            ->multiple()
                            ->preserveFilenames()
                            ->directory(function ($get) {
                                $name = $get('name');
                                return $this->getelectronicspath($name);
                            }),
                        FileUpload::make('design_other_files')
                            ->label('Other Design Files')
                            ->multiple()
                            ->preserveFilenames()
                            ->directory(function ($get) {
                                $name = $get('name');
                                return $this->getdesignotherpath($name);
                            }),
                        RichEditor::make('summary')
                            ->fileAttachmentsDirectory('attachments')
                            ->columns(2),
                        RichEditor::make('description')
                            ->fileAttachmentsDirectory('attachments')
                            ->columns(2),
                        KeyValue::make('bill_of_materials'),
                        Repeater::make('components')
                            ->collapsible()
                            ->relationship()
                            ->itemLabel(fn(array $state): ?string => $state['position'] ?? null)
                            ->schema([
                                Select::make('driver_id')
                                    ->searchable(['brand', 'model'])
                                    ->searchPrompt('Search by Brand or Model')
                                    ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                        return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                    }))
                                    ->preload()
                                    ->native(false)
                                    ->label('Driver'),
                                Select::make('position')
                                    ->live()
                                    ->options(['LF' => 'LF', 'LMF' => 'LMF', 'MF' => 'MF', 'HMF' => 'HMF', 'HF' => 'HF', 'Other' => 'Other']),
                                TextInput::make('quantity')
                                    ->numeric(),
                                TextInput::make('low_frequency')
                                    ->numeric(),
                                TextInput::make('high_frequency')
                                    ->numeric(),
                                TextInput::make('air_volume')
                                    ->numeric(),
                                FileUpload::make('Frequency_Files')
                                    ->label('Frequency Measurements')
                                    ->multiple()
                                    ->preserveFilenames()
                                    ->directory(function ($get) {
                                        $name = $get('../../name');
                                        $position = $get('position');
                                    return $this->getfrqpath($name, $position);
                                }),
                                FileUpload::make('Impedance_Files')
                                    ->label('Impedance Measurements')
                                    ->multiple()
                                    ->preserveFilenames()
                                    ->directory(function ($get) {
                                        $name = $get('../../name');
                                        $position = $get('position');
                                    return $this->getzpath($name, $position);
                                }),
                                FileUpload::make('Other_Files')
                                    ->label('Other Files')
                                    ->multiple()
                                    ->preserveFilenames()
                                    ->directory(function ($get) {
                                        $name = $get('../../name');
                                        $position = $get('position');
                                    return $this->getotherpath($name, $position);
                                }),
                                RichEditor::make('description')
                                    ->fileAttachmentsDirectory('attachments'),
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
                        , 'Three-Way' => 'Three-Way', 'Four-Way+' => 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),
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
                RichEditor::make('description'),
                KeyValue::make('bill_of_materials'),
            ])
            ->statePath('data');
    }

}
?>


<x-layouts.app>
    <x-app.container>
        @volt('dashboard.designs')
        <div>
            {{ $this->table }}
        </div>
        @endvolt
    </x-app.container>
</x-layouts.app>

