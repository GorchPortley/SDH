<?php

use App\Models\Design;
use App\Models\DesignPurchase;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Volt\Component;
use function Laravel\Folio\{middleware, name};

middleware('auth');
name('dashboard.library');

new class extends Component implements HasForms, Tables\Contracts\HasTable {
    use InteractsWithForms, Tables\Concerns\InteractsWithTable;

    public ?array $data = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(DesignPurchase::query()
                ->where('user_id', auth()->id())
                ->with('design'))
            ->heading('Design Library')
            ->description('Designs you have purchased or collected')
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn (DesignPurchase $record): string => route('design', ['id' => $record->design->id]))
            ->columns([
                TextColumn::make('design.name')
                    ->description(fn(DesignPurchase $record): string=> $record->design->tag),
                TextColumn::make('design.category')
                    ->label('Category'),
                TextColumn::make('design.designer.name')
                    ->label('Designer'),
            ]);
    }
}
?>

<x-layouts.app>
    @volt('library')
    <x-app.container>

    {{ $this->table }}

    </x-app.container>
    @endvolt
</x-layouts.app>
