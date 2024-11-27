<?php


use App\Models\Design;
use App\Models\Driver;
use App\Models\DesignDriver;
use Dotswan\FilamentGrapesjs\Fields\GrapesJs;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
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

    public $speakerTaglines = [
        "Experience Sound in Its Purest Form",
        "Where Music Comes to Life",
        "Precision Audio Engineering",
        "Unleash the Power of Perfect Sound",
        "Crafted for Audio Excellence",
        "Sound that Moves You",
        "Beyond Ordinary Listening",
        "The Art of Audio Perfection",
        "Sonic Innovation Redefined",
        "Elevate Your Audio Experience",
        "Performance Meets Precision",
        "Sound Without Compromise",
        "Engineered for Excellence",
        "The Future of Sound is Here",
        "Audio Mastery Unleashed",
        "Where Technology Meets Harmony",
        "Experience Sound in 360°",
        "Pure. Powerful. Precise.",
        "Revolutionary Sound Design",
        "Setting New Audio Standards",
        "Immersive Sound Experience",
        "The Evolution of Audio",
        "Sound Engineering Excellence",
        "Redefining Audio Clarity",
        "Premium Sound Engineering"
    ];

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

        return "files/{$userId}/{$designName}/Attachments/Display Responses";
    }

    function getphotospath($name)
    {
        if (!$name) {
            return 'files/lost files/lost design photo files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Attachments/Display Images";
    }

    function getsummaryattachmentspath($name)
    {
        if (!$name) {
            return 'files/lost files/lost design photo files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Attachments/Summary Images";
    }

    function getdescriptionattachmentspath($name)
    {
        if (!$name) {
            return 'files/lost files/lost design photo files';
        }

        $userId = auth()->id();
        $designName = str($name);

        return "files/{$userId}/{$designName}/Attachments/Description Images";
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
                            ->label('Published')
                            ->onColor('success'),

                        Section::make('Design Information')
                            ->description('Basic information about the design')
                            ->collapsible()
                            ->schema([
                        TextInput::make('name')
                            ->hint('The name of your design')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('card_image')
                            ->label('Design Images')
                            ->hint('Images that will be displayed publicly')
                            ->disk('public')
                            ->directory('designthumbs')
                            ->visibility('public')
                            ->default('demo/800x800.jpg'),


                                FileUpload::make('frd_files')
                                    ->label('Response Previews')
                                    ->hint('These are representative of your designs. Upload multiple component responses or a single response')
                                    ->multiple()
                                    ->preserveFilenames()
                                    ->directory(function ($get) {
                                        $name = $get('name');
                                        return $this->getwidgetresponsepath($name);
                                    }),

                        TextInput::make('tag')
                            ->label('Tagline')
                            ->hint('A short descriptor for your design')
                            ->maxLength(255)
                            ->placeholder($this->speakerTaglines[array_rand($this->speakerTaglines)]),

                        Select::make('category')
                            ->hint('What type of speaker is your design?')
                            ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                                , 'Three-Way' => 'Three-Way', 'Four-Way+' => 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),

                        TextInput::make('price')
                            ->hint('The selling price of your design')
                            ->default('0.00')
                            ->inputMode('decimal')
                            ->numeric(),

                        TextInput::make('build_cost')
                            ->hint('The cost to build a single speaker')
                            ->inputMode('decimal')
                            ->numeric(),

                        TextInput::make('impedance')
                            ->hint('The nominal impedance of the design')
                            ->numeric(),

                        TextInput::make('power')
                            ->hint('The power rating of the design')
                            ->numeric(),

                        RichEditor::make('summary')
                            ->hint('The selling point of your design')
                            ->fileAttachmentsDirectory('attachments')
                            ->columns(2),

                        Section::make('Private Information')
                            ->collapsible()
                            ->schema([
                                RichEditor::make('description')
                                    ->hint('The main information area of your design, describe the design in greater detail')
                                    ->fileAttachmentsDirectory('attachments')
                                    ->columns(2),
                                KeyValue::make('bill_of_materials')
                                ->hint('The BOM of your design'),
                                Section::make('Design File Uploads')
                                    ->description('Upload your design files here')
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        FileUpload::make('enclosure_files')
                                            ->hint('These are files related to the enclosure design')
                                            ->helperText('Drawings, CAD files, Blueprints, etc...')
                                            ->label('Enclosure Files')
                                            ->multiple()
                                            ->preserveFilenames()
                                            ->directory(function ($get) {
                                                $name = $get('name');
                                                return $this->getenclosurepath($name);
                                            }),

                                        FileUpload::make('electronic_files')
                                            ->hint('These are files related to the electronics')
                                            ->helperText('Crossover Schematics, PCB Designs, etc...')
                                            ->label('Electronics Files')
                                            ->multiple()
                                            ->preserveFilenames()
                                            ->directory(function ($get) {
                                                $name = $get('name');
                                                return $this->getelectronicspath($name);
                                            }),

                                        FileUpload::make('design_other_files')
                                            ->hint('These are other files related to the design')
                                            ->helperText('Images, Recordings, Writeups, etc...')
                                            ->label('Other Design Files')
                                            ->multiple()
                                            ->preserveFilenames()
                                            ->directory(function ($get) {
                                                $name = $get('name');
                                                return $this->getdesignotherpath($name);
                                            }),
                            ]),]),]),
                        Section::make('Design Drivers')
                            ->collapsed()
                            ->description('Attach and upload data for the drivers in your design')
                ->schema([
                        Repeater::make('components')
                            ->label('')
                            ->addActionLabel('Add Driver To Design')
                            ->collapsed()
                            ->defaultItems(0)
                            ->collapsible()
                            ->relationship()
                            ->itemLabel(fn(array $state): ?string => $state['position'] ?? 'New Component')
                            ->schema([

                                Select::make('driver_id')
                                    ->hint('attach a driver to this band. If the driver you used is not available please go create an entry in My Drivers')
                                    ->searchable(['brand', 'model'])
                                    ->searchPrompt('Search by Brand or Model')
                                    ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                        return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                    }))
                                    ->preload()
                                    ->nullable(false)
                                    ->live()
                                    ->native(false)
                                    ->label('Driver'),

                                Select::make('position')
                                    ->hint('Which frequency band this driver occupies in the design')
                                    ->live()
                                    ->options(['LF' => 'LF', 'LMF' => 'LMF', 'MF' => 'MF', 'HMF' => 'HMF', 'HF' => 'HF', 'Other' => 'Other']),

                                TextInput::make('quantity')
                                    ->hint('The number of drivers in this band')
                                    ->numeric(),

                                Section::make('Frequency Range')
                                    ->description('The bandwidth the driver occupies')
                                    ->columns(2)
                                    ->schema([
                                TextInput::make('low_frequency')
                                    ->columns(1)
                                    ->numeric(),

                                TextInput::make('high_frequency')
                                    ->columns(1)
                                    ->numeric(),
                                ]),

                                TextInput::make('air_volume')
                                    ->hint('The enclosed air volume of the driver')
                                    ->default(0)
                                    ->numeric(),

                                Section::make('Driver File Uploads')
                                    ->description('Upload the working files used to design your speaker')
                                    ->collapsed()
                                    ->collapsible()
                                    ->schema([
                                        FileUpload::make('frequency_files')
                                        ->label('Frequency Measurements')
                                        ->multiple()
                                        ->preserveFilenames()
                                        ->directory(function ($get) {
                                            $name = $get('../../name');
                                            $position = $get('position');
                                            return $this->getfrqpath($name, $position);
                                        }),

                                        FileUpload::make('impedance_files')
                                            ->label('Impedance Measurements')
                                            ->multiple()
                                            ->preserveFilenames()
                                            ->directory(function ($get) {
                                                $name = $get('../../name');
                                                $position = $get('position');
                                                return $this->getzpath($name, $position);
                                            }),

                                        FileUpload::make('other_files')
                                            ->label('Other Files')
                                            ->multiple()
                                            ->preserveFilenames()
                                            ->directory(function ($get) {
                                                $name = $get('../../name');
                                                $position = $get('position');
                                                return $this->getotherpath($name, $position);
                                            }),]),
                                RichEditor::make('description')
                                    ->hint('This area allows you to describe the design driver in more detail.')
                                    ->fileAttachmentsDirectory('attachments'),

                                KeyValue::make('specifications')
                                    ->hint('These measurements are taken by the designer during testing, not copied from factory specs.')
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

                    ])])

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
            EditAction::make()
                ->form([
                    Toggle::make('active')
                        ->label('Published')
                        ->onColor('success'),

                    Section::make('Design Information')
                        ->description('Basic information about the design')
                        ->collapsible()
                        ->schema([
                            TextInput::make('name')
                                ->hint('The name of your design')
                                ->required()
                                ->maxLength(255),

                            FileUpload::make('card_image')
                                ->label('Design Images')
                                ->hint('Images that will be displayed publicly')
                                ->disk('public')
                                ->directory('designthumbs')
                                ->visibility('public')
                                ->default('demo/800x800.jpg'),


                            FileUpload::make('frd_files')
                                ->label('Response Previews')
                                ->hint('These are representative of your designs. Upload multiple component responses or a single response')
                                ->multiple()
                                ->preserveFilenames()
                                ->directory(function ($get) {
                                    $name = $get('name');
                                    return $this->getwidgetresponsepath($name);
                                }),

                            TextInput::make('tag')
                                ->label('Tagline')
                                ->hint('A short descriptor for your design')
                                ->maxLength(255)
                                ->placeholder($this->speakerTaglines[array_rand($this->speakerTaglines)]),

                            Select::make('category')
                                ->hint('What type of speaker is your design?')
                                ->options(['Subwoofer' => 'Subwoofer', 'Full-Range' => 'Full-Range', 'Two-Way' => 'Two-Way'
                                    , 'Three-Way' => 'Three-Way', 'Four-Way+' => 'Four-Way+', 'Portable' => 'Portable', 'Esoteric' => 'Esoteric']),

                            TextInput::make('price')
                                ->hint('The selling price of your design')
                                ->default('0.00')
                                ->inputMode('decimal')
                                ->numeric(),

                            TextInput::make('build_cost')
                                ->hint('The cost to build a single speaker')
                                ->inputMode('decimal')
                                ->numeric(),

                            TextInput::make('impedance')
                                ->hint('The nominal impedance of the design')
                                ->numeric(),

                            TextInput::make('power')
                                ->hint('The power rating of the design')
                                ->numeric(),

                            RichEditor::make('summary')
                                ->hint('The selling point of your design')
                                ->fileAttachmentsDirectory('attachments')
                                ->columns(2),

                            Section::make('Private Information')
                                ->collapsible()
                                ->schema([
                                    RichEditor::make('description')
                                        ->hint('The main information area of your design, describe the design in greater detail')
                                        ->fileAttachmentsDirectory('attachments')
                                        ->columns(2),
                                    KeyValue::make('bill_of_materials')
                                        ->hint('The BOM of your design'),
                                    Section::make('Design File Uploads')
                                        ->description('Upload your design files here')
                                        ->collapsible()
                                        ->collapsed()
                                        ->schema([
                                            FileUpload::make('enclosure_files')
                                                ->hint('These are files related to the enclosure design')
                                                ->helperText('Drawings, CAD files, Blueprints, etc...')
                                                ->label('Enclosure Files')
                                                ->multiple()
                                                ->preserveFilenames()
                                                ->directory(function ($get) {
                                                    $name = $get('name');
                                                    return $this->getenclosurepath($name);
                                                }),

                                            FileUpload::make('electronic_files')
                                                ->hint('These are files related to the electronics')
                                                ->helperText('Crossover Schematics, PCB Designs, etc...')
                                                ->label('Electronics Files')
                                                ->multiple()
                                                ->preserveFilenames()
                                                ->directory(function ($get) {
                                                    $name = $get('name');
                                                    return $this->getelectronicspath($name);
                                                }),

                                            FileUpload::make('design_other_files')
                                                ->hint('These are other files related to the design')
                                                ->helperText('Images, Recordings, Writeups, etc...')
                                                ->label('Other Design Files')
                                                ->multiple()
                                                ->preserveFilenames()
                                                ->directory(function ($get) {
                                                    $name = $get('name');
                                                    return $this->getdesignotherpath($name);
                                                }),
                                        ]),]),]),
                    Section::make('Design Drivers')
                        ->collapsed()
                        ->description('Attach and upload data for the drivers in your design')
                        ->schema([
                            Repeater::make('components')
                                ->label('')
                                ->addActionLabel('Add Driver To Design')
                                ->collapsed()
                                ->defaultItems(0)
                                ->collapsible()
                                ->relationship()
                                ->itemLabel(fn(array $state): ?string => $state['position'] ?? 'New Component')
                                ->schema([

                                    Select::make('driver_id')
                                        ->hint('attach a driver to this band. If the driver you used is not available please go create an entry in My Drivers')
                                        ->searchable(['brand', 'model'])
                                        ->searchPrompt('Search by Brand or Model')
                                        ->options(Driver::where('active', 1)->select('id', 'brand', 'model', 'size', 'category')->get()->mapWithKeys(function ($driver) {
                                            return [$driver->id => $driver->brand . ' ' . $driver->model . ': ' . $driver->size . ' inch ' . $driver->category];
                                        }))
                                        ->preload()
                                        ->nullable(false)
                                        ->live()
                                        ->native(false)
                                        ->label('Driver'),

                                    Select::make('position')
                                        ->hint('Which frequency band this driver occupies in the design')
                                        ->live()
                                        ->options(['LF' => 'LF', 'LMF' => 'LMF', 'MF' => 'MF', 'HMF' => 'HMF', 'HF' => 'HF', 'Other' => 'Other']),

                                    TextInput::make('quantity')
                                        ->hint('The number of drivers in this band')
                                        ->numeric(),

                                    Section::make('Frequency Range')
                                        ->description('The bandwidth the driver occupies')
                                        ->columns(2)
                                        ->schema([
                                            TextInput::make('low_frequency')
                                                ->columns(1)
                                                ->numeric(),

                                            TextInput::make('high_frequency')
                                                ->columns(1)
                                                ->numeric(),
                                        ]),

                                    TextInput::make('air_volume')
                                        ->hint('The enclosed air volume of the driver')
                                        ->default(0)
                                        ->numeric(),

                                    Section::make('Driver File Uploads')
                                        ->description('Upload the working files used to design your speaker')
                                        ->collapsed()
                                        ->collapsible()
                                        ->schema([
                                            FileUpload::make('frequency_files')
                                                ->label('Frequency Measurements')
                                                ->multiple()
                                                ->preserveFilenames()
                                                ->directory(function ($get) {
                                                    $name = $get('../../name');
                                                    $position = $get('position');
                                                    return $this->getfrqpath($name, $position);
                                                }),

                                            FileUpload::make('impedance_files')
                                                ->label('Impedance Measurements')
                                                ->multiple()
                                                ->preserveFilenames()
                                                ->directory(function ($get) {
                                                    $name = $get('../../name');
                                                    $position = $get('position');
                                                    return $this->getzpath($name, $position);
                                                }),

                                            FileUpload::make('other_files')
                                                ->label('Other Files')
                                                ->multiple()
                                                ->preserveFilenames()
                                                ->directory(function ($get) {
                                                    $name = $get('../../name');
                                                    $position = $get('position');
                                                    return $this->getotherpath($name, $position);
                                                }),]),
                                    RichEditor::make('description')
                                        ->hint('This area allows you to describe the design driver in more detail.')
                                        ->fileAttachmentsDirectory('attachments'),

                                    KeyValue::make('specifications')
                                        ->hint('These measurements are taken by the designer during testing, not copied from factory specs.')
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

                        ])]),
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

