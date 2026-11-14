@php
    // Correspondance couleur -> code hexadécimal pour l'aperçu visuel des pastilles
    $colorMap = [
        'noir' => '#1a1a1a', 'blanc' => '#ffffff', 'marron' => '#6b4226', 'camel' => '#c19a6b',
        'beige' => '#e8dcc8', 'gris' => '#8a8a8a', 'rouge' => '#b1372f', 'bleu' => '#2c4a7c',
        'vert' => '#3d6b4a', 'jaune' => '#d9b338', 'rose' => '#d98ba3', 'orange' => '#d9772f',
        'doré' => '#c9a227', 'or' => '#c9a227', 'argenté' => '#b0b0b0', 'bordeaux' => '#6b1f2a',
        'violet' => '#6b4a8a', 'mauve' => '#9b6b9e', 'fuchsia' => '#c21878', 'magenta' => '#c21878',
        'rose fuchsia' => '#c21878', 'rose clair' => '#efb6c8', 'bleu marine' => '#17345c',
        'bleu ciel' => '#78b8e8', 'turquoise' => '#40b8b0', 'vert olive' => '#78823d',
        'marron clair' => '#a87955', 'marron foncé' => '#50301f', 'champagne' => '#e8d6b0',
        'kaki' => '#7a7a4a', 'tan' => '#c19a6b',
    ];
    $uid = $product->id.'-'.uniqid();
@endphp

@if (!empty($product->available_sizes) || !empty($product->available_colors))
    <div class="variant-picker">
        @if (!empty($product->available_sizes))
            <div class="variant-group">
                <span class="variant-label">Taille</span>
                <div class="variant-options">
                    @foreach ($product->available_sizes as $i => $size)
                        <input type="radio" name="size" id="size-{{ $uid }}-{{ $i }}" value="{{ $size }}" {{ $i === 0 ? 'checked' : '' }} required>
                        <label for="size-{{ $uid }}-{{ $i }}">{{ $size }}</label>
                    @endforeach
                </div>
            </div>
        @endif

        @if (!empty($product->available_colors))
            <div class="variant-group">
                <span class="variant-label">Couleur</span>
                <div class="variant-options color-options">
                    @foreach ($product->available_colors as $i => $color)
                        @php $hex = $colorMap[strtolower($color)] ?? '#cccccc'; @endphp
                        <input type="radio" name="color" id="color-{{ $uid }}-{{ $i }}" value="{{ $color }}" aria-label="{{ $color }}" {{ $i === 0 ? 'checked' : '' }} required>
                        <label for="color-{{ $uid }}-{{ $i }}" title="{{ $color }}" aria-label="{{ $color }}">
                            <span class="swatch" style="background:{{ $hex }};"></span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

<style>
    .variant-options { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .color-options { gap: 12px; }
    .color-options input[type="radio"] { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
    .color-options label { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: 2px solid transparent; border-radius: 50%; cursor: pointer; }
    .color-options .swatch { display: block; width: 22px; height: 22px; border: 1px solid rgba(34, 31, 29, .24); border-radius: 50%; }
    .color-options input[type="radio"]:checked + label { border-color: var(--accent, #b1372f); }
    .color-options input[type="radio"]:focus-visible + label { outline: 2px solid var(--accent, #b1372f); outline-offset: 3px; }
</style>
