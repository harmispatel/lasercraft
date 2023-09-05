@php
    $current_year = \Carbon\Carbon::now()->format('Y');
    $client_settings = getClientSettings();
    $copyright_text = isset($client_settings['homepage_intro']) ? $client_settings['homepage_intro'] : '';
    $copyright_text = str_replace('[year]',$current_year,$copyright_text);
@endphp

<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
    <div class="copyright">
        {!! $copyright_text !!}
    </div>
</footer>
<!-- End Footer -->
