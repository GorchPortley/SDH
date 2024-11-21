<?php

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
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
use Illuminate\Support\Facades\DB;
use Livewire\Volt\Component;
use function Laravel\Folio\{middleware, name};
use App\Models\Driver;

middleware('auth');
name('dashboard.drivers');

new class extends Component implements HasForms, Tables\Contracts\HasTable {
    use InteractsWithForms, InteractsWithTable;

    public ?array $data = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(Driver::query()->where('user_id', auth()->id()))
            ->heading('Drivers')
            ->description('Manage your Drivers here!')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->form([
                        Toggle::make('active'),
                        TextInput::make('brand')
                            ->datalist(DB::table('drivers')->distinct()->orderBy('brand', 'asc')->pluck('brand')),
                        TextInput::make('model'),
                        TextInput::make('tag'),
                        Select::make('category')
                            ->options(
                                ['Subwoofer'=>'Subwoofer','Woofer'=>'Woofer','Tweeter'=>'Tweeter','Compression Driver'=>'Compression Driver','Exciter'=>'Exciter','Other'=>'Other']),
                        TextInput::make('size')
                            ->numeric(),
                        TextInput::make('impedance')
                            ->numeric(),
                        TextInput::make('power')
                            ->numeric(),
                        TextInput::make('price')
                            ->numeric(),
                        TextInput::make('link')
                            ->url(),
                        TextInput::make('summary'),
                        MarkdownEditor::make('description'),
                        KeyValue::make('factory_specs')
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
                            ->valueLabel('Value'),
                    ])
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Driver created')
                            ->send();
                    })
            ])
            ->columns([
                ToggleColumn::make('active')
                    ->onColor('success')
                    ->sortable(),
                TextColumn::make('brand')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Driver $record): string => $record->tag),
                TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('size')
                    ->label('Size in inches')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('impedance')
                    ->label('Impedance')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created On')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make()
                    ->form([
                        Toggle::make('active')
                            ->onColor('success'),
                        TextInput::make('brand')
                            ->datalist(DB::table('drivers')->distinct()->orderBy('brand', 'asc')->pluck('brand')),
                        TextInput::make('model'),
                        TextInput::make('tag'),
                        Select::make('category')
                            ->options(
                                ['Subwoofer'=>'Subwoofer','Woofer'=>'Woofer','Tweeter'=>'Tweeter','Compression Driver'=>'Compression Driver','Exciter'=>'Exciter','Other'=>'Other']),
                        TextInput::make('size')
                            ->numeric(),
                        TextInput::make('impedance')
                            ->numeric(),
                        TextInput::make('power')
                            ->numeric(),
                        TextInput::make('price')
                            ->numeric(),
                        TextInput::make('link')
                            ->url(),
                        TextInput::make('summary'),
                        MarkdownEditor::make('description'),
                        KeyValue::make('factory_specs')
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->editableKeys(false)
                            ->keyLabel('Parameter')
                            ->valueLabel('Value'),
                    ]),
                DeleteAction::make()
                    ->after(function () {
                        Notification::make()
                            ->success()
                            ->title('Project deleted')
                            ->send();
                    }),
            ])
            ->filters([
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
    @volt('drivers')
    {{ $this->table }}
    @endvolt
</x-layouts.app>

