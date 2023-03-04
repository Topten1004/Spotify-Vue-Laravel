
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    @meta_tags
    <link rel="preload" href="{{ asset('css/app.css') }}" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" as="style">
    <link rel="preload" href="{{ asset('js/app.js') }}" as="script">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="shortcut icon" href="{{ asset('/storage/defaults/images/favicon.png') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('/storage/defaults/images/favicon.png') }}">
    <script src="https://unpkg.com/jwt-decode/build/jwt-decode.js"></script>
    <script>
      window.Settings = {!! $settings !!};
</script>
<script>
// Check that service workers are supported
if ('serviceWorker' in navigator) {
  // Use the window load event to keep the page load performant
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js');
  });
}
</script>
</head>
<body>
  <div class="loading-background-wrapper" id="spa-loading-page">
    <div class="loader">
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
          <span></span>
    </div>
</div>
<div id="app">
  <Master />
</div> 
<script src="https://accounts.google.com/gsi/client" async="" defer=""></script>
<div id="g_id_onload" data-client_id="660507553870-ttsrcn31o3m2fq9r8s4ov3csv7slgqr9.apps.googleusercontent.com" data-callback="onSignIn"></div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
<style>
  :root{
    --color-primary: {!! $themes['primary']  !!};
    --color-secondary: {!! $themes['secondary']  !!};
    
    --dark-theme-bg-color: {!! $themes['dark']['background']  !!};
    --dark-theme-text-color:{!! $themes['dark']['text']  !!};
    --dark-theme-panel-bg-color:{!! $themes['dark']['panel']  !!};
    
    --light-theme-bg-color: {!! $themes['light']['background']  !!};
    --light-theme-text-color:{!! $themes['light']['text']  !!};
    --light-theme-panel-bg-color:{!! $themes['light']['panel']  !!};

    --card-box-shadow: 0px 3px 1px -2px rgba(73, 73, 73, 0.2), 0px 2px 2px 0px rgba(78, 78, 78, 0.14), 0px 1px 5px 0px rgba(77, 77, 77, 0.12);
    }
    #spa-loading-page .loader {
        z-index: -1;
    }
</style>