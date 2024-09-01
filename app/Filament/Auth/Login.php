<?php

namespace App\Filament\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as AuthLogin;

class Login extends AuthLogin
{
    public function mount(): void
    {
        parent::mount();

        if (app()->environment('local')) {
            $this->form->fill([
                'login' => 'putri',
                'password' => 'password',
                'remember' => true
            ]);
        }
    }

    /* The commented line `//
    https://laraveldaily.com/post/filament-3-login-with-name-username-or-email` is providing a
    reference or documentation link to a blog post on laraveldaily.com. This link likely contains
    additional information or a tutorial related to implementing login functionality in Filament
    using name, username, or email as login credentials. It can be helpful for understanding the
    context or implementation details related to the login functionality in the code snippet. */
    // https://laraveldaily.com/post/filament-3-login-with-name-username-or-email

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // $this->getEmailFormComponent(),
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Login')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where('email', $data['login'])->first();
        if($user){
            $user->last_login = now();
            $user->save();
        }
        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (!Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            Notification::make()
                ->title('Login Gagal!')
                ->danger()
                ->body('Username/Password Salah!')
                ->icon('heroicon-m-x-circle')
                ->send();
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();
        if($user){
            Notification::make()
                ->title('Login Berhasil!')
                ->success()
                ->body('Welcome back 🚀 ')
                ->icon('heroicon-m-shield-check')
                ->send();
        }

        if (
            ($user instanceof FilamentUser) &&
            (!$user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
