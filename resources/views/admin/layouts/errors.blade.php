<!DOCTYPE html>
<html>

@include('admin.layouts.partials.htmlheader')

<body>
    <div id="app" v-cloak>
        <!-- Main content -->
        <section class="content">
            <!-- Your Page Content Here -->
            @yield('main-content')
        </section>
    </div>
    @section('scripts')
        @include('admin.layouts.partials.scripts')
    @show
</body>

</html>
