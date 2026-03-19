<script type="text/javascript">
    const route_prefix = "{{ url("/laravel-filemanager") }}";
    const ckeditor_options = {
        language: 'vi',
        skin: 'bootstrapck',
        extraPlugins: 'justify',
        filebrowserImageBrowseUrl: route_prefix + '?type=Images',
        filebrowserImageUploadUrl: route_prefix + '/upload?type=Images&_token={{ csrf_token() }}',
        filebrowserBrowseUrl: route_prefix + '?type=Files',
        filebrowserUploadUrl: route_prefix + '/upload?type=Files&_token={{ csrf_token() }}'
    };
</script>