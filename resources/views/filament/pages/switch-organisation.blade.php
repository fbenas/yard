<x-filament-panels::page>
    <div class="space-y-4">
        @foreach ($organisations as $organisation)
            @php

                $organisationId = $organisation['id'] ?? null;
                $organisationName = $organisation['name'] ?? $organisationId;
                $isCurrent = $organisationId === $currentOrganisationId;
            @endphp

            <div class="rounded-xl border p-4 flex items-center justify-between">
                <div>
                    <div class="font-medium">{{ $organisationName }}</div>
                    <div class="text-sm text-gray-500">{{ $organisationId }}</div>
                </div>

                <div>
                    @if ($isCurrent)
                        <x-filament::badge color="success">
                            Current
                        </x-filament::badge>
                    @else
                        <x-filament::button
                            wire:click="switchOrganisation('{{ $organisationId }}')"
                        >
                            Switch
                        </x-filament::button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
