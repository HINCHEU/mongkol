<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

// The rows of the settings table that admin edits; read across the app with Setting::read().
class Settings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static UnitEnum|string|null $navigationGroup = 'Administration · ការគ្រប់គ្រង';

    protected static ?string $navigationLabel = 'Settings · ការកំណត់';

    protected static ?int $navigationSort = 22;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $khqr = Setting::read('payment_khqr', []);

        $this->form->fill([
            'trial_days' => Setting::read('trial_days', 2),
            'trial_guest_cap' => Setting::read('trial_guest_cap', 10),
            'payment_instructions' => Setting::read('payment_instructions'),
            // Stored as paths on the media disk; older full URLs (set by hand) can't be shown in the uploader.
            'khqr_usd' => self::isPath($khqr['USD'] ?? null) ? $khqr['USD'] : null,
            'khqr_khr' => self::isPath($khqr['KHR'] ?? null) ? $khqr['KHR'] : null,
            'telegram_chat_id' => Setting::read('telegram_chat_id'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $qr = fn (string $name, string $label) => FileUpload::make($name)->label($label)
            ->image()->disk(config('filesystems.media_disk'))->directory('payment-khqr')->visibility('public')
            ->maxSize(5120)->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp']);

        return $schema->statePath('data')->components([
            Section::make('Free Preview Experience · កាលកំណត់សាកល្បង')->columns(2)->schema([
                TextInput::make('trial_days')->label('Trial length (days)')->numeric()->integer()->minValue(1)->maxValue(30)->required()
                    ->helperText('For new invitations; extend one from Invitations.'),
                TextInput::make('trial_guest_cap')->label('Personal links during the trial')->numeric()->integer()->minValue(1)->required(),
            ]),
            Section::make('Payment & KHQR Configuration · គណនីបង់ប្រាក់ KHQR')->schema([
                Textarea::make('payment_instructions')->rows(4)->required()
                    ->helperText('Shown with the amount and the KW reference on the upgrade page.'),
                $qr('khqr_usd', 'Your KHQR (USD)'),
                $qr('khqr_khr', 'Your KHQR (KHR)'),
            ]),
            Section::make('Alerts & Telegram Notifications · ការជូនដំណឹង')->schema([
                TextInput::make('telegram_chat_id')->label('Telegram chat id')
                    ->helperText('Where new payment proofs are announced. The bot token stays in .env (TELEGRAM_BOT_TOKEN).'),
            ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([Actions::make([Action::make('save')->submit('save')->keyBindings(['mod+s'])])]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $values = [
            'trial_days' => (int) $data['trial_days'],
            'trial_guest_cap' => (int) $data['trial_guest_cap'],
            'payment_instructions' => $data['payment_instructions'],
            'payment_khqr' => ['USD' => $data['khqr_usd'] ?: null, 'KHR' => $data['khqr_khr'] ?: null],
            'telegram_chat_id' => filled($data['telegram_chat_id']) ? trim($data['telegram_chat_id']) : null,
        ];

        $changed = [];
        foreach ($values as $key => $value) {
            if (Setting::read($key) !== $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
                $changed[$key] = $value;
            }
        }

        if ($changed) {
            ActivityLog::record('settings.updated', null, $changed);
        }

        Notification::make()->success()->title('Settings saved')->send();
    }

    private static function isPath(?string $value): bool
    {
        return filled($value) && ! str_starts_with($value, 'http');
    }
}
