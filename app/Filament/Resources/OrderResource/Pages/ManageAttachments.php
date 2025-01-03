<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\AttachementStatus;
use App\Filament\Resources\OrderResource;
use App\Models\Attachment;
use Filament\Actions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;

class ManageAttachments extends ManageRecords
{
    protected static string $resource = \Lunar\Admin\Filament\Resources\OrderResource::class;
    public Order $order;
    public OrderLine $orderLine;


    public function mount(): void
    {
        $this->order = Order::find(request('record'));
        $this->orderLine = OrderLine::find(request('line'));
    }

    protected ?string $heading  = 'Attachments';
    protected ?string $description = 'Manage the attachments for this order.';

    protected function getTableQuery(): ?Builder
    {
        return Attachment::query()
            ->where('order_line_id' ,$this->orderLine->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('create attachment for this order line')
                ->modalSubmitActionLabel('create attachment')
                ->form([
                    Hidden::make('order_line_id')
                        ->name('order_line_id')
                        ->default($this->orderLine->id),
                    TextInput::make('logo_heights')
                        ->columnSpan(1)
                        ->required()
                        ->default(50)
                        ->integer()
                        ->placeholder('tap logo height'),
                    ColorPicker::make('logo_color')
                        ->default('#f5f5f5')
                        ->columnSpan(1)
                        ->required(),
                    TextInput::make('printing_type')
                        ->label('printing type')
                        ->default('qsd')
                        ->columnSpan(1)
                        ->required(),
                    Textarea::make('notes')
                        ->label('notes')
                        ->default('dqsdsq')
                        ->columnSpan(3)
                        ->required(),
                    Select::make('status')
                        ->default(AttachementStatus::PENDING->value)
                        ->options([
                            AttachementStatus::PENDING->value => AttachementStatus::PENDING->name,
                            AttachementStatus::APPROVED->value => AttachementStatus::APPROVED->name,
                            AttachementStatus::REJECTED->value => AttachementStatus::REJECTED->name,
                        ]),
                    FileUpload::make('logo')
                        ->disk(Config::get('lunar.orders.attachments.disk', 'public'))
                        ->directory(Config::get('lunar.orders.attachments.directory', 'public'))
                        ->acceptedFileTypes(Config::get('lunar.orders.attachments.types', 'public'))
                ])
                ->model(Attachment::class)
                ->action(function (array $data) {
                    $model = Attachment::create($data);
                    $model->save();
                })
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->actions([
                DeleteAction::make()->requiresConfirmation(),
                Action::make('download')
                    ->openUrlInNewTab()
                    ->url(function(Attachment $record){
                        return Storage::exists('app/public/' . $record->logo )?
                            response()->streamDownload(function () use ($record){
                                echo Storage::disk('public')->readStream($record->logo);
                            })->getContent():
                            null;
                    })
            ])
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->prefix('#'),
                TextColumn::make('logo_heights')
                    ->label('Logo Height'),
                TextColumn::make('logo_color')
                    ->label('Logo Color')
                    ->badge(),
                TextColumn::make('printing_type')
                    ->label('Printing Entry'),
                SelectColumn::make('status')
                    ->default(fn($state) => $state)
                    ->options([
                        AttachementStatus::PENDING->value => AttachementStatus::PENDING->name,
                        AttachementStatus::APPROVED->value => AttachementStatus::APPROVED->name,
                        AttachementStatus::REJECTED->value => AttachementStatus::REJECTED->name,
                    ]),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->badge(),
            ]);
    }
}
