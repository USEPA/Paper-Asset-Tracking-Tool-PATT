(function (blocks, editor, components, i18n, element) {

var el = wp.element.createElement;
var withSelect = wp.data.withSelect;
var registerBlockType = wp.blocks.registerBlockType;
var RichText = wp.editor.RichText;
var BlockControls = wp.editor.BlockControls;
var AlignmentToolbar = wp.editor.AlignmentToolbar;
var MediaUpload = wp.editor.MediaUpload;
var InspectorControls = wp.editor.InspectorControls;
var TextControl = components.TextControl;
var SelectControl = components.SelectControl;
var RadioControl = components.RadioControl;
var ServerSideRender = wp.components.ServerSideRender;
var blockStyle = { backgroundColor: '#900', color: '#fff', padding: '20px' };

registerBlockType( 'gutenberg/mdocs-add-file-block', {
    title: 'mDocs File 2',
    icon: 'megaphone',
    category: 'common',
	attributes: { 
		file_list: {
			type: 'string',
		},
		files: {
			type: 'string',
			default: '25975',
		},
	},
    edit: withSelect( function( select ) {
        return {
            posts: select( 'core' ).getEntityRecords( 'postType', 'post' )
        };
    } )(  function( props ) {
		var file_list = props.attributes.file_list;
		var files = props.attributes.files;
		function on_file_select( selected_file ) { props.setAttributes( { files: selected_file } ); }
        return [
			el('div', { className: props.className },
				el('div', { className: 'test-class' },
					el(RichText, {
						tagName: 'h2',
						style: blockStyle,
						//onChange: onChangeContent,
						value: files,
					})
				)
			),
			el(InspectorControls, { key: 'inspector' },
				el('p', {}, i18n.__('Select the file you want to insert.')),
				
				el(ServerSideRender, {
					block: "gutenberg/mdocs-gutenberg-get-files",
					attributes:  props.attributes,
					//value: files,
					onChange: on_file_select,
				})
				
				
				/*
				el(
					RadioControl, {
						type: 'select',
						label: i18n.__( 'mDocs Files' ),
						onChange: on_file_select,
						selected: files,
						options: [
						  { value: '25975', label: i18n.__( 'File One' ) },
						  { value: '25919', label: i18n.__( 'File Two' ) },
						],
					}
				)
				el(
					SelectControl, {
						type: 'select',
						label: i18n.__( 'mDocs Files' ),
						onChange: on_file_select,
						value: files,
						options: [
						  { value: '25975', label: i18n.__( 'File One' ) },
						  { value: '25919', label: i18n.__( 'File Two' ) },
						],
					}
				)
				*/
			)
        ]
    }),

    save: function() {
        // Rendering in PHP
        return null;
    },
} );

})(
window.wp.blocks,
window.wp.editor,
window.wp.components,
window.wp.i18n,
window.wp.element
)









/*
registerBlockType('gutenberg/mdocs-add-file-block', { 
	title: i18n.__('mDocs File'), 
	description: i18n.__('Allows you to add a Memphis Documents Library file to a post or page.'),
	icon: 'nametag',
	category: 'common',
	attributes: { 
		file_list: {
			type: 'radio',
			default: '25377',
		},
		content: {
			type: 'string',
            source: 'html',
            selector: 'p',
        }
	},
	edit: function (props) {
		console.debug(props);
		var attributes = props.attributes
		var file_list = props.attributes.file_list;
		function on_file_select( selected_file ) {
			props.setAttributes( { file_list: selected_file } );
		}
		function onChangeContent( newContent ) {
			props.setAttributes( { content: newContent } );
        }
		
		return [
			el('div', { className: props.className, style: blockStyle },
				el('div', { className: 'test-class' },
					el(RichText, {
						tagName: 'p',
						style: blockStyle,
						onChange: onChangeContent,
						value: '[mdocs single-file="'+file_list+'"]',
					})
				)
			),
			el(InspectorControls, { key: 'inspector' }, 
				el('p', {}, i18n.__('Select the file you want to insert.')),
				el(
					RadioControl, {
						type: 'number',
						label: i18n.__( 'mDocs Files' ),
						value: file_list,
						onChange: on_file_select,
						selected: file_list,
						options: [
						  { value: '25377', label: i18n.__( 'File One' ) },
						  { value: '25391', label: i18n.__( 'File Two' ) },
						],
					}
				)
			)
		]
	},
	save: function (props) {
		var content = props.attributes.content;
		var file_list =  props.attributes.file_list;
        return el( RichText.Content, {
            tagName: 'div',
            className: props.className,
            value: '[mdocs single-file="'+file_list+'"]',
        } );
	}
})

})(
window.wp.blocks,
window.wp.editor,
window.wp.components,
window.wp.i18n,
window.wp.element
)
*/