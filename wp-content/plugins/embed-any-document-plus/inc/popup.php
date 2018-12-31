<?php if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
    } 
?>
<div id="embed-popup-wrap">
    <div id="embed-popup">
        <button title="<?php esc_html_e( 'Close', 'embed-any-document-plus' ); ?>" type="button" class="ead-close">×</button>
        <div id="popup-header" class="ead-popup-header">
            <h1><?php esc_html_e( 'Add Document', 'embed-any-document-plus' );?></h1>
        </div>
        <div class="ead-section">
            <div id="embed-message" class="awsm-error" style="display:none;">
                <p></p>
            </div>
            <div class="ead-container">
                <form action="" onSubmit="return false" method="post" enctype="multipart/form-data" id="Docuploader">
                    <ul class="ead-options">
                        <li><a href="#" id="ead-upload"><span><img src="<?php echo $this->plugin_url;?>images/icon-upload.png" alt="Upload document" /><?php esc_html_e('Upload Document','embed-any-document-plus');?></span></a></li>
                        <li><a href="#" id="ead-doc-url"><span><img src="<?php echo $this->plugin_url;?>images/icon-link.png" alt="Add From URL" /><?php esc_html_e('Add from URL','embed-any-document-plus');?></span></a></li>
                        <li>
                            <?php echo $this->providerlink('ead_dropbox','ead-dropbox','Dropbox') ;?>
                        </li>
                        <li>
                            <?php echo $this->providerlink(array('ead_drivekey','ead_driveClient'),'ead-google','Drive') ;?>
                        </li>
                        <li>
                            <?php echo $this->providerlink('ead_box','ead-box','Box') ;?>
                        </li>
                    </ul>
                    <div class="box addurl-box">
                        <label for="awsm-url">
                            <?php esc_html_e( 'Enter document URL' ,'embed-any-document-plus' );?>
                        </label>
                        <input name="awsm-url" type="text" class="opt dwl input-group-text" id="awsm-url" placeholder="http://www.example.com/filename.pdf" />
                        <input type="button" value="Add URL" class="ead-btn button-primary input-group-btn" id="add-url" />
                        <div class="clear"></div>
                        <a href="#" class="go-back">&larr; <?php esc_html_e( 'back', 'embed-any-document-plus' );?></a>
                    </div>
                </form>
            </div>
            <!--ead-container-->
            <div class="upload-success">
                <div class="inner">
                    <div class="uploaded-doccument">
                        <p id="ead-filename"></p>
                        <span id="ead-filesize"></span>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="advanced-options">
                    <h3><?php esc_html_e('Advanced Options','embed-any-document-plus');?> <span><?php esc_html_e('(Leave blank for default settings)','embed-any-document-plus');?></span></h3>
                    <ul class="option-fields">
                        <li>
                            <div class="f-left">
                                <label>
                                    <?php esc_html_e('Width','embed-any-document-plus');?>
                                </label>
                                <input type="text" name="width" class="embedval embed-sanitize input-small" id="ead-width" data-setting="width" value="<?php echo get_option('ead_width', '100%' );?>">
                            </div>
                            <div class="f-left">
                                <label>
                                    <?php esc_html_e('Height','embed-any-document-plus');?>
                                </label>
                                <input type="text" name="height" class="embedval embed-sanitize input-small" id="ead-height" data-setting="height" value="<?php echo get_option('ead_height', '500px' );?>">
                            </div>
                            <div class="f-left" id="ead-downloadc">
                                <label>
                                    <?php esc_html_e('Show Download Link','embed-any-document-plus');?>
                                </label>
                                <?php 
                        $downoptions= array('all' => __('For all users','embed-any-document-plus'),'logged' => __('For Logged-in users','embed-any-document-plus'),'none' => __('No Download','embed-any-document-plus'));
                        $this->selectbuilder('ead-download', $downoptions,esc_attr( get_option('ead-download')),'ead-usc','data-setting="download"'); 
                        ?>
                            </div>
                            <div class="f-left" id="ead-download-text">
                                <label>
                                    <?php esc_html_e('Download Text','embed-any-document-plus');?>
                                </label>
                                <input type="text" name="text" class="embedval" id="ead-text" data-setting="text" value="<?php echo get_option('ead_text', 'Download' );?>">
                            </div>
                            <div class="f-left last" id="new-provider">
                                <label>
                                    <?php esc_html_e('Viewer','embed-any-document-plus');?>
                                </label>
                                <?php 
                        $providers = array('google' => __('Google Docs Viewer','embed-any-document-plus'),'microsoft' => __('Microsoft Office Online','embed-any-document-plus'));
                        $this->selectbuilder('ead-provider', $providers,esc_attr( get_option('ead_provider','google')),'ead-usc','data-setting="viewer"'); 
                        ?>
                            </div>
                            <div class="f-left" id="ead-pseudo" style="display:none">
                                <label>
                                    <?php esc_html_e('Viewer','embed-any-document-plus');?>
                                </label>
                                <select name="ead-pseudo" disabled>
                                    <option value="box">
                                        <?php esc_html_e('Box','embed-any-document-plus');?>
                                    </option>
                                    <option value="drive">
                                        <?php esc_html_e('Drive','embed-any-document-plus');?>
                                    </option>
                                </select>
                            </div>
                            <div class="f-left last" id="ead-boxtheme" style="display:none">
                                <label>
                                    <?php esc_html_e('Theme','embed-any-document-plus');?>
                                </label>
                                <?php 
                        $themes = array('dark' => __('Dark','embed-any-document-plus'),'light' => __('Light','embed-any-document-plus'));
                        $this->selectbuilder('ead-boxtype', $themes,'dark','ead-boxtheme'); 
                        ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li class="checkbox" id="eadcachemain">
                            <input type="checkbox" id="ead-cache" class="ead-usc" value="on">
                            <label for="ead-cache">
                                <?php esc_html_e('Do not cache this file (Affects performance)','embed-any-document-plus');?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <?php esc_html_e('Shortcode Preview', 'embed-any-document-plus'); ?>
                            </label>
                            <textarea name="shortcode" style="width:100%" id="shortcode" readonly="readonly"></textarea>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mceActionPanel ead-action-panel">
            <div style="float: right">
                <input type="button" id="insert-doc" name="insert" data-txt="<?php esc_html_e('Insert', 'embed-any-document-plus'); ?>" data-loading="<?php esc_html_e('Loading...', 'embed-any-document-plus'); ?>" class="ead-btn button button-primary button-medium" value="<?php esc_html_e('Insert', 'embed-any-document-plus'); ?>" disabled/>
            </div>
            <div style="float: left">
                <input type="button" name="cancel" class="ead-btn button cancel-embed button-medium" value="<?php esc_html_e('Cancel', 'embed-any-document-plus'); ?>" />
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>