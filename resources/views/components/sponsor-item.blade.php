
    <picture class="{{(isset($baseline) && $baseline == true) ? 'baseline' : ''}}">
        <source srcset="/img/sponsors/{{$id . ((isset($dark) && $dark == true) ? '-dark' : '')}}.webp" type="image/webp">
        <source srcset="/img/sponsors/{{$id . ((isset($dark) && $dark == true) ? '-dark' : '')}}.png" type="image/png">
        <img src="/img/sponsors/{{$id . ((isset($dark) && $dark == true) ? '-dark' : '')}}.png" alt="{{$name}}">
    </picture>