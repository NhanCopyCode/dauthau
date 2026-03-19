<!-- Main Footer -->
<!-- Main Footer -->
<footer class="main-footer  
">
    <!-- To the right -->
    <div class="float-right hidden-xs">
        <img src="{{ (\DB::table('settings')->where('key', 'company_logo')->value('value') != null) ? url(\DB::table('settings')->where('key', 'company_logo')->value('value')) : asset('images/logoFB.png') }}" alt="" height="40" width="100">
    </div>
    <!-- Default to the left -->
    Copyright &copy; 2023 <i class="fab fa-laravel text-red"></i>
</footer>
