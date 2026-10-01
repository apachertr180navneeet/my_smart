<script>
    @if(isset($generalsetting) && isset($generalsetting->currency_symbol))
        window.currencySymbol = '{{ $generalsetting->currency_symbol }}';
    @endif
</script>
