@extends("home.include.mother")

@section("content")
    <script src="https://cdn.ckeditor.com/ckeditor5/11.1.1/classic/ckeditor.js"></script>

    <textarea name="content" id="editor">
        &lt;p&gt;This is some sample content.&lt;/p&gt;
    </textarea>
    <script>
        ClassicEditor
            .create( document.querySelector( '#editor' ) , {

        })
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor.builtinPlugins.map( plugin => plugin.pluginName );
    </script>
@endsection
