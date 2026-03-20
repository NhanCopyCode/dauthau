<!DOCTYPE html>
<html lang="en">

@section('htmlheader')
    @include('admin.layouts.partials.htmlheader')
@show

@yield('style')

<body class="skin-blue-light sidebar-mini">
    <div id="app" v-cloak>
        <div class="wrapper">

            @include('admin.layouts.partials.mainheader')

            @include('admin.layouts.partials.sidebar')

            <div class="content-wrapper">
                <section class="content">
                    @yield('main-content')
                </section>
            </div>

            @include('admin.layouts.partials.controlsidebar')

            @include('admin.layouts.partials.footer')

        </div>
    </div>

    @section('scripts')
        @include('admin.layouts.partials.scripts')
    @show

    @yield('scripts-footer')
    @if (session('success'))
        <script>
            toastr.success("{{ session('success') }}");
        </script>
    @endif
</body>

</html>
