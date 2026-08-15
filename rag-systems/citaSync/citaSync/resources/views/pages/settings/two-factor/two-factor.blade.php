<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Two-factor authentication')] class extends Component {
    public bool $showModal = false;

    public string $code = '';

    /**
     * Si el usuario generó un secret pero nunca confirmó (abandonó el flujo),
     * lo limpiamos al volver a cargar el componente.
     */
    public function mount(DisableTwoFactorAuthentication $disable): void
    {
        $user = Auth::user();

        if ($user->two_factor_secret && !$user->two_factor_confirmed_at) {
            $disable($user);
        }
    }

    #[Computed]
    public function twoFactorEnabled(): bool
    {
        return (bool) Auth::user()->two_factor_confirmed_at;
    }

    #[Computed]
    public function qrCodeSvg(): ?string
    {
        return Auth::user()->two_factor_secret ? Auth::user()->twoFactorQrCodeSvg() : null;
    }

    #[Computed]
    public function recoveryCodes(): array
    {
        return Auth::user()->two_factor_recovery_codes ? json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true) : [];
    }

    public function enableTwoFactorAuthentication(EnableTwoFactorAuthentication $enable): void
    {
        $enable(Auth::user());

        $this->showModal = true;
    }

    public function confirmTwoFactorAuthentication(ConfirmTwoFactorAuthentication $confirm): void
    {
        $this->validate([
            'code' => ['required', 'string'],
        ]);

        $confirm(Auth::user(), $this->code);

        $this->showModal = false;
        $this->code = '';

        unset($this->qrCodeSvg, $this->recoveryCodes);
    }

    public function disableTwoFactorAuthentication(DisableTwoFactorAuthentication $disable): void
    {
        $disable(Auth::user());

        $this->showModal = false;
    }

    public function regenerateRecoveryCodes(GenerateNewRecoveryCodes $generate): void
    {
        $generate(Auth::user());

        unset($this->recoveryCodes);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Two-factor authentication') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Two-factor authentication')" :subheading="__('Add additional security to your account using two-factor authentication')">

        <div class="my-6 w-full space-y-6">
            @if ($this->twoFactorEnabled)
                <flux:text class="!text-green-600 dark:!text-green-400">
                    {{ __('Two-factor authentication is enabled.') }}
                </flux:text>

                <div class="flex items-center gap-3">
                    <flux:button wire:click="regenerateRecoveryCodes" variant="filled"
                        data-test="regenerate-recovery-codes-button">
                        {{ __('Regenerate recovery codes') }}
                    </flux:button>

                    <flux:button wire:click="disableTwoFactorAuthentication" variant="danger"
                        data-test="disable-two-factor-button">
                        {{ __('Disable') }}
                    </flux:button>
                </div>

                @if (count($this->recoveryCodes))
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <flux:text class="mb-2 font-medium">{{ __('Recovery codes') }}</flux:text>
                        <div class="grid grid-cols-2 gap-1 font-mono text-sm">
                            @foreach ($this->recoveryCodes as $recoveryCode)
                                <span>{{ $recoveryCode }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <flux:text>
                    {{ __('You have not enabled two-factor authentication yet.') }}
                </flux:text>

                <flux:button wire:click="enableTwoFactorAuthentication" variant="primary"
                    data-test="enable-two-factor-button">
                    {{ __('Enable two-factor authentication') }}
                </flux:button>
            @endif
        </div>

        <flux:modal wire:model.self="showModal" name="confirm-two-factor" class="max-w-md">
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('Confirm two-factor authentication') }}</flux:heading>

                <flux:text>
                    {{ __('Scan the QR code below with your authenticator app, then enter the generated code to confirm.') }}
                </flux:text>

                @if ($this->qrCodeSvg)
                    <div class="rounded-lg bg-white p-4">
                        {!! $this->qrCodeSvg !!}
                    </div>
                @endif

                <form wire:submit="confirmTwoFactorAuthentication" class="space-y-4">
                    <flux:input wire:model="code" :label="__('Code')" type="text" inputmode="numeric" autofocus
                        required data-test="two-factor-code-input" />

                    <div class="flex justify-end gap-2">
                        <flux:button type="button" variant="filled" wire:click="disableTwoFactorAuthentication">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="submit" variant="primary" data-test="confirm-two-factor-button">
                            {{ __('Confirm') }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </x-pages::settings.layout>
</section>
