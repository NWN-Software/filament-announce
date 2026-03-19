@php
    use Filament\Support\Enums\Alignment;

    $titleAlignment = $notification->getTitleAlignment();
    $bodyAlignment = $notification->getBodyAlignment();
    $title = $notification->getTitle();
    $body = $notification->getBody();
    $actions = $notification->getActions();
    $color = $notification->getColor();

    if (!$titleAlignment instanceof Alignment) {
        $titleAlignment = filled($titleAlignment) ? Alignment::tryFrom($titleAlignment) ?? $titleAlignment : null;
    }

    if (!$bodyAlignment instanceof Alignment) {
        $bodyAlignment = filled($bodyAlignment) ? Alignment::tryFrom($bodyAlignment) ?? $bodyAlignment : null;
    }

    $colorClasses = \Illuminate\Support\Arr::toCssClasses([
        'flex items-center border border-transparent px-6 py-2 gap-4',
        'text-gray-950 dark:text-white' => $color === 'gray',
        'text-white' => $color !== 'gray',
    ]);

    if (is_string($color)) {
        $colorStyles = 'background-color: '.$color.';';
    } elseif (is_array($color) && isset($color[500])) {
        $colorStyles = 'background-color: rgb('.$color[500].');';
    } else {
        $colorStyles = 'background-color: oklch(0.769 0.188 70.08);';
    }
@endphp

<div class="{{ $colorClasses }}" style="{{ $colorStyles }}">
    @if ($icon = $notification->getIcon())
        <div class="flex items-center">
            <x-filament::icon icon="{{ $icon }}" class="h-6 w-6" />
        </div>
    @endif
    <div @class(['w-full flex-1'])>
        @if ($title && !$body && $actions)
            <div @class([
                'flex flex-row flex-wrap items-center gap-4 leading-none',
                match ($titleAlignment) {
                    Alignment::Start, Alignment::Left => 'justify-start',
                    Alignment::Center => 'justify-center',
                    Alignment::End, Alignment::Right => 'justify-end',
                    Alignment::Between, Alignment::Justify => 'justify-between',
                    default => $titleAlignment,
                },
            ])>
                <h5 class="font-semibold">{{ $title }}</h5>

                @foreach ($actions ?? [] as $action)
                    {{ $action }}
                @endforeach
            </div>
        @elseif (!$title && $body && $actions)
            <div @class([
                'flex flex-row flex-wrap items-center gap-4 leading-none',
                match ($bodyAlignment) {
                    Alignment::Start, Alignment::Left => 'justify-start',
                    Alignment::Center => 'justify-center',
                    Alignment::End, Alignment::Right => 'justify-end',
                    Alignment::Between, Alignment::Justify => 'justify-between',
                    default => $bodyAlignment,
                },
            ])>
                <span class="text-sm">{{ $body }}</span>

                @foreach ($actions ?? [] as $action)
                    {{ $action }}
                @endforeach
            </div>
        @else
            <div @class([
                match ($titleAlignment) {
                    Alignment::Start => 'text-start',
                    Alignment::Center => 'text-center',
                    Alignment::End => 'text-end',
                    Alignment::Left => 'text-left',
                    Alignment::Right => 'text-right',
                    Alignment::Justify, Alignment::Between => 'text-justify',
                    default => $titleAlignment,
                },
            ])>
                <h5 class="font-semibold">{{ $title }}</h5>
            </div>
            <div @class([
                'flex flex-row flex-wrap items-center gap-4 leading-none',
                match ($bodyAlignment) {
                    Alignment::Start, Alignment::Left => 'justify-start',
                    Alignment::Center => 'justify-center',
                    Alignment::End, Alignment::Right => 'justify-end',
                    Alignment::Between, Alignment::Justify => 'justify-between',
                    default => $bodyAlignment,
                },
            ])>
                <span class="text-sm">{{ $body }}</span>

                @if ($actions)
                    @foreach ($actions ?? [] as $action)
                        {{ $action }}
                    @endforeach
                @endif
            </div>
        @endif
    </div>


    @if ($notification->isClosable())
        <div class="flex items-center">
            <x-filament::icon-button icon="heroicon-o-x-mark" color="white"
                x-on:click="$dispatch('markedAnnouncementAsRead', {id: '{{ $notification->getId() }}'})" />
        </div>
    @endif
</div>
