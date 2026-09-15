@php
    // Correspondance couleur -> code hexadécimal pour l'aperçu visuel des pastilles
    $colorMap = [
        'noir' => '#1a1a1a', 'blanc' => '#ffffff', 'marron' => '#6b4226', 'camel' => '#c19a6b',
        'beige' => '#e8dcc8', 'gris' => '#8a8a8a', 'rouge' => '#b1372f', 'bleu' => '#2c4a7c',
        'vert' => '#3d6b4a', 'jaune' => '#d9b338', 'rose' => '#d98ba3', 'orange' => '#d9772f',
        'doré' => '#c9a227', 'or' => '#c9a227', 'argenté' => '#b0b0b0', 'bordeaux' => '#6b1f2a',
        'violet' => '#6b4a8a', 'kaki' => '#7a7a4a', 'tan' => '#c19a6b',
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
                        <input type="radio" name="color" id="color-{{ $uid }}-{{ $i }}" value="{{ $color }}" {{ $i === 0 ? 'checked' : '' }} required>
                        <label for="color-{{ $uid }}-{{ $i }}" title="{{ $color }}">
                            <span class="swatch" style="background:{{ $hex }};"></span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif
