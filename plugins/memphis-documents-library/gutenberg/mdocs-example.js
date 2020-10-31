(function (blocks, editor, components, i18n, element) {
  var el = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var RichText = wp.editor.RichText;
  var BlockControls = wp.editor.BlockControls;
  var AlignmentToolbar = wp.editor.AlignmentToolbar;
  var MediaUpload = wp.editor.MediaUpload;
  var InspectorControls = wp.editor.InspectorControls;
  var TextControl = components.TextControl;
  var SelectControl = components.SelectControl;
  var RadioControl = components.RadioControl;

  registerBlockType('gutenberg/mdocs-add-file-block', { 
    title: i18n.__('mDocs File'), 
    description: i18n.__('A custom block for displaying personal profiles.'), // The description of our block.
    icon: 'nametag', // Dashicon icon for our block. Custom icons can be added using inline SVGs.
    category: 'common', // The category of the block.
    attributes: { // Necessary for saving block content.
      title: {
        type: 'array',
        source: 'children',
        selector: 'h3'
      },
      subtitle: {
        type: 'array',
        source: 'children',
        selector: 'h5'
      },
      bio: {
        type: 'array',
        source: 'children',
        selector: 'p'
      },
      mediaID: {
        type: 'number'
      },
      mediaURL: {
        type: 'string',
        source: 'attribute',
        selector: 'img',
        attribute: 'src'
      },
      alignment: {
        type: 'string',
        default: 'center'
      },
      facebookURL: {
        type: 'url'
      },
      twitterURL: {
        type: 'url'
      },
      instagramURL: {
        type: 'url'
      },
      linkedURL: {
        type: 'url'
      },
      emailAddress: {
        type: 'text'
      },
	columns: {
		type: 'select',
		default: '2'
	},
	files: {
		type: 'radio',
		default: '2',
	},
    },
	

	edit: function (props) {
		console.debug(props.attributes);
		var attributes = props.attributes
		var alignment = props.attributes.alignment
		var facebookURL = props.attributes.facebookURL
		var twitterURL = props.attributes.twitterURL
		var instagramURL = props.attributes.instagramURL
		var linkedURL = props.attributes.linkedURL
		var emailAddress = props.attributes.emailAddress
		var columns = props.attributes.columns;
		var files = props.attributes.files;
		var onSelectImage = function (media) {
		  return props.setAttributes({
			mediaURL: media.url,
			mediaID: media.id
		  })
		}

      function onChangeAlignment (newAlignment) {
        props.setAttributes({ alignment: newAlignment })
      }
	  
	  function onChangeCols( newColumns ) {
				props.setAttributes( { columns: newColumns } );
			}
		 function onChangeFiles( newFile ) {
				props.setAttributes( { files: newFile } );
			}

      return [
        el(BlockControls, { key: 'controls' }, // Display controls when the block is clicked on.
          el('div', { className: 'components-toolbar' },
            el(MediaUpload, {
              onSelect: onSelectImage,
              type: 'image',
              render: function (obj) {
                return el(components.Button, {
                  className: 'components-icon-button components-toolbar__control',
                  onClick: obj.open
                },
                // Add Dashicon for media upload button.
                el('svg', { className: 'dashicon dashicons-edit', width: '20', height: '20' },
                  el('path', { d: 'M2.25 1h15.5c.69 0 1.25.56 1.25 1.25v15.5c0 .69-.56 1.25-1.25 1.25H2.25C1.56 19 1 18.44 1 17.75V2.25C1 1.56 1.56 1 2.25 1zM17 17V3H3v14h14zM10 6c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2zm3 5s0-6 3-6v10c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1V8c2 0 3 4 3 4s1-3 3-3 3 2 3 2z' })
                ))
              }
            })
          ),
          // Display alignment toolbar within block controls.
          el(AlignmentToolbar, {
            value: alignment,
            onChange: onChangeAlignment
          })
        ),
        el(InspectorControls, { key: 'inspector' }, // Display the block options in the inspector panel.
          el(components.PanelBody, {
            title: i18n.__('Social Media Links'),
            className: 'block-social-links',
            initialOpen: true
          },
          el('p', {}, i18n.__('Add links to your social media profiles.')),
          
		  
		  
		  el(
						RadioControl,
						{
							type: 'number',
							label: i18n.__( 'Test of Radio' ),
							value: files,
							onChange: onChangeFiles,
							selected: files,
							options: [
							  { value: '1', label: i18n.__( 'File One' ) },
							  { value: '2', label: i18n.__( 'File Two' ) },
							],
						}
					),
		  
		  el(
						SelectControl,
						{
							type: 'number',
							label: i18n.__( 'Number of Columns' ),
							value: columns,
							onChange: onChangeCols,
							options: [
							  { value: '1', label: i18n.__( 'One column' ) },
							  { value: '2', label: i18n.__( 'Two columns' ) },
							],
						}
					),
		  
		  
		  
		  
		  // Facebook social media text field option.
          el(TextControl, {
            type: 'url',
            label: i18n.__('Facebook URL'),
            value: facebookURL,
            onChange: function (newFacebook) {
              props.setAttributes({ facebookURL: newFacebook })
            }
          }),
          // Twitter social media text field option.
          el(TextControl, {
            type: 'url',
            label: i18n.__('Twitter URL'),
            value: twitterURL,
            onChange: function (newTwitter) {
              props.setAttributes({ twitterURL: newTwitter })
            }
          }),
          // Instagram social media text field option.
          el(TextControl, {
            type: 'url',
            label: i18n.__('Instagram URL'),
            value: instagramURL,
            onChange: function (newInstagram) {
              props.setAttributes({ instagramURL: newInstagram })
            }
          }),
          // LinkedIn social media text field option.
          el(TextControl, {
            type: 'url',
            label: i18n.__('LinkedIn URL'),
            value: linkedURL,
            onChange: function (newLinkedIn) {
              props.setAttributes({ linkedURL: newLinkedIn })
            }
          }),
          // Email address text field option.
          el(TextControl, {
            type: 'text',
            label: i18n.__('Email Address'),
            value: emailAddress,
            onChange: function (newEmail) {
              props.setAttributes({ emailAddress: newEmail })
            }
          }))
        ),
        el('div', { className: props.className },
          el('div', {
            className: attributes.mediaID ? 'organic-profile-image image-active' : 'organic-profile-image image-inactive',
            style: attributes.mediaID ? { backgroundImage: 'url(' + attributes.mediaURL + ')' } : {}
          },
          el(MediaUpload, {
            onSelect: onSelectImage,
            type: 'image',
            value: attributes.mediaID,
            render: function (obj) {
              return el(components.Button, {
                className: attributes.mediaID ? 'image-button' : 'button button-large',
                onClick: obj.open
              },
              !attributes.mediaID ? i18n.__('Upload Image') : el('img', { src: attributes.mediaURL })
              )
            }
          })
          ),
          el('div', { className: 'organic-profile-content', style: { textAlign: alignment } },
            el(RichText, {
              key: 'editable',
              tagName: 'h3',
              placeholder: 'Profile Name',
              keepPlaceholderOnFocus: true,
              value: attributes.title,
              onChange: function (newTitle) {
                props.setAttributes({ title: newTitle })
              }
            }),
            el(RichText, {
              tagName: 'h5',
              placeholder: i18n.__('Subtitle'),
              keepPlaceholderOnFocus: true,
              value: attributes.subtitle,
              onChange: function (newSubtitle) {
                props.setAttributes({ subtitle: newSubtitle })
              }
            }),
            el(RichText, {
              key: 'editable',
              tagName: 'p',
              placeholder: i18n.__('Write a brief bio...'),
              keepPlaceholderOnFocus: true,
              value: attributes.bio,
              onChange: function (newBio) {
                props.setAttributes({ bio: newBio })
              }
            }),
            el('div', { className: 'organic-profile-social' },
              attributes.facebookURL && el('a', {
                className: 'social-link',
                href: attributes.facebookURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-facebook' })
              ),
              attributes.twitterURL && el('a', {
                className: 'social-link',
                href: attributes.twitterURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-twitter' })
              ),
              attributes.instagramURL && el('a', {
                className: 'social-link',
                href: attributes.instagramURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-instagram' })
              ),
              attributes.linkedURL && el('a', { className: 'social-link',
                href: attributes.linkedURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-linkedin' })
              ),
              attributes.emailAddress && el('a', {
                className: 'social-link',
                href: 'mailto:' + attributes.emailAddress,
                target: '_blank'
              },
              el('i', { className: 'fa fa-envelope' })
              )
            )
          )
        )
      ]
    },

    save: function (props) {
      var attributes = props.attributes
      var alignment = props.attributes.alignment
      var facebookURL = props.attributes.facebookURL
      var twitterURL = props.attributes.twitterURL
      var instagramURL = props.attributes.instagramURL
      var linkedURL = props.attributes.linkedURL
      var emailAddress = props.attributes.emailAddress
      return (
        el('div', { className: props.className },
          el('div', { className: 'organic-profile-image', style: { backgroundImage: 'url(' + attributes.mediaURL + ')' } },
            el('img', { src: attributes.mediaURL })
          ),
          el('div', { className: 'organic-profile-content', style: { textAlign: attributes.alignment } },
            el(RichText.Content, {
              tagName: 'h3',
              value: attributes.title
            }),
            el(RichText.Content, {
              tagName: 'h5',
              value: attributes.subtitle
            }),
            el(RichText.Content, {
              tagName: 'p',
              value: attributes.bio
            }),
            el('div', { className: 'organic-profile-social' },
              attributes.facebookURL && el('a', {
                className: 'social-link',
                href: attributes.facebookURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-facebook' })
              ),
              attributes.twitterURL && el('a', {
                className: 'social-link',
                href: attributes.twitterURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-twitter' })
              ),
              attributes.instagramURL && el('a', {
                className: 'social-link',
                href: attributes.instagramURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-instagram' })
              ),
              attributes.linkedURL && el('a', {
                className: 'social-link',
                href: attributes.linkedURL,
                target: '_blank'
              },
              el('i', { className: 'fab fa-linkedin' })
              ),
              attributes.emailAddress && el('a', {
                className: 'social-link',
                href: 'mailto:' + attributes.emailAddress,
                target: '_blank'
              },
              el('i', { className: 'fa fa-envelope' })
              )
            )
          )
        )
      )
    }
  })

})(
  window.wp.blocks,
  window.wp.editor,
  window.wp.components,
  window.wp.i18n,
  window.wp.element
)












(function (blocks, editor, components, i18n, element) {
var el = wp.element.createElement;
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


