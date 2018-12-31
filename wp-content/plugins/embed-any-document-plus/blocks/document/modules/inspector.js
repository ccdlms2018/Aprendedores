import EadHelper from './helper';

const { __ } = wp.i18n;
const { Component } = wp.element;
const { InspectorControls } = wp.editor;
const {
    PanelBody,
    TextControl,
    SelectControl,
    ToggleControl,
    RadioControl,
    Disabled
} = wp.components;

class EadInspector extends Component {
    constructor() {
        super(...arguments);
        this.downloadControlhandle = this.downloadControlhandle.bind(this);
        this.viewerControlHandle = this.viewerControlHandle.bind(this);
        const { attributes: { download, viewer } } = this.props;
        this.state = {
            downloadDisabled: ( download === 'none' ) ? true : false,
            cacheHidden: ( viewer === 'google' ) ? false : true
        };
    }

    downloadControlhandle(download) {
        this.setState( { downloadDisabled: ( download === 'none' ) ? true : false } );
        this.props.setAttributes( { download } );
    }

    viewerControlHandle(viewer) {
        this.setState( { cacheHidden: ( viewer === 'google' ) ? false : true } );
        this.props.setAttributes( { viewer } );
    }

    render() {
        const { attributes: { url, width, height, text, download, viewer, cache, boxtheme }, setAttributes } = this.props;
        let enableViewerControl = ( viewer === 'google' || viewer === 'microsoft' ) ? true : false;
        let viewerOptions = [{ value: 'google', label: __( 'Google Docs Viewer', 'embed-any-document-plus' ) }];
        if( EadHelper.isValidMSExtension(url) ) {
            viewerOptions.push({ value: 'microsoft', label: __( 'Microsoft Office Online', 'embed-any-document-plus' ) });
        }
        let downloadTextControl = <TextControl label={ __( 'Download Text', 'embed-any-document-plus' ) } help={ __( 'Default download button text', 'embed-any-document-plus' ) } value={ text } onChange={ text => setAttributes( { text } ) } />;
        if( this.state.downloadDisabled ) {
            downloadTextControl = <Disabled>{ downloadTextControl }</Disabled>;
        }

        return (
            <InspectorControls>
                <PanelBody>
                    <TextControl label={ __( 'Width', 'embed-any-document-plus' ) } help={ __( 'Width of document either in px or in %', 'embed-any-document-plus' ) } value={ width } onChange={ width => setAttributes( { width } ) } />
                </PanelBody>

                <PanelBody>
                    <TextControl label={ __( 'Height', 'embed-any-document-plus' ) } help={ __( 'Height of document either in px or in %', 'embed-any-document-plus' ) } value={ height } onChange={ height => setAttributes( { height } ) } />
                </PanelBody>

                { enableViewerControl && [
                    <PanelBody>
                        <SelectControl label={ __( 'Show Download Link', 'embed-any-document-plus' ) } options={[
                            { value: 'all', label: __( 'For all users', 'embed-any-document-plus' ) },
                            { value: 'logged', label: __( 'For Logged-in users', 'embed-any-document-plus' ) },
                            { value: 'none', label: __( 'No Download', 'embed-any-document-plus' ) }
                        ]} value={ download } onChange={ this.downloadControlhandle } />
                    </PanelBody>,
                    <PanelBody>{ downloadTextControl }</PanelBody>,
                    <PanelBody>
                        <SelectControl label={ __( 'Viewer', 'embed-any-document-plus' ) } options={ viewerOptions } value={ viewer } onChange={ this.viewerControlHandle } />
                    </PanelBody>
                ]}

                { ! this.state.cacheHidden && <PanelBody>
                    <ToggleControl label={ __( 'Cache', 'embed-any-document-plus' ) } checked={ cache } onChange={ cache => setAttributes( { cache } ) } />
                </PanelBody> }

                { viewer === 'box' && <PanelBody>
                    <RadioControl label={ __( 'Theme', 'embed-any-document-plus' ) } help={ __( 'Choose a theme for Box.com embeds', 'embed-any-document-plus' ) } selected={ boxtheme } options={[
                        { value: 'dark', label: __( 'Dark', 'embed-any-document-plus' ) },
                        { value: 'light', label: __( 'Light', 'embed-any-document-plus' ) }
                    ]} onChange={ boxtheme => setAttributes( { boxtheme } ) } />
                </PanelBody> }
            </InspectorControls>
        );
    }
}

export default EadInspector;