/**
 * 全部
 * @param config
 */
CKEDITOR.editorConfig = function( config ) {
    //config.uiColor = '#ffffff';
    config.removePlugins = 'elementspath,resize';
    config.image_previewText='图片预览区...'; //预览区域显示内容
    config.toolbarGroups = [
        { name: 'forms', groups: [ 'forms' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'align', 'list', 'indent', 'blocks', 'bidi', 'paragraph' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'insert', groups: [ 'insert' ] },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'document', groups: [ 'document', 'doctools', 'mode' ] },
        { name: 'others', groups: [ 'others' ] },
        { name: 'about', groups: [ 'about' ] }
    ];

    config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CopyFormatting,RemoveFormat,Outdent,Indent,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,SpecialChar,Iframe,ShowBlocks,About,Smiley,Font,Styles,HorizontalRule';
};

